<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'BBQ-Lagao') }} - @yield('title')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @if(Request::routeIs('dashboard'))
        <link rel="stylesheet" href="{{ asset('dashboard.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('output.css') }}">
    @endif

    @stack('styles')
</head>
<body class="@yield('body-class')">
    @yield('content')

    @stack('scripts')
</body>
</html>