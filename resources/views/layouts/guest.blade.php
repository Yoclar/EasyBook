<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link rel="stylesheet" href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap%22" integrity="sha384-hLG8bFljtgHuApB4oCBVOFz0sSZ8HoEc6SDeUpH1AQ3wZkXTOsGpfQIvQpMzm7R7" crossorigin="anonymous">
        
        {{-- Phone masking --}}
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@21.1.1/build/css/intlTelInput.min.css" integrity="sha384-nRa8ACvWDbreLC5BICBF/Q9xN3hpD/giT+MQUzFCsQmYOUHOglRzE6cftYnb1Awv" crossorigin="anonymous">
         <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
    <style>
        body{
            background-image: linear-gradient(to bottom, #051e44, #0b356e, #104f9c, #0f6acc, #0086ff);
            color: black;
        }
    </style>
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100 dark:bg-gray-900">
            <div>
                <a href="/" style="text-decoration:none">
                    <h1 class="text-3xl font-bold text-gray-800 dark:text-white" style="font-family:cursive ">EasyBook</h1>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 dark:bg-gray-800 shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    {{-- Jquery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha384-vtXRMe3mGCbOeY7l30aIg8H9p3GdeSe4IFlP6G8JMa7o7lXvnz3GFKzPxzJdPfGK" crossorigin="anonymous"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    {{-- Fullcalendar --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js" integrity="sha384-B1OFx8Gy9GjPu8UbUyXbGQpzll9ubAUQ9agInFJ8NnD7nYG1u/CLR+Sqr5yifl4q" crossorigin="anonymous"></script>
    </body>
</html>
