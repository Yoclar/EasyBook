<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\User;
use App\Models\WorkingHour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Ramsey\Uuid\Uuid;

class LoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle(Request $request)
    {
        $role = $request->query('role');
        session(['role' => $role]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {
        try {
            $user = Socialite::driver('google')->stateless()->user();
            $role = session('role', 'customer');
            $existingUser = User::where('email', $user->getEmail())->first();
            // TODO
            // ! if the user changes their password, in the profiles he/she can't sign in with that google account anymore
            // ! need a solution

            if ($existingUser) {
                Auth::login($existingUser);
            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'is_google_user' => true,
                    'password' => bcrypt(Str::random(32)),
                    'role' => $role ?? 'customer',
                ]);
                if ($role == 'provider') {

                    $providerProfile = ProviderProfile::create([
                        'user_id' => $newUser->id,
                        'profile_image' => $user->getAvatar(),
                        'service_name' => $this->generateProviderName('Service_name'),
                    ]);
                    foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                        WorkingHour::create([
                            'provider_id' =>  $providerProfile->id,
                            'day' => $day,
                            'is_working_day' => ($day !== 'Saturday' && $day !== 'Sunday') ? 1 : 0,
                            'open_time' => ($day !== 'Saturday' && $day !== 'Sunday') ? '08:00' : null,
                            'close_time' => ($day !== 'Saturday' && $day !== 'Sunday') ? '16:00' : null,
                        ]);
                    }

                }

                Auth::login($newUser);
            }
            if($role == 'provider')
            {
                \Jeybin\Toastr\Toastr::info('Please edit your profile details')->toast();
                return redirect()->to('/dashboard');
            }
            else
            {
                return redirect()->to('/dashboard');
            }

        } catch (\Exception $e) {
            dd('Error creating provider profile:', $e->getMessage());

            return redirect('login')->with('error', 'Google login failed or no email returned.');
        }
    }

    private function generateProviderName($basestring)
    {
        do {
            $uuid = UUid::uuid4()->toString();
            $generatedServiceName = $basestring.'_'.substr($uuid, 0, 8);
            $userExists = ProviderProfile::where('service_name', $generatedServiceName)->exists();
        } while ($userExists);

        return $generatedServiceName;
    }
}
