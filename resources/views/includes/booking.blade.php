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
    <div class="modal fade" id="bookingModal" tabindex="-1" aria-labelledby="bookingModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bookingModalLabel">Foglalás</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bookingForm" method="POST" action="{{ route('store', $provider->id) }} ">
                        @csrf
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" class="form-control" value="{{ Auth::user()->name }}" readonly>
                        </div>
                        <div class="mb-3">
                            <label for="start_time" class="form-label">Start time</label>
                            <input type="datetime-local" name="start_time" class="form-control" id="start_time" value="{{ old('start_time') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="end_time" class="form-label">End time</label>
                            <input type="datetime-local" name="end_time" class="form-control" id="end_time" value="{{ old('end_time') }}" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
   
    
    
    <script>
        let calendar; // Globális változó a naptárhoz
    
        document.addEventListener('DOMContentLoaded', async function () {
            const modal = document.getElementById('exampleModal');
            const calendarEl = document.getElementById('calendar');
    
            if (!modal || !calendarEl) {
                console.error("Hiba: A 'exampleModal' vagy 'calendar' elem nem található!");
                return;
            }
    
            const providerId = @json($provider->id);
            let formattedBusinessHours = [];
    
            async function fetchBusinessHours() {
                try {
                    const response = await fetch(`/get-business-hours/${providerId}`);
                    const businessHours = await response.json();
                    formattedBusinessHours = businessHours.map(item => ({
                        daysOfWeek: [item.daysOfWeek],
                        startTime: item.startTime,
                        endTime: item.endTime
                    }));
                } catch (error) {
                    console.error('Hiba a businessHours lekérdezésénél:', error);
                }
            }
    
            modal.addEventListener('shown.bs.modal', async function () {
                await fetchBusinessHours();
    
                if (!calendar) {
                    calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'timeGridWeek',
                        selectable: true,
                        firstDay: 1,
                        slotMinTime: '06:00:00',
                        slotMaxTime: '22:00:00',
                        businessHours: formattedBusinessHours,
                        selectConstraint: formattedBusinessHours,
                        validRange: function(nowDate) {
                            let today = new Date();
                            let minTimeToday = new Date();
                            
                            // Ha ma van, akkor a mostani időtől engedünk csak foglalást
                            if (nowDate.getDate() === today.getDate() &&
                                nowDate.getMonth() === today.getMonth() &&
                                nowDate.getFullYear() === today.getFullYear()) {
                                
                                let currentHour = today.getHours();
                                let currentMinutes = today.getMinutes();
                                
                                // Ha például most 14:30 van, akkor 14:30 előtti időpontok tiltva
                                minTimeToday.setHours(currentHour, currentMinutes, 0, 0);
                                
                                return {
                                    start: minTimeToday, // Ma csak a jövőbeli órák engedélyezettek
                                    end: null // A jövőbeli napok korlátozás nélkül foglalhatók
                                };
                            } else {
                                return {
                                    start: new Date(), // Minden más nap teljesen elérhető a mai naptól
                                    end: null
                                };
                            }
                        },
                        events: async function (fetchInfo, successCallback, failureCallback) {
                            try {
                                const providerId = @json($provider->id);
                                console.log("Lekérdezett időszak:", fetchInfo.startStr, "->", fetchInfo.endStr); // Debug
                                const response = await fetch(`/get-bookings/${providerId}`);
                                const events = await response.json();

                                const formattedEvents = events.map(event => {
                                    let eventColor = '';
                                    if(event.status === 'confirmed'){
                                        eventColor = '#28a745'; //zöld
                                    }
                                    else if(event.status === 'pending'){
                                        eventColor = '#ffc107';//sárga
                                    }
                         
                                    return {
                                        title: `Reserved - User ${event.user_id}`,
                                        start: event.start_time,
                                        end: event.end_time,
                                        backgroundColor: eventColor,
                                        borderColor: eventColor
                                    };

                            });
                                successCallback(formattedEvents);
                            } catch (error) {
                                console.error('Hiba az események betöltésekor:', error);
                                failureCallback(error);
                            }
                            },

                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'timeGridWeek,timeGridDay'
                        },
                        select: function (info) {
    
                            const startTimeInput = document.getElementById('start_time');
                            const endTimeInput = document.getElementById('end_time');
    
                            if (!startTimeInput || !endTimeInput) {
                                console.error("Nem található az időpont mező!");
                                return;
                            }
    
                            // Azonnali értékbeállítás
                            startTimeInput.value = formatDateToLocal(info.start);
                            endTimeInput.value = formatDateToLocal(info.end);
    
                            var myModal = new bootstrap.Modal(document.getElementById('bookingModal'));
                            myModal.show();
                        }
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
        function formatDateToLocal(date) {
            let offset = date.getTimezoneOffset(); // Az időzóna eltérése percekben
            let localDate = new Date(date.getTime() - offset * 60000); // Időeltolás korrigálása
            return localDate.toISOString().slice(0, 16); // Helyes formátum
        }
    </script>
    
    
    
    
    
</x-app-layout>
