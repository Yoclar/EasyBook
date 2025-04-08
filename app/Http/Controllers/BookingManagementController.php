<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use Illuminate\Http\Request;
use App\Models\Appointment;

class BookingManagementController extends Controller
{
    public function index()
    {
        if(auth()->user()->role === 'provider')
        {
            
            $bookings = Appointment::where('provider_id', auth()->user()->providerProfile->id)
            ->with(['user'])
            ->get();
            $role = 'provider';
        }
        else{
            $bookings = Appointment::where('user_id', auth()->user()->id)
            ->with(['provider'])
            ->get();
            $role = 'customer';
        }
        
        return view('includes.myAppointments', compact('bookings', 'role'));
    }   //csak azokat kell átküldeni, amik még nem történtek meg (jövőbeliek)



    //ezt kicserélhetném ajaxra később
    public function approveApplication($id)
    {
        $appointment = Appointment::find($id);
        if(!$appointment)
        {
            abort(404, 'Appointment not found.');
        }
        $appointment->status = 'confirmed';
        $appointment->save();

        //ide még kell majd egy email küldés
        \Jeybin\Toastr\Toastr::success('Appointment confirmed.')->toast();
        return redirect()->back();
    }

    public function declineApplication($id)
    { 
        //talán törlöm az időpontot és értesítek
        $appointment = Appointment::find($id);
        if(!$appointment)
        {
            abort(404, 'Appointment not found.');
        }
        $appointment->status = 'canceled';
        $appointment->save();

        //ide még kell majd egy email küldés
        \Jeybin\Toastr\Toastr::success('Appointment deleted.')->toast();
        return redirect()->back();

    }
}
