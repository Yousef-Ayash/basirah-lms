<!DOCTYPE html>
{{-- <html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}" --}}
<html lang="ar" dir="rtl" @class(['dark' => ($appearance ?? 'system') == 'dark'])>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title inertia>{{ config('app.name', 'برنامج بصيرة للدراسات الإسلامية') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Noto+Kufi+Arabic:wght@100..900&display=swap" rel="stylesheet">

    <style>
        :root {
            --font-sans: 'Noto Kufi Arabic', ui-sans-serif, system-ui, sans-serif;
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
