<?php

namespace App\Http\Controllers;

use App\Mail\AppointmentBooked;
use App\Models\Appointment;
use App\Models\ProviderProfile;
use App\Models\WorkingHour;
use App\Rules\DateValidationForBookingRule;
use Carbon\Carbon;
use Illuminate\Http\Request;
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

        $validated = $request->validate([
            'name' => ['required', 'exists:users,name'], // useless here :)
            'start_time' => ['required', 'date', new DateValidationForBookingRule],
            'end_time' => ['required', 'date', new DateValidationForBookingRule],
        ]);
        $start_time = Carbon::parse($validated['start_time']);
        $end_time = Carbon::parse($validated['end_time']);

        if ($end_time->lessThan($start_time)) {
            \Jeybin\Toastr\Toastr::error('End time must be after start time.')->toast();

            return redirect()->back();
        }

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
        /* SELECT * FROM appointments
            WHERE
            (start_time BETWEEN '2025-04-01 10:00:00' AND '2025-04-01 11:00:00')
            OR (end_time BETWEEN '2025-04-01 10:00:00' AND '2025-04-01 11:00:00')
            OR (start_time < '2025-04-01 10:00:00' AND end_time > '2025-04-01 11:00:00')
            LIMIT 1;
        */

        if ($isBooked) {
            \Jeybin\Toastr\Toastr::error('This appointment is already booked.')->timeOut(5000)->toast();

            return redirect()->back();
        }

        Appointment::create([
            'user_id' => auth()->user()->id,
            'provider_id' => $id,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'status' => 'pending',
        ]);
        \Jeybin\Toastr\Toastr::success('Appointment created successfully')->toast();

        Mail::to(auth()->user()->email)->send(new AppointmentBooked(auth()->user()->name, ProviderProfile::where('id', $id)->value('service_name'), $start_time, $end_time));

        return redirect()->route('booking.appointmentBookedInfo');

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
