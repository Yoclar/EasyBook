<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class DashboardController extends Controller
{
    public function dashboard()
{
    $user = auth()->user();
    $now = now('Europe/Budapest');

    $nextAppointment = null;
    $unconfirmedBooking = null;

    if ($user->role === 'provider') {
      
        if ($user->receivedAppointments()->exists()) {
            $nextAppointment = $user->receivedAppointments()
                ->where('start_time', '>', $now)
                ->where('status', '=', 'confirmed')
                ->orderBy('start_time')
                ->first();
        }

        $unconfirmedBooking = $user->receivedAppointments()
            ->where('start_time', '>', $now)
            ->where('status', '=', 'pending')
            ->exists();

    } else {
       
        if ($user->appointments()->exists()) {
            $nextAppointment = $user->appointments()
                ->where('start_time', '>', $now)
                ->where('status', '=', 'confirmed')
                ->orderBy('start_time')
                ->first();
        }
    }
    return view('dashboard', compact('nextAppointment', 'unconfirmedBooking'));
}

}
