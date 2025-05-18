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
use Illuminate\Support\Facades\Mail;
use App\Mail\RegistrationSuccessful;

class LoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectToGoogle(Request $request)
    {
        $allowedRoles = ['customer', 'provider'];
        $role = $request->query('role');
        if(!in_array($role, $allowedRoles)){
            abort(400, 'Invalid role');
        }
        session(['role' => $role]);

        return Socialite::driver('google')
            ->scopes(['openid', 'profile', 'email', 'https://www.googleapis.com/auth/calendar'])
            ->with(['access_type' => 'offline', 'prompt' => 'consent'])
            ->redirect();
    }

    /**
     * Obtain the user information from Google and log them in.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleGoogleCallback()
    {

        try {
            $user = Socialite::driver('google')->user();
            $role = session('role', 'customer');
            Log::info('Google callback received', ['email' => $user->getEmail(), 'role' => $role]);
            $existingUser = User::where('email', $user->getEmail())->first();

            if ($existingUser) {
                Auth::login($existingUser);
                Log::info('Existing user logged in via Google', ['user_id' => $existingUser->id]);

                return redirect()->route('dashboard');
            } else {
                $newUser = User::create([
                    'name' => $user->getName(),
                    'email' => $user->getEmail(),
                    'is_google_user' => true,
                    'google_access_token' => $user->token,
                    'google_refresh_token' => $user->refreshToken,
                    'google_token_expires_at' => now('Europe/Budapest')->addSeconds($user->expiresIn),
                    'using_google_calendar' => false,
                    'password' => bcrypt(Str::random(32)),
                    'role' => $role ?? 'customer',
                ]);
                Log::info('New user created via Google login', ['user_id' => $newUser->id]);
                if ($role == 'provider') {

                    $providerProfile = ProviderProfile::create([
                        'user_id' => $newUser->id,
                        'profile_image' => $user->getAvatar(),
                        'company_name' => $this->generateProviderName('Company'),
                    ]);
                    Log::info('Provider profile created', ['provider_id' => $providerProfile->id]);
                    foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                        $workingHour = WorkingHour::create([
                            'provider_id' => $providerProfile->id,
                            'day' => $day,
                            'is_working_day' => ($day !== 'Saturday' && $day !== 'Sunday') ? 1 : 0,
                            'open_time' => ($day !== 'Saturday' && $day !== 'Sunday') ? '08:00' : null,
                            'close_time' => ($day !== 'Saturday' && $day !== 'Sunday') ? '16:00' : null,
                        ]);
                        Log::debug('Working hour created', ['day' => $day, 'working_hour_id' => $workingHour->id]);
                    }

                }

                Auth::login($newUser);
                Mail::to($newUser->email)->send(new RegistrationSuccessful($newUser->name));

            }
            if ($role == 'provider') {
                \Jeybin\Toastr\Toastr::info('Please edit your profile details')->timeOut(5000)->toast();

            }

            return redirect()->route('dashboard');

        } catch (\Exception $e) {
            Log::error('Google login failed', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect('login')->with('error', 'Google login failed or no email returned.');
        }
    }

    private function generateProviderName($basestring)
    {
        do {
            $uuid = UUid::uuid4()->toString();
            $generatedServiceName = $basestring.'_'.substr($uuid, 0, 8);
            $userExists = ProviderProfile::where('company_name', $generatedServiceName)->exists();
        } while ($userExists);

        return $generatedServiceName;
    }
}
