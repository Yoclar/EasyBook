<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Provider Profile Information') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __("Update your provider profile information.") }}
        </p>

    </header>

    <form method="post" action=" {{ route('provider.profile.update') }}" class="mt-6 space-y-6">
    @csrf
    @method('patch')
    <div class="mb-4">
        <x-input-label for="service_name" :value="__('Service Name')" />
        <x-text-input id="service_name" name="service_name" type="text" class="mt-1 block w-full" 
            :value="old('service_name', $providerProfile->service_name ?? '')" required />
        <x-input-error class="mt-2" :messages="$errors->get('service_name')" />
    </div>

    <div class="mb-4">
        <x-input-label for="description" :value="__('Description')" />
        <textarea id="description" name="description" class="mt-1 block w-full">{{ old('description', $providerProfile->description ?? '') }}</textarea>
        <x-input-error class="mt-2" :messages="$errors->get('description')" />
    </div>

    <div class="mb-4">
        <x-input-label for="address" :value="__('Address')" />
        <x-text-input 
            id="address" 
            name="address" 
            class="mt-1 block w-full" 
            :value="old('address', $providerProfile->address ?? '')"
            :placeholder="empty(old('address', $providerProfile->address ?? '')) ? '1133, Budapest, Tisza utca 26' : ''" 
        />
        <x-input-error class="mt-2" :messages="$errors->get('address')" />
    </div>

    <div class="mb-4">
        <x-input-label for="website" :value="__('Website')" />
        <x-text-input 
            type="url" 
            id="website" 
            name="website" 
            class="mt-1 block w-full" 
            :value="old('website', $providerProfile->website ?? '')" 
            :placeholder="empty(old('website', $providerProfile->website ?? '')) ? 'https://example.com' : ''"
        />
        <x-input-error class="mt-2" :messages="$errors->get('website')" />
    </div>

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