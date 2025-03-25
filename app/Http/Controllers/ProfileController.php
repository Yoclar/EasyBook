<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProviderProfileUpdateRequest;
use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();

        $providerProfile = null;
        if ($user->role === 'provider') {
            $providerProfile = $user->providerProfile;
            $workingHours = WorkingHour::where('provider_id', $user->id)->get()->keyBy('day');
        }

        return view('profile.edit', [
            'user' => $request->user(),
            'providerProfile' => $providerProfile,
            'workingHour' => $workingHours,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateProviderProfile(ProviderProfileUpdateRequest $request): RedirectResponse
    {

        $profile = auth()->user()->providerProfile;

        $profile->fill($request->validated());

        if ($profile->isDirty()) {
            $profile->save();
        }

        return Redirect::route('profile.edit')->with('status', 'provider-profile-updated');
    }

    public function setWorkingHours(Request $request)
    {
        $request->validate([
            'working_days' => ['required', 'array'],
            'working_day.*.is_working' => ['nullable', 'boolean'],
            'working_days.*.open_time' => ['nullable', 'date_format:H:i', 'required_if:working_days.*.is_working,1'],
            'working_days.*.close_time' => ['nullable', 'date_format:H:i', 'required_if:working_days.*.is_working,1',
                function ($attribute, $value, $fail) use ($request) {
                    $day = explode('.', $attribute)[1];
                    $openTime = $request->input("working_days.$day.open_time");

                    if ($openTime && $value && strtotime($openTime) >= strtotime($value)) {
                        $fail("The closing time for $day must be later than the opening time.");
                    }
                }],
        ]);

        foreach ($request->input('working_days') as $day => $data) {
            WorkingHour::updateOrCreate(
                ['provider_id' => auth()->id(), 'day' => $day],
                [
                    'is_working_day' => isset($data['is_working']) ? 1 : 0,
                    'open_time' => isset($data['is_working']) ? $data['open_time'] : null,
                    'close_time' => isset($data['is_working']) ? $data['close_time'] : null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Working hours updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
