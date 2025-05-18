<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Working Hours') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Set your working hours for each day of the week.') }}
        </p>
    </header>

    <form method="POST" action="{{ route('setWorkingHours') }}" class="mt-6 space-y-6">
        @csrf

        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
            @php
                $dayData = $workingHour->where('day', $day)->first();
            @endphp

            <div class="mb-4">
                <x-input-label :value="__($day)" />

                <div class="flex items-center space-x-4 mt-1">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="working_days[{{ $day }}][is_working]" value="1"
                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                            {{ $dayData && $dayData->is_working_day ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('Workday') }}</span>
                    </label>

                    <div class="flex items-center space-x-2">
                        <label for="open_time_{{ $day }}" class="text-sm text-gray-700 dark:text-gray-300">{{ __('From') }}</label>
                        <input id="open_time_{{ $day }}" type="time" name="working_days[{{ $day }}][open_time]"
                            value="{{ $dayData ? $dayData->open_time : '' }}"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
                    </div>

                    <div class="flex items-center space-x-2">
                        <label for="close_time_{{ $day }}" class="text-sm text-gray-700 dark:text-gray-300">{{ __('To') }}</label>
                        <input id="close_time_{{ $day }}" type="time" name="working_days[{{ $day }}][close_time]"
                            value="{{ $dayData ? $dayData->close_time : '' }}"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:focus:border-indigo-600 dark:focus:ring-indigo-600" />
                    </div>
                </div>
            </div>
        @endforeach

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
