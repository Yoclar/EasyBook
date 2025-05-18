<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>EasyBook</title>

    {{-- Bootstrap CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    {{-- Animate.css --}}
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" integrity="sha384-Gu3KVV2H9d+yA4QDpVB7VcOyhJlAVrcXd0thEjr4KznfaFPLe0xQJyonVxONa4ZC" crossorigin="anonymous" referrerpolicy="no-referrer" />


    
</head>
<body>

    <style>
     /* Alapértelmezett világos mód */
body {
    background-image: linear-gradient(to bottom, #051e44, #0b356e, #104f9c, #0f6acc, #0086ff);
    color: black;
}

/* Kártyák és FAQ alapértelmezett világos mód */
.card {
    background-color: white;
    color: black;
}

/* Kártyák és FAQ sötét mód */
@media (prefers-color-scheme: dark) {
    body {
        color: white;
    }

    .card {
        background-color: #2c2c2c; /* Sötét háttér a kártyáknak */
        color: white; /* Fehér szöveg a kártyákban */
    }

    .accordion-button {
        background-color: #333333; /* Sötét háttér a gomboknak */
        color: white; /* Fehér szöveg */
    }

    .accordion-button:not(.collapsed) {
        background-color: #444444; /* Sötét háttér, ha a gomb fel van nyitva */
        color: white; /* Fehér szöveg */
    }

    .accordion-body {
        background-color: #222222; /* Sötét háttér a FAQ szövegekhez */
        color: white; /* Fehér szöveg */
    }
}

    </style>
    <main>
        {{ $slot }}
    </main>

    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
