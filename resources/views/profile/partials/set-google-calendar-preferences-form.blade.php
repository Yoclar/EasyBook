<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Additional Settings') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 mb-2">
            {{ __('Set Google Calendar Preferences') }}
        </p>

    </header>
    <form method="POST" action="{{ route('enableGoogleCalendar') }}">
        @csrf
        <div class="flex flex-col">
            <label class="flex items-center">
                <input type="checkbox" name="google_calendar" value="1" {{ auth()->user()->using_google_calendar ? 'checked' : '' }} style="margin-right: 15px">
                <span class="text-white">Would you like us to automatically save your appointments to your Google Calendar?</span>
            </label>
            <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                This will only happen if you give us permission.
            </div>
        </div>
    <div class="flex items-center gap-4 mt-4">
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