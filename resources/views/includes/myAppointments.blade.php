<x-app-layout>
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        <!-- Bootstrap JavaScript -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
        <!-- jQuery (fontos a FullCalendar működéséhez) -->
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js'></script>

    <x-slot name="header">
    </x-slot>
    @if($role === 'provider')
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Pending Appoinments</h5>
                </div>
                <div class="card-body">
                    @if($bookings->isEmpty())
                        <p class="text-muted tex-center">No pending appointments.</p>
                    @else
                    
                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead class="table-dark">
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        {{--!  --}}
                                        {{-- Service name here later --}}
                                        <th scope="col">Start Time</th>
                                        <th scope="col">End Time</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($bookings as $booking)
                                        <tr>
                                            <td scope="row">{{ $booking->id }}</td>
                                            <td scope="row">{{ $booking->user->name }}</td>
                                            <td scope="row">{{ $booking->start_time }}</td>
                                            <td scope="row">{{ $booking->end_time }}</td>
                                            <td scope="row" style="display: flex; align-items: center;">
                                                <span class="badge 
                                                    {{ $booking->status === 'pending' ? 'bg-warning text-dark rounded-circle' : 
                                                    ($booking->status === 'confirmed' ? 'bg-success text-white rounded-circle' : 'bg-danger text-white rounded-circle') }}" 
                                                    style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; margin-right: 8px;">
                                                </span>
                                                {{ ucfirst($booking->status) }}
                                            </td>
                                            <td>
                                                @if($booking->status === 'pending')
                                                    <form action="{{ route('approveApplication', $booking->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-success btn-sm">✅ Accept</button>
                                                    </form>
                                                    <form action="{{ route('declineApplication', $booking->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="btn btn-danger btn-sm">❌ Reject</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">No actions available</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="container mt-4">
            <div class="card shadow-sm">
                <div id="calendar"></div>
            </div>
        </div>
        <script>
            let calendar;
            const providerId = @json(auth()->user()->providerProfile->id);
        
            document.addEventListener('DOMContentLoaded', async function () {
                const calendarEl = document.getElementById('calendar');
        
                if (!calendarEl) {
                    console.log("Calendar not found");
                    return;
                }
        
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
                        console.error('Error loading businessHours:', error);
                    }
                }
        
            
                await fetchBusinessHours();
        
                calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'timeGridWeek',
                    businessHours: formattedBusinessHours,
                    firstDay: 1,
                    slotMinTime: '06:00:00',
                    slotMaxTime: '22:00:00',
        
                    validRange: function (nowDate) {
                        let today = new Date();
                        let minTimeToday = new Date();
        
                        if (nowDate.getDate() === today.getDate() &&
                            nowDate.getMonth() === today.getMonth() &&
                            nowDate.getFullYear() === today.getFullYear()) {
        
                            let currentHour = today.getHours();
                            let currentMinutes = today.getMinutes();
        
                            minTimeToday.setHours(currentHour, currentMinutes, 0, 0);
        
                            return {
                                start: minTimeToday,
                                end: null
                            };
                        } else {
                            return {
                                start: new Date(),
                                end: null
                            };
                        }
                    },
        
                    events: async function (fetchInfo, successCallback, failureCallback) {
                        try {
                            const response = await fetch(`/get-bookings/provider/${providerId}`);
                            const events = await response.json();
        
                            const formattedEvents = events.map(event => {
                                let eventColor = '';
                                if (event.status === 'confirmed') {
                                    eventColor = '#28a745'; // zöld
                                } else if (event.status === 'pending') {
                                    eventColor = '#ffc107'; // sárga
                                }
        
                                return {
                                    
                                    //egy service name később esetleg
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
                        right: 'timeGridWeek,timeGridDay',
                    }
                });
        
                calendar.render();
            });
        </script>
    @else
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Pending Appoinments</h5>
            </div>
            <div class="card-body">
                @if($bookings->isEmpty())
                    <p class="text-muted tex-center">No pending appointments.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped align-middle">
                            <thead class="table-dark">
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Name</th>
                                    {{--!  --}}
                                    {{-- Service name here later --}}
                                    <th scope="col">Start Time</th>
                                    <th scope="col">End Time</th>
                                    <th scope="col">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookings as $booking)
                                    <tr>
                                        <td scope="row">{{ $booking->id }}</td>
                                        <td scope="row">{{ $booking->user->name }}</td>
                                        <td scope="row">{{ $booking->start_time }}</td>
                                        <td scope="row">{{ $booking->end_time }}</td>
                                        <td scope="row" style="display: flex; align-items: center;">
                                            <span class="badge 
                                                {{ $booking->status === 'pending' ? 'bg-warning text-dark rounded-circle' : 
                                                ($booking->status === 'confirmed' ? 'bg-success text-white rounded-circle' : 'bg-danger text-white rounded-circle') }}" 
                                                style="width: 30px; height: 30px; display: inline-flex; align-items: center; justify-content: center; margin-right: 8px;">
                                            </span>
                                            {{ ucfirst($booking->status) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div id="calendar"></div>
        </div>
    </div>


    <script>
        let calendar;
        const userId = @json(auth()->user()->id);
        document.addEventListener('DOMContentLoaded', async function(){
            const calendarEl = document.getElementById('calendar');

            if(!calendarEl) {
                console.log('Calendar not found.');
                return;
            }
            calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                firstDay: 1,
                slotMinTime: '06:00:00',
                slotMaxTime: '22:00:00',
                validRange: function (nowDate) {
                    let today = new Date();
                    let minTimeToday = new Date();
    
                    if (nowDate.getDate() === today.getDate() &&
                        nowDate.getMonth() === today.getMonth() &&
                        nowDate.getFullYear() === today.getFullYear()) {
    
                        let currentHour = today.getHours();
                        let currentMinutes = today.getMinutes();
    
                        minTimeToday.setHours(currentHour, currentMinutes, 0, 0);
    
                        return {
                            start: minTimeToday,
                            end: null
                        };
                    } else {
                        return {
                            start: new Date(),
                            end: null
                        };
                    }
                },
                events: async function (fetchInfo, successCallback, failureCallback) {
                    try {
                        const response = await fetch(`/get-bookings/customer/${userId}`);
                        const events = await response.json();
    
                        const formattedEvents = events.map(event => {
                            let eventColor = '';
                            if (event.status === 'confirmed') {
                                eventColor = '#28a745'; // zöld
                            } else if (event.status === 'pending') {
                                eventColor = '#ffc107'; // sárga
                            }
    
                            return {
                                
                                //egy service name később esetleg
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
                    right: 'timeGridWeek,timeGridDay',
                }

            });
            calendar.render();
        });
    </script>

    @endif
    

    
    

   
</x-app-layout>
