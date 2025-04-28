<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Services\GoogleCalendarService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;

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
        
            $bookings = Appointment::where('user_id', auth()->user()->id)->where('start_time','>' , $now)
                ->with(['provider'])
                ->get();
            $role = 'customer';
        }

        return view('includes.myAppointments', compact('bookings', 'role'));
    }  

    // ezt kicserélhetném ajaxra később
    public function approveApplication($id)
    {
        
        $appointment = Appointment::find($id);
        if (! $appointment) {
            abort(404, 'Appointment not found.');
        }
        //dd('Customer ID: ' . $appointment->user->id, 'Provider ID: ' . $appointment->provider->user->id, 'Customer Token: ' . $appointment->user->google_access_token, 'Provider Token: ' . $appointment->provider->user->google_access_token);
        $appointment->status = 'confirmed';
        $appointment->save();
        if($appointment->user->using_google_calendar)
        {

            $customerCalendarService = new GoogleCalendarService();
            $customerCalendarService->getClient(
                $appointment->user->google_access_token,
                $appointment->user->google_refresh_token,
                $appointment->user->google_token_expires_at,
                $appointment->user,
            );

          
        
            $customerCalendarService->createEvent([
                'summary' => 'Booked Appointment: ' . $appointment->service_name  . ' to ' . $appointment->user->name, 
                'start' => [
                    'dateTime' => Carbon::parse($appointment->start_time)->toIso8601String(),
                    'timeZone' => 'Europe/Budapest'
                ],
                'end' => [
                    'dateTime' => Carbon::parse($appointment->end_time)->toIso8601String(),
                    'timeZone' => 'Europe/Budapest'
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

        }
       if($appointment->provider->user->using_google_calendar)
        {

            $providerCalendarService = new GoogleCalendarService();
            $providerCalendarService->getClient(
                $appointment->provider->user->google_access_token,
                $appointment->provider->user->google_refresh_token,
                $appointment->provider->user->google_token_expires_at,
                $appointment->provider->user,
            );

            $providerCalendarService->createEvent([
                'summary' => 'Reserved by: ' . $appointment->user->name . ' for ' . $appointment->service_name, 
                'start' => [
                    'dateTime' => Carbon::parse($appointment->start_time)->toIso8601String(),
                    'timeZone' => 'Europe/Budapest'
                ],
                'end' => [
                    'dateTime' => Carbon::parse($appointment->end_time)->toIso8601String(),
                    'timeZone' => 'Europe/Budapest'
                ],
                'colorId' => 2,
                'reminders' => [
                    'useDefault' => false,
                    'overrides' => [
                        ['method' => 'popup', 'minutes' => 60],
                    ],
                ],
            ]);  
    
        } 



        // ide még kell majd egy email küldés
        \Jeybin\Toastr\Toastr::success('Appointment confirmed.')->toast();

        return redirect()->back();
  
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
