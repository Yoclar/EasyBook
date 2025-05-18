<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppointmentAccepted;

class BookingManagementController extends Controller
{
    public function index()
    {
        $now = now('Europe/Budapest')->toDateTimeString();
        if (auth()->user()->role === 'provider') {

            $bookings = Appointment::where('provider_id', auth()->user()->providerProfile->id)->where('start_time', '>', $now)
                ->with(['user'])
                ->get();
            $role = 'provider';
        } else {

            $bookings = Appointment::where('user_id', auth()->user()->id)->where('start_time', '>', $now)
                ->with(['provider'])
                ->get();
            $role = 'customer';
        }

        return view('includes.myAppointments', compact('bookings', 'role'));
    }

    public function approveApplication($id)
    {
        try {
            $appointment = Appointment::find($id);
            if (! $appointment) {
                abort(404, 'Appointment not found.');
            }
            $appointment->status = 'confirmed';
            $appointment->save();
            if ($appointment->user->using_google_calendar) {

                $customerCalendarService = new GoogleCalendarService;
                $customerCalendarService->getClient(
                    $appointment->user->google_access_token,
                    $appointment->user->google_refresh_token,
                    $appointment->user->google_token_expires_at,
                    $appointment->user,
                );

                $customerCalendarService->createEvent([
                    'summary' => 'Booked Appointment: '.$appointment->service_name.' to '.$appointment->user->name,
                    'start' => [
                        'dateTime' => Carbon::parse($appointment->start_time)->toIso8601String(),
                        'timeZone' => 'Europe/Budapest',
                    ],
                    'end' => [
                        'dateTime' => Carbon::parse($appointment->end_time)->toIso8601String(),
                        'timeZone' => 'Europe/Budapest',
                    ],
                    'colorId' => 2,
                    'location' => $appointment->provider->address,
                    'reminders' => [
                        'useDefault' => false,
                        'overrides' => [
                            ['method' => 'email', 'minutes' => 24 * 60],
                            ['method' => 'popup', 'minutes' => 10],
                        ],
                    ],
                ]);
                Log::info('Event created in customer\'s calendar', [
                    'appointed_customer_id' => $appointment->user->id,
                    'appointed_provider_id' => $appointment->provider->user->id,
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                ]);

            }
            if ($appointment->provider->user->using_google_calendar) {

                $providerCalendarService = new GoogleCalendarService;
                $providerCalendarService->getClient(
                    $appointment->provider->user->google_access_token,
                    $appointment->provider->user->google_refresh_token,
                    $appointment->provider->user->google_token_expires_at,
                    $appointment->provider->user,
                );

                $providerCalendarService->createEvent([
                    'summary' => 'Reserved by: '.$appointment->user->name.' for '.$appointment->service_name,
                    'start' => [
                        'dateTime' => Carbon::parse($appointment->start_time)->toIso8601String(),
                        'timeZone' => 'Europe/Budapest',
                    ],
                    'end' => [
                        'dateTime' => Carbon::parse($appointment->end_time)->toIso8601String(),
                        'timeZone' => 'Europe/Budapest',
                    ],
                    'colorId' => 2,
                    'reminders' => [
                        'useDefault' => false,
                        'overrides' => [
                            ['method' => 'popup', 'minutes' => 60],
                        ],
                    ],
                ]);
                Log::info('Event created in provider\'s calendar', [
                    'appointed_customer_id' => $appointment->user->id,
                    'appointed_provider_id' => $appointment->provider->user->id,
                    'start_time' => $appointment->start_time,
                    'end_time' => $appointment->end_time,
                ]);

            }

            Mail::to($appointment->user->email)->send(new AppointmentAccepted($appointment->provider->company_name, $appointment->start_time, $appointment->end_time));
            \Jeybin\Toastr\Toastr::success('Appointment confirmed.')->toast();
            Log::info('Appointment confirmed successfully', [
                'user_id' => auth()->id(),
                'appointment_id' => $appointment->id,
                'customer_id' => $appointment->user->id,
                'provider_id' => $appointment->provider->user->id,
                'status' => $appointment->status,
                'start_time' => $appointment->start_time,
            ]);

            return redirect()->back();
        } catch (\Exception $e) {
            Log::error('Error confirming appointment', [
                'error_message' => $e->getMessage(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()->withErrors(['message' => 'There was a problem confirming the appointment.']);
        }

    }

    public function declineApplication($id)
    {
        // talán törlöm az időpontot és értesítek
        $appointment = Appointment::find($id);
        if (! $appointment) {
            abort(404, 'Appointment not found.');
        }
        $appointment->status = 'canceled';
        $appointment->save();

        // ide még kell majd egy email küldés
        \Jeybin\Toastr\Toastr::success('Appointment deleted.')->toast();

        return redirect()->back();

    }
}
