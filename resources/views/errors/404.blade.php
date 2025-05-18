<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <title>Page 404 Scrolling Terrain and Shooting Stars</title>

    <!-- Betűtípusok -->
    <style>
        @font-face {
            font-family: 'Pine Jungle Night';
            src: url('{{ asset('storage/assets/fonts/pine-jungle-night.ttf') }}') format('truetype');
        }
        @font-face {
            font-family: 'Wild Dreams';
            src: url('{{ asset('storage/assets/fonts/wilddreams.woff') }}') format('woff'),
                 url('{{ asset('storage/assets/fonts/wilddreams.otf') }}') format('opentype');
        }
    </style>

    <!-- Stíluslap -->
    <link rel="stylesheet" href="{{ asset('storage/assets/css/style.css') }}" />
</head>
<body>
    <div class="text-404">
        <h1>4</h1>
        <h1>X</h1>
        <h1>4</h1>
    </div>

    <!-- Kép (ha szükséges, itt például beillesztheted) -->
    <img src="{{ asset('storage/assets/pic/Image.png') }}" alt="404 Image" />

    <!-- Canvas elem a háttér animációhoz -->
    <canvas id="bgCanvas"></canvas>

    <!-- JS script -->
    <script src="{{ asset('storage/assets/js/script.js') }}"></script>
</body>
</html>
