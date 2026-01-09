<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Inventory Management</title>

    <link rel="icon" href="{{ asset('logo_black.png') }}" type="image/png">

    <!-- Fonts -->
    <link href="{{ asset('fonts/fonts.css') }}" rel="stylesheet">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <!-- Navigation -->
        <nav x-data="{ open: false }" class="sticky top-0 z-50 bg-white border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <!-- Hamburger -->
                        <div class="-mr-2 flex items-center sm:hidden mr-4">
                            <button @click="open = ! open"
                                class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                        stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Logo -->
                        <div class="shrink-0 flex items-center">
                            <a href="{{ route('inventory.dashboard') }}">
                                <img src="{{ asset('logo_black.png') }}" style="width: 50px; height: 50px;"
                                    alt="logo_black" class="block h-9 w-auto fill-current text-gray-800" />
                            </a>
                        </div>

                        <!-- Navigation Links -->
                        <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
                            <a href="{{ route('inventory.dashboard') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('inventory.dashboard') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Inventory
                            </a>
                            <a href="{{ route('inventory.forecasting.index') }}"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->routeIs('inventory.forecasting.*') ? 'border-indigo-400 text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm font-medium leading-5 transition duration-150 ease-in-out">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                                Forecasting
                            </a>
                        </div>
                    </div>

                    <!-- Settings Dropdown -->
                    <div class="hidden sm:flex sm:items-center sm:ml-6">
                        <x-dropdown align="right" width="48">
                            <x-slot name="trigger">
                                <button
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-400"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <div>{{ Auth::user()->name }}</div>

                                    <div class="ml-1">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <x-dropdown-link :href="route('profile.edit')" class="flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    {{ __('Profile') }}
                                </x-dropdown-link>



                                <!-- Authentication -->
                                <form method="POST" action="{{ route('logout') }}" id="logout-form-inventory">
                                    @csrf
                                    <button type="button" id="logout-btn-inventory"
                                        class="block w-full text-left px-4 py-2 text-sm leading-5 text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                        </svg>
                                        {{ __('Log Out') }}
                                    </button>
                                </form>
                            </x-slot>
                        </x-dropdown>
                    </div>
                </div>
            </div>

            <!-- Responsive Navigation Menu -->
            <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
                <div class="pt-2 pb-3 space-y-1">
                    <x-responsive-nav-link :href="route('inventory.dashboard')"
                        :active="request()->routeIs('inventory.dashboard')">
                        {{ __('Inventory') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('inventory.stock-in-history')"
                        :active="request()->routeIs('inventory.stock-in-history')">
                        {{ __('Stock-In History') }}
                    </x-responsive-nav-link>

                    <x-responsive-nav-link :href="route('inventory.daily-report')"
                        :active="request()->routeIs('inventory.daily-report')">
                        {{ __('Daily Report') }}
                    </x-responsive-nav-link>
                </div>

                <!-- Responsive Settings Options -->
                <div class="pt-4 pb-1 border-t border-gray-200">
                    <div class="px-4">
                        <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                        <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
                    </div>

                    <div class="mt-3 space-y-1">
                        <x-responsive-nav-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-responsive-nav-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}" id="mobile-logout-form-inventory">
                            @csrf
                            <button type="button" onclick="document.getElementById('logout-btn-inventory').click()"
                                class="block w-full pl-3 pr-4 py-2 border-l-4 border-transparent text-left text-base font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 focus:outline-none focus:text-gray-800 focus:bg-gray-50 focus:border-gray-300 transition duration-150 ease-in-out">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        @include('components.offline-status-alert')

        <!-- Page Content -->
        <main>
            <div class="py-12">
                <div class="max-w-full mx-auto sm:px-6 lg:px-8">
                    @yield('content')
                </div>
            </div>
        </main>

        <!-- Alert Modal -->
        @include('components.alert-modal')

        <!-- Flash Messages -->
        @include('components.flash-messages')

        <!-- Shift Report Reminder Modal -->
        <div id="report-reminder-modal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-75 z-50">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-lg p-6 max-w-md w-full">
                    <h3 class="text-xl font-bold mb-4 text-gray-900">⚠️ Daily Report Required</h3>
                    <p class="mb-6 text-gray-700">You must submit a daily report before logging out. This ensures
                        proper end-of-day accountability.</p>
                    <a href="{{ route('inventory.daily-report') }}"
                        class="block w-full text-center bg-blue-600 text-white px-6 py-3 rounded hover:bg-blue-700 transition">
                        Submit Report Now
                    </a>
                </div>
            </div>
        </div>

        /*
        <script>
            // Logout interception for shift report check
            document.addEventListener('DOMContentLoaded', function () {
                const logoutBtn = document.getElementById('logout-btn-inventory');
                const logoutForm = document.getElementById('logout-form-inventory');
                const modal = document.getElementById('report-reminder-modal');

                if (logoutBtn) {
                    logoutBtn.addEventListener('click', function (e) {
                        e.preventDefault();

                        // Check if shift report exists
                        fetch('{{ route('shift-report.check') }}?_t=' + new Date().getTime())
                            .then(response => response.json())
                            .then(data => {
                                if (data.has_report) {
                                    // Allow logout
                                    logoutForm.submit();
                                } else {
                                    // Show modal
                                    modal.classList.remove('hidden');
                                }
                            })
                            .catch(error => {
                                console.error('Error checking shift report:', error);
                                // If error, show modal to be safe
                                modal.classList.remove('hidden');
                            });
                    });
                }
            });
        </script>
        */
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const logoutBtn = document.getElementById('logout-btn-inventory');
                const logoutForm = document.getElementById('logout-form-inventory');
                if (logoutBtn) {
                    logoutBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        logoutForm.submit();
                    });
                }
            });
        </script>
    </div>
</body>

</html>