<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        @if (!empty($role) && $role === 'provider')
        {{--! Picture goes here later, if I still have time and marking mandatory fields with *  --}}
        @endif
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
        @if (!empty($role) && $role === 'provider')
            
            <div class="mt-4">
                <x-input-label for="company_name" :value="__('Service Name')" />
                <x-text-input id="company_name" class="block mt-1 w-full" type="text" name="company_name" :value="old('company_name')"/>
                <x-input-error :messages="$errors->get('company_name')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="descriptione" :value="__('Description')" />
                <textarea id="description" name="description" class="mt-1 block w-full text-white border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"></textarea>
                <x-input-error :messages="$errors->get('description')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="address" :value="__('Address (Optional)')" />
                <x-text-input 
                    id="address" 
                    name="address" 
                    class="mt-1 block w-full"  
                    placeholder="1133, Budapest, Tisza utca 26"
                />
                <x-input-error class="mt-2" :messages="$errors->get('address')" />
            </div>
        
            <div class="mt-4">
                <x-input-label for="website" :value="__('Website (Optional)')" />
                <x-text-input 
                    type="url" 
                    id="website" 
                    name="website" 
                    class="mt-1 block w-full" 
                    placeholder="https://example.com"
                />
                <x-input-error class="mt-2" :messages="$errors->get('website')" />
            </div>
        @endif()

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
    <div class="my-6 flex items-center justify-center">
        <div class="flex-1 border-t border-gray-300"></div>
        <span class="px-3  text-gray-500 text-sm font-semibold mx-2">OR</span>
        <div class="flex-1 border-t border-gray-300"></div>
    </div>

    <a href="{{ route('login.google') }}?role={{ $role }}" 
    class="mt-4 flex items-center justify-center w-full  border border-gray-300 text-gray-700 py-2 rounded-lg shadow-sm hover:shadow-md transition">
     <img src="https://www.svgrepo.com/show/355037/google.svg" class="w-5 h-5 mr-2" style="margin-right:12px" alt="Google logo">
     Continue with Google
    </a>
 


  
    
    <!-- Telefon mező script -->
    <script src="https://cdn.jsdelivr.net/npm/intl-tel-input@21.1.1/build/js/intlTelInput.min.js" integrity="sha384-/oPKP9Vr3X1g+Pc37a9ENlRwEYqdqvSy1zPqZVE/DrEV569r71ZTIZojLaOjcqsx" crossorigin="anonymous"></script>
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
