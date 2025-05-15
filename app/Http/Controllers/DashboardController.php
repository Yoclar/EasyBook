<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Appointment;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        $now = now('Europe/Budapest');

        $nextAppointment = null;
        $unconfirmedBooking = null;

        if ($user->appointments()->exists()) {
            $nextAppointment = $user->appointments()
                ->where('start_time', '>', $now)
                ->where('status', '=', 'confirmed')
                ->orderBy('start_time')
                ->first();
        }

        if ($user->role === 'provider'){
            $unconfirmedBooking = Appointment::where('provider_id', $user->providerProfile->id)
                ->where('start_time', '>', $now)
                ->where('status', '=', 'pending')
                ->exists();
        }

        return view('dashboard', compact('nextAppointment', 'unconfirmedBooking'));
    }

}
