<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Belfoods | Loading</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/loading.css') }}">
</head>
<body>

<div class="background">

    <div class="blur blur-one"></div>
    <div class="blur blur-two"></div>
    <div class="blur blur-three"></div>

</div>

<div class="loading-container">

    <div class="snow-icon">
        ❄
    </div>

    <img
        src="{{ asset('assets/images/logo.png') }}"
        class="logo"
        alt="Belfoods Logo">

    <h2>
        Memuat...
    </h2>

    <p>
        Mohon tunggu sebentar
    </p>

    <div class="progress-box">

        <div class="progress">

            <div
                class="progress-fill"
                id="progressFill">
            </div>

        </div>

        <span
            id="progressText">

            0%

        </span>

    </div>

</div>

<script src="{{ asset('assets/js/loading.js') }}"></script>

</body>
</html>