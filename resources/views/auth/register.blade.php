<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- Phone masking --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.1.1/build/css/intlTelInput.min.css">
    {{-- Password strength checker JQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input {{-- oninput="checkEmailTaken(this.value);" --}} id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
            {{-- <div id="responseTextEmail"></div> --}}
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('Phone')" />
            <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')"/>
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
            <div class="text-primary" id="passwordStrength"></div>
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button id="logButton" class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Telefon mező script -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@21.1.1/build/js/intlTelInput.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const input = document.querySelector("#phone");
            window.intlTelInput(input, {
                initialCountry: "auto",
                geoIpLookup: callback => {
                    fetch("https://ipapi.co/json")
                        .then(res => res.json())
                        .then(data => callback(data.country_code))
                        .catch(() => callback("us"));
                },
                strictMode: true,
                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@21.1.1/build/js/utils.js",
            });
        });

        document.getElementById('password').addEventListener('input', function() {
            var password = this.value;
            $.ajax({
                type: 'POST',
                url: '/calculate-entropy',
                data: {
                    password: password,
                    _token: '{{ csrf_token() }}',
                },
                success: function(response) {
                    var strength;
                    var colorClass;
                    switch (true) {
                        case (response.entropy <= 35):
                            strength = 'Weak';
                            colorClass = 'text-danger';
                            break;
                        case (response.entropy >= 36 && response.entropy <= 59):
                            strength = 'Moderate';
                            colorClass = 'text-warning';
                            break;
                        case (response.entropy >= 60 && response.entropy <= 119):
                            strength = 'Strong';
                            colorClass = 'text-primary';
                            break;
                        case (response.entropy >= 120):
                            strength = 'Very Strong';
                            colorClass = 'text-success';
                            break;
                        default:
                            strength = 'Something went wrong';
                            colorClass = 'text-dark';
                            break;
                    }
                    var passwordStrengthElement = document.getElementById('passwordStrength');
                    passwordStrengthElement.innerText = 'Password Strength: ' + strength;
                    passwordStrengthElement.className = colorClass;
                },
                error: function(xhr, textStatus, errorThrown) {
                    console.log("Error:", errorThrown);
                }
            });
        });

    </script>

</x-guest-layout>
