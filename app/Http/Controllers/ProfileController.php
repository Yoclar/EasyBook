<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Requests\ProviderProfileUpdateRequest;
use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
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
        $workingHours = null;

        if ($user->role === 'provider') {
            $providerProfile = $user->providerProfile;
            $workingHours = WorkingHour::where('provider_id', $providerProfile->id)->get()->keyBy('day');
        }

        return view('profile.edit', [
            'user' => $user,
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
        \Jeybin\Toastr\Toastr::success('Your profile updated successfully')->toast();
        Log::info('User profile info updated', [
            'user_id' => auth()->id(),
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    public function updateProviderProfile(ProviderProfileUpdateRequest $request): RedirectResponse
    {

        $profile = auth()->user()->providerProfile;

        $profile->fill($request->validated());

        if ($profile->isDirty()) {
            $profile->save();
        }
        \Jeybin\Toastr\Toastr::success('Your providerprofile updated successfully')->toast();
        Log::info('Providerprofile info updated', [
            'user_id' => auth()->id(),
            'provider_id' => $profile->id,
        ]);

        return Redirect::route('profile.edit')->with('status', 'provider-profile-updated');
    }

    public function setWorkingHours(Request $request)
    {

        foreach ($request->input('working_days') as $day => $data) {

            $data['open_time'] = substr($data['open_time'], 0, 5); // Eltávolítja a másodperceket
            $data['close_time'] = substr($data['close_time'], 0, 5); // Eltávolítja a másodperceket
            // Validáció
            $validator = Validator::make($data, [
                'is_working' => 'sometimes|boolean',
                'open_time' => ['sometimes', 'required_if:is_working,1', 'nullable', 'date_format:H:i'],
                'close_time' => ['sometimes', 'required_if:is_working,1', 'nullable', 'date_format:H:i'],
            ]);

            if ($validator->fails()) {
                \Jeybin\Toastr\Toastr::error('Something was not in the correct format.')->toast();
                Log::warning('User tried to set invalid working hours (formar error)', [
                    'user_id' => auth()->id(),
                ]);

                return redirect()->back()->withErrors($validator)->withInput();
            }

            // Idő konverzió
            if ($data['open_time'] && $data['close_time']) {
                $convertedOpenTimeArray = explode(':', $data['open_time']);
                $convertedCloseTimeArray = explode(':', $data['close_time']);
                $convertedOpenTimeToInt = intval($convertedOpenTimeArray[0]) * 60 + intval($convertedOpenTimeArray[1]);
                $convertedCloseTimeToInt = intval($convertedCloseTimeArray[0] * 60) + intval($convertedCloseTimeArray[1]);
                // Időpontok ellenőrzése

                if ($convertedCloseTimeToInt <= $convertedOpenTimeToInt) {
                    \Jeybin\Toastr\Toastr::error('Close time cannot be earlier than open time.')->toast();
                    Log::warning('User tried to set invalid working hours (Close time cannot be earlier than open time)', [
                        'user_id' => auth()->id(),
                    ]);

                    return redirect()->back();
                }
            }

            WorkingHour::updateOrCreate(
                ['provider_id' => auth()->user()->providerProfile->id, 'day' => $day],
                [
                    'is_working_day' => isset($data['is_working']) ? 1 : 0,
                    'open_time' => isset($data['is_working']) ? $data['open_time'] : null,
                    'close_time' => isset($data['is_working']) ? $data['close_time'] : null,
                ]
            );
        }

        \Jeybin\Toastr\Toastr::success('Working hour saved successfully')->toast();
        Log::info('Working hour saved or modified successfully', [
            'user_id' => auth()->id(),
        ]);

        return redirect()->back();

    }

    public function enableGoogleCalendar(Request $request)
    {

        $user = auth()->user();
        $google_calendar = $request->input('google_calendar');
        if ($google_calendar == true) {
            $user->using_google_calendar = true;
            $user->update();
            \Jeybin\Toastr\Toastr::success('Google Calendar preference saved successfully')->toast();
            Log::info('Google Calendar option turned on successfully', [
                'user_id' => auth()->id(),
                'google_calendar' => true,
            ]);

            return redirect()->back();
        } else {
            $user->using_google_calendar = false;
            $user->update();
            \Jeybin\Toastr\Toastr::success('Google Calendar preference saved successfully')->toast();
            Log::info('Google Calendar option turned off successfully', [
                'user_id' => auth()->id(),
                'google_calendar' => false,
            ]);

            return redirect()->back();
        }
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

        $userId = $user->id;
        Auth::logout();
        $user->delete();
        Log::info('User deleted successfully', [
            'user_id' => auth()->id(),
        ]);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
