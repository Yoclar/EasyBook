<x-mail::message>
# New Booking Received

Dear **{{ $name }}**,

You have received a new booking for **{{ $start_time }}** – **{{ $end_time }}**.

Please review the request and confirm or reject it.

<x-mail::button :url="route('myBookingsIndex')">
View Bookings
</x-mail::button>

Best regards,<br>
EasyBook team
</x-mail::message>
