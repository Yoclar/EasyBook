<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Http\Request;

class ProviderListingController extends Controller
{
    /*     use WithPagination; */
    public function index(Request $request)
    {

        $providers = ProviderProfile::with('user')->get();

        return view('includes.ProviderListing', [
            'providers' => $providers,
        ]);

        /* return view('profile.edit', [
            'user' => $request->user(),
            'providerProfile' => $providerProfile,
        ]); */
    }
}
