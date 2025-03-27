<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\User;
use App\Models\WorkingHour;
use App\Rules\AddressFormatRule;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    public function showRegistrationForm(Request $request)
    {

        if (Auth::check()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            cookie()->queue(cookie()->forget('laravel_session'));
        }

        $role = $request->query('role', 'customer');
        session(['registration_role' => $role]);

        return view('auth.register', compact('role'));
    }

    public function register(Request $request): RedirectResponse
    {

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'phone' => ['nullable', 'string', 'max:20'],
        ];
        $role = session('registration_role', 'customer');

        if ($role === 'provider') {
            $rules['service_name'] = ['required', 'string', 'max:255'];
            $rules['description'] = ['nullable', 'string'];
            $rules['average_price'] = ['nullable', 'integer', 'min:0'];
            $rules['address'] = ['nullable', new AddressFormatRule];
            $rules['website'] = ['nullable', 'url']; // change to active_url (only working link can be accepted)
        }
        $validated = $request->validate($rules);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
            'role' => $role,
        ]);

        if ($role === 'provider') {
            $providerProfile = ProviderProfile::create([
                'user_id' => $user->id,
                'service_name' => $validated['service_name'],
                'description' => $validated['description'] ?? '',
                'address' => $validated['address'] ?? null,
                'website' => $validated['website'] ?? null,

            ]);
            foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day) {
                WorkingHour::create([
                    'provider_id' => $user->id,
                    'day' => $day,
                    'is_working_day' => ($day !== 'Saturday' && $day !== 'Sunday') ? 1 : 0,
                    'open_time' => ($day !== 'Saturday' && $day !== 'Sunday') ? '08:00' : null,
                    'close_time' => ($day !== 'Saturday' && $day !== 'Sunday') ? '16:00' : null,
                ]);
            }

        }

        Auth::login($user);

        session()->forget('registration_role');
        \Jeybin\Toastr\Toastr::error('Registration successful')->toast();
        return redirect(route('dashboard'));
    }

    public function calculateEntropy(Request $request)
    {
        $password = $request->input('password');

        $entropy = $this->calculateEntropyScore($password);

        return response()->json(['entropy' => $entropy]);
    }

    private function calculateEntropyScore($password)
    {
        $charset = 0;

        if (preg_match('/[a-z]/', $password)) {
            $charset += 26;
        }
        if (preg_match('/[A-Z]/', $password)) {
            $charset += 26;
        }
        if (preg_match('/[0-9]/', $password)) {
            $charset += 10;
        }
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $charset += 30;
        }
        $entropy = round(log(pow($charset, strlen($password)), 2));

        return $entropy;
    }

    /*  public function checkEmailIsTaken(Request $request)
     {
         $email = $request->input('email');

         $userFound = User::where('email', $email)->exists();
         if ($userFound) {
             return response()->json([
                 'status' => 'failed',
                 'message' => 'Email is already taken',
             ]);
         }
         return response()->json([
             'status' => 'success',
         ]);

     } */
}
