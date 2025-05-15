<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.23.6/dist/css/uikit.min.css" />

    <!-- UIkit JS -->
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.6/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.23.6/dist/js/uikit-icons.min.js"></script>

    {{-- https://getuikit.com/docs/installation --}}


    @php
        $start_time = null;
        if ($nextAppointment != null)
        {
            $start_time = \Carbon\Carbon::parse($nextAppointment->start_time, 'Europe/Budapest');
        }
              
    @endphp
  <h3 class="text-center text-white"><span id="countdown" data-start="{{ $start_time ? $start_time->format('Y-m-d H:i:s') : '' }}"></span></h3>
    @if($unconfirmedBooking)
        <h3 class="text center text-white">You have unconfirmed booking</h3>
    @endif

    <script>
        function updateCountdown() {
            const countdownElement = document.getElementById('countdown');
            if(!countdownElement.dataset.start){
                countdownElement.textContent = "You have no upcoming confirmed appointment";
                return;
            }

            const startTime = new Date(countdownElement.dataset.start);
            const now = new Date();
    
            let diff = startTime - now;
    
            if (diff < 0) {
                countdownElement.textContent = "It started";
                setTimeout(() => {
                    location.reload();
                    }, 5000);  
            }
    
            const seconds = Math.floor((diff / 1000) % 60);
            const minutes = Math.floor((diff / (1000 * 60)) % 60);
            const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
            const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    
            let parts = [];

            if (days > 0) parts.push(`${days} day${days > 1 ? 's' : ''}`);
            if (hours > 0) parts.push(`${hours} hour${hours > 1 ? 's' : ''}`);
            if (minutes > 0) parts.push(`${minutes} minute${minutes > 1 ? 's' : ''}`);
            parts.push(`${seconds} second${seconds > 1 ? 's' : ''}`);
            parts.push(' left till your next appointment');

            countdownElement.textContent = parts.join(' ');

        }
    
        updateCountdown();  
        setInterval(updateCountdown, 1000);
    </script>
</x-app-layout>
