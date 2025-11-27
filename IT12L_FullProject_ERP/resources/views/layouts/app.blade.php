<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'BBQ-Lagao') | BBQ-Lagao</title>

    <!-- Google Fonts - Poppins (matches your design) -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Load correct CSS based on current route -->
    @if(request()->routeIs('login') || request()->routeIs('register') || request()->routeIs('login.phone'))
        <link rel="stylesheet" href="{{ asset('css/output.css') }}">
    @elseif(request()->routeIs('dashboard'))
        <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    @else
        <link rel="stylesheet" href="{{ asset('css/output.css') }}">
    @endif

    <!-- Extra styles from views -->
    @stack('styles')

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body class="@yield('body-class', '')">

    @yield('content')

    <!-- Scripts from views (modals, etc.) -->
    @stack('scripts')
</body>
</html>