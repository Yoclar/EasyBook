<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center">
            Provider details
        </h2>
    </x-slot>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- jQuery (fontos a FullCalendar működéséhez) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }
    </style>
    <div class="d-flex justify-content-center mt-5">
        <div class="card text-center shadow-lg p-4" style="width: 50rem;">
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
    
                <button type="button" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#exampleModal">Book Now</button>
                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="exampleModalLabel">Modal title</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div id="calendar"></div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    
    
    <script>
        document.addEventListener('DOMContentLoaded', async function () {
            let calendar;
            const modal = document.getElementById('exampleModal');
            const calendarEl = document.getElementById('calendar');
    
            if (!modal || !calendarEl) return;
    
            // Business Hours betöltése AJAX segítségével
            let businessHours = [];
            const providerId = @json($provider->id);  // Az ID átadása a Blade-ből
            console.log(providerId);  
    
            let formattedBusinessHours = []; // Globális változóként deklaráljuk

            try {
                // A providerId-t használjuk a fetch kéréshez, helyes változó névvel
                const response = await fetch(`/get-business-hours/${providerId}`);
                console.log(response);
                const businessHours = await response.json();
                console.log(businessHours);

                // Változót itt töltjük meg adattal
                formattedBusinessHours = businessHours.map(item => ({
                    daysOfWeek: [item.daysOfWeek],
                    startTime: item.startTime,
                    endTime: item.endTime
                }));
                
                console.log(formattedBusinessHours);
            } catch (error) {
                console.error('Hiba a businessHours lekérdezésénél:', error);
            }

            modal.addEventListener('shown.bs.modal', function () {
                if (!calendar) {
                    calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        selectable: true,
                        //editable: true,
                        firstDay: 1,
                        slotMinTime: '06:00:00',
                        slotMaxTime: '22:00:00',

                        businessHours: formattedBusinessHours, 
                        selectConstraint: formattedBusinessHours,

                        /* events: '/get-events', */

                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay'
                        },


                    });

                    calendar.render();
                }
            });
    
            modal.addEventListener('hidden.bs.modal', function () {
                if (calendar) {
                    calendar.destroy();
                    calendar = null;
                }
            });
        });
    </script>
    
    
    
    
</x-app-layout>
