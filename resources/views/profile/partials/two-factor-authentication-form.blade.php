<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Two Factor Authentication') }}
        </h2>

        <p class="mt-1 text-sm text-white-600 dark:text-gray-400">
            {{ __("Add additional security to your account using two factor authentication.") }}
        </p>
    </header>

    
        <form method="POST" action="{{ url('/user/two-factor-authentication') }}" class="mt-6 space-y-6">
        @csrf
        <div class="flex gap-4">
            @if (auth()->user()->two_factor_secret)
                @method('DELETE')
                <x-danger-button type="submit">{{ __('Disable') }}</x-danger-button>
            @else
                <x-primary-button type="submit">{{ __('Enable') }}</x-primary-button>
            @endif
        </div>
    </form>

    @if (auth()->user()->two_factor_secret)
        <div class="flex flex-col gap-4 mt-5">
            <h3 class="text-lg font-medium text-white">{{ __('QR Code for authenticator applications') }}</h3>
 
            <div class="pt-5 pb-5  p-4 rounded" >
                <div class="qr-code-svg">
                    {!! auth()->user()->twoFactorQrCodeSvg() !!}
                </div>
            </div>
                <p class="mt-4 text-white">
                {{ __('Can’t scan the QR code? Enter this code manually in your authenticator app:') }}
            </p>
            <p class="font-mono text-white text-lg select-all">
                {{ decrypt(auth()->user()->two_factor_secret) }}
            </p>

            <h3 class="text-lg font-medium text-white">{{ __('Recovery codes') }}</h3>
            <ul class="space-y-2 text-white">
                @foreach(auth()->user()->recoveryCodes() as $code)
                    <li>{{ $code }}</li>
                @endforeach
            </ul>

            {{-- Re-Generating Recovery Codes --}}
            <form method="POST" action="{{ url('user/two-factor-recovery-codes') }}">
                @csrf
                <x-primary-button type="submit">{{ __('Re-Generate Recovery Codes') }}</x-primary-button>
            </form>
        </div>
    @endif

    

    @php
        $sessionStatus = session('status') === 'two-factor-authentication-enabled' ? 'Two factor authentication is enabled.' :
                         (session('status') === 'two-factor-authentication-disabled' ? 'Two factor authentication is disabled.' : '');
    @endphp

    @if ($sessionStatus)
        <p x-data="{ show: true }"
            x-show="show"
            x-transition
            x-init="setTimeout(() => show = false, 4000)"
            class="text-sm text-gray-600 dark:text-gray-400 mt-4">
            {{ __($sessionStatus) }}
        </p>
    @endif
</section>
