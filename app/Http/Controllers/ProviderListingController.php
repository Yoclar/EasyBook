<?php

namespace App\Http\Controllers;

use App\Models\ProviderProfile;
use Illuminate\Http\Request;

class ProviderListingController extends Controller
{
    public function index(Request $request)
    {
        $query = ProviderProfile::with('user');
        if ($request->has('search')) {
            $searchInput = $request->input('search');
            $query->where('company_name', 'LIKE', "%{$searchInput}%");
        }
        $providers = $query->paginate(24);

        return view('includes.ProviderListing', compact('providers'));
    }
}
