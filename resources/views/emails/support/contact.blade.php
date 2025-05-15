<x-mail::message>
# New message from a user

**Name:** {{ $name }}
**Email:** {{ $email }}
**Subject:** {{ $subject }}

---

{{ $message }}

---

<x-mail::button :url="'mailto:'.$email">
    Reply to this message
</x-mail::button>

This message was sent via the contact form on your website.
</x-mail::message>
