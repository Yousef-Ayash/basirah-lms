<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}"
    @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    @if (app()->getLocale() == 'ar')
        <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap"
            rel="stylesheet">
    @else
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap"
            rel="stylesheet">
    @endif

    <style>
        :root {
            @if (app()->getLocale() == 'ar')
                --font-sans: 'Noto Kufi Arabic', ui-sans-serif, system-ui, sans-serif;
            @else
                --font-sans: 'Roboto', ui-sans-serif, system-ui, sans-serif;
            @endif
        }
    </style>

    <link rel="icon" href="/favicon.svg" type="image/svg+xml">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', 'resources/css/app.css', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>

<body class="font-sans antialiased">
    @inertia
</body>

</html>
