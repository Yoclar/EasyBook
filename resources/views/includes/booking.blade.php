<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Provider details
        </h2>
    </x-slot>
    <div class="d-flex justify-content-center mt-5">
        <div class="card text-center shadow-lg p-4" style="width: 80rem;">
            {{--TODO itt még a képeket egy kicsit át kell gondolni, de haladunk ;)  --}}
            <img src="{{ 
                $provider->profile_image 
                    ? (filter_var($provider->profile_image, FILTER_VALIDATE_URL) 
                        ? $provider->profile_image 
                        : asset('storage/' . $provider->profile_image)) 
                    : asset('storage/profile_images/placeholder-profile-img.jpg') }}" 
                 class="card-img-top rounded-circle mx-auto mt-3" 
                 style="width: 150px; height: 150px; object-fit: cover;">
    
            <div class="card-body">
                <h1 class="display-6 card-title">{{ $provider->service_name }}</h1>
                <p class="card-text">{{ $provider->description }}</p>
                
                <!-- Location (kikapcsolható) -->
                <p class="text-muted"><i class="bi bi-geo-alt"></i>{{ $provider->address }}</p>
    
                <!-- Webpage (kikapcsolható) -->
               <p><a href="{{ $provider->website }}" target="self" class="btn btn-link">Visit Website</a></p>
    
                <button class="btn btn-primary mt-3">Book Now</button>
            </div>
        </div>
    </div>
    
 
    



</x-app-layout>
