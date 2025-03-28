<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Provider Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Set your working hours") }}
        </p>

    </header>
    <form method="POST" action="{{ route('setWorkingHours') }}">
        @csrf
        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
        <div>
            @php
                $dayData = $workingHour->where('day', $day)->first();
            @endphp
            <label>{{ $day }}</label>
            <input type="checkbox" name="working_days[{{ $day }}][is_working]" value="1"
            {{ $dayData && $dayData->is_working_day ? 'checked' : ''  }}> Workday
            <input type="time" name="working_days[{{ $day }}][open_time]"
            value="{{ $dayData ? $dayData->open_time : '' }}">
            <input type="time" name="working_days[{{ $day }}][close_time]"
            value="{{ $dayData ? $dayData->close_time : '' }}">
        </div>
       
        @endforeach
        {{--  <x-text-input id="service_name" name="service_name" type="text" class="mt-1 block w-full" 
            :value="old('service_name', $providerProfile->service_name ?? '')" required /> --}}
        {{-- workingHours --}}
    <div class="flex items-center gap-4">
        <x-primary-button>{{ __('Save') }}</x-primary-button>

        @if (session('status') === 'profile-updated')
            <p
                x-data="{ show: true }"
                x-show="show"
                x-transition
                x-init="setTimeout(() => show = false, 2000)"
                class="text-sm text-gray-600 dark:text-gray-400"
            >{{ __('Saved.') }}</p>
        @endif
    </div>
    </form>
</section>