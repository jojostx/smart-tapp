<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="description" content="Reliable Parking Control System">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @hasSection('title')
    <title>@yield('title') - {{ config('app.name') }}</title>
    @else
    <title>{{ config('app.name') }}</title>
    @endif

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ url(asset('favicon.ico')) }}">

    <!-- Fonts -->
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=JetBrains+Mono:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ url(mix('css/app.css')) }}">
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    @livewireStyles

    <!-- Scripts -->
    @stack('scripts:head-start')
    <script src="{{ url(mix('js/app.js')) }}" defer></script>
    @stack('scripts:head-end')
</head>

<body class="font-sans">
    @stack('scripts:body-start')

    @yield('body')

    <x-alert :type="'info'" :hasCloseButton="true" :showAlert="false"></x-alert>
    <x-toast :type="'info'" :hasCloseButton="true" :showToast="false"></x-toast>

    @livewireScripts

    @stack('scripts:body-end')
</body>

</html>