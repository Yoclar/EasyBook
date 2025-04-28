<x-mail::message>
# Your Application Has Been Sent to the Provider

Dear **{{ $name }}**,


Your application to the **{{ $company_name }}** on **{{ $start_time }}** -- **{{ $end_time }}** has been successfully sent.
Please note that **{{ $company_name }}** must accept your application for it to be valid.

We will notify you once a decision has been made.


{{-- this button maybe unnecessary here or maybe redirect to my appointments --}}
<x-mail::button :url="''">
Button Text
</x-mail::button>

Best regards,<br>
EasyBook team
</x-mail::message>
