<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentBooked;
use App\Mail\AppointmentBookedProvider;
use App\Models\Appointment;
use App\Models\ProviderProfile;
use App\Models\WorkingHour;
use App\Rules\DateValidationForBookingRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function index($id)
    {
        $provider = ProviderProfile::with('user')->findOrFail($id);

        return view('includes.booking', compact('provider'));
    }

    public function getBusinessHours($providerId)
    {

        $workingHours = WorkingHour::where('provider_id', $providerId)
            ->where('is_working_day', 1)
            ->get(['day', 'open_time', 'close_time']);

        $dayMap = [
            'Sunday' => 0,
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
        ];

        $businessHours = [];
        foreach ($workingHours as $hour) {
            $businessHours[] = [
                'daysOfWeek' => $dayMap[$hour->day],
                'startTime' => substr($hour->open_time, 0, 5),
                'endTime' => substr($hour->close_time, 0, 5),
            ];
        }

        return response()->json($businessHours, 200, [], JSON_PRETTY_PRINT);

    }

    public function store(Request $request, $id)
    {

        try {
            $validated = $request->validate([
                'service_name' => ['required', 'string', 'max:25'],
                'start_time' => ['required', 'date', new DateValidationForBookingRule],
                'end_time' => ['required', 'date', new DateValidationForBookingRule],
            ]);
            $start_time = Carbon::parse($validated['start_time']);
            $end_time = Carbon::parse($validated['end_time']);

            if ($end_time->lessThan($start_time)) {
                \Jeybin\Toastr\Toastr::error('End time must be after start time.')->toast();
                Log::warning('Invalid booking time: end before start', [
                    'user_id' => auth()->id(),
                    'start_time' => $start_time->toDateTimeString(),
                    'end_time' => $end_time->toDateTimeString(), ]);

                return redirect()->back();
            }

            // Csekkoljuk, hogy ne lehessen arra az időpontra (vagy intervallra foglalni) ami már foglalt
            $isBooked = Appointment::where('provider_id', $id) // Csak az adott provider foglalásait nézi
                ->where(function ($query) use ($start_time, $end_time) {
                    $query->whereBetween('start_time', [$start_time, $end_time])
                        ->orWhereBetween('end_time', [$start_time, $end_time])
                        ->orWhere(function ($query) use ($start_time, $end_time) {
                            $query->where('start_time', '<', $start_time)
                                ->where('end_time', '>', $end_time);
                        });
                })->exists();

            /* SQL EQUIVALENT */
            /*SELECT 1
                FROM appointments
                WHERE provider_id = 3
                AND (
                    (start_time BETWEEN '2025-05-01 10:00:00' AND '2025-05-01 11:00:00')
                    OR (end_time BETWEEN '2025-05-01 10:00:00' AND '2025-05-01 11:00:00')
                    OR (start_time < '2025-05-01 10:00:00' AND end_time > '2025-05-01 11:00:00')
                )
                LIMIT 1;
            */

            if ($isBooked) {
                \Jeybin\Toastr\Toastr::error('This appointment is already booked.')->timeOut(5000)->toast();
                Log::warning('Booking rejected: time slot already taken', [
                    'provider_id' => $id,
                    'user_id' => auth()->id(),
                    'start_time' => $start_time->toDateTimeString(),
                    'end_time' => $end_time->toDateTimeString(), ]);

                return redirect()->back();
            }

            $overlappingBooking = Appointment::where('user_id', auth()->id())
                ->where(function ($query) use ($start_time, $end_time) {
                    $query->whereBetween('start_time', [$start_time, $end_time])
                        ->orWhereBetween('end_time', [$start_time, $end_time])
                        ->orWhere(function ($query) use ($start_time, $end_time) {
                            $query->where('start_time', '<', $start_time)
                                ->where('end_time', '>', $end_time);
                        });
                })
                ->exists();
            if ($overlappingBooking) {
                \Jeybin\Toastr\Toastr::error('You already have a booking at this time!')->timeOut(5000)->toast();
                Log::warning('User tried to book an overlapping time slot', [
                    'user_id' => auth()->id(),
                    'start_time' => $start_time->toDateTimeString(),
                    'end_time' => $end_time->toDateTimeString(),
                    'overlapping_booking' => true,
                ]);

                return redirect()->back();
            }

            /* SELECT EXISTS(
                        SELECT 1
                        FROM bookings
                        WHERE user_id = 5
                        AND (
                                (start_time BETWEEN '2025-05-01 14:00:00' AND '2025-05-01 15:00:00')
                            OR (end_time BETWEEN '2025-05-01 14:00:00' AND '2025-05-01 15:00:00')
                            OR (start_time < '2025-05-01 14:00:00' AND end_time > '2025-05-01 15:00:00')
                        )
                    ) AS exists;
                */

            $appointment = Appointment::create([
                'user_id' => auth()->user()->id,
                'provider_id' => $id,
                'start_time' => $start_time,
                'end_time' => $end_time,
                'service_name' => $validated['service_name'],
                'status' => 'pending',
            ]);
            \Jeybin\Toastr\Toastr::success('Appointment created successfully')->toast();
            Log::info('Appointment booked successfully', [
                'user_id' => auth()->id(),
                'provider_id' => $id,
                'service_name' => $validated['service_name'],
                'start_time' => $appointment->start_time->toDateTimeString(),
                'end_time' => $appointment->end_time->toDateTimeString(),
                'booking_created_at' => now()->toDateTimeString(),
            ]);
            $providerprofile = ProviderProfile::where('id', $id)->firstOrFail();
            $providerUser = $providerprofile->user;

            Mail::to(auth()->user()->email)->send(new AppointmentBooked(auth()->user()->name, ProviderProfile::where('id', $id)->value('company_name'), $start_time, $end_time));
            Mail::to($providerUser->email)->send(new AppointmentBookedProvider($providerUser->name, $start_time, $end_time));

            return redirect()->route('booking.appointmentBookedInfo');
        } catch (\Exception $e) {
            Log::error('Error booking appointment', [
                'error_message' => $e->getMessage(),
                'user_id' => auth()->id(),
                'provider_id' => $id,
                'start_time' => isset($appointment) ? $appointment->start_time->toDateTimeString() : null,
                'end_time' => isset($appointment) ? $appointment->end_time->toDateTimeString() : null,
                'stack_trace' => $e->getTraceAsString(),
            ]);
            \Jeybin\Toastr\Toastr::error('An error occurred while booking your appointment. Please try again later.')->toast();

            return redirect()->back();
        }

    }

    public function getAppointmentsforProviders($providerId)
    {
        $appointments = Appointment::where('provider_id', $providerId)->get();

        return response()->json($appointments->map(function ($appointments) {
            return [
                'id' => $appointments->id,
                'user_id' => $appointments->user_id,
                'status' => $appointments->status,
                'start_time' => Carbon::parse($appointments->start_time)->toIso8601String(),
                'end_time' => Carbon::parse($appointments->end_time)->toIso8601String(),
            ];
        }));

    }

    public function getAppointmentsforCustomers($userId)
    {
        $appointments = Appointment::where('user_id', $userId)->get();

        return response()->json($appointments->map(function ($appointments) {
            return [
                'id' => $appointments->id,
                'user_id' => $appointments->user_id,
                'status' => $appointments->status,
                'start_time' => Carbon::parse($appointments->start_time)->toIso8601String(),
                'end_time' => Carbon::parse($appointments->end_time)->toIso8601String(),
            ];
        }));
    }
}
