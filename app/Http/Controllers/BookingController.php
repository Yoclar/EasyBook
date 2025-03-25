<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;

class BookingController extends Controller
{
    public function index($id)
    {
        $provider = ProviderProfile::with('user')->findOrFail($id);

        return view('includes.booking', compact('provider'));
    }


}
