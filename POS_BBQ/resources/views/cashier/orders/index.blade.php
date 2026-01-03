@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

            {{-- ----------------------------------------- --}}
            <div class="flex justify-between items-center mb-6">

                <h1 class="text-2xl font-semibold">Orders</h1>

                <a href="{{ route('orders.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center relative z-20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Order
                </a>

            </div>
            {{-- ----------------------------------------- --}}

            {{-- Search Form --}}
            <div class="mb-6 relative z-20">
                <form action="{{ route('orders.index') }}" method="GET" class="flex gap-2">
                    <div class="flex-1 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            placeholder="Search by Order #, Customer Name, or Date (YYYY-MM-DD)"
                            class="w-full pl-10 rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Search
                    </button>
                    @if($search ?? false)
                        <a href="{{ route('orders.index') }}"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Clear
                        </a>
                    @endif
                </form>
            </div>
            {{-- ----------------------------------------- --}}

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- ----------------------------------------- --}}

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <!-- Card-Based Order Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @forelse($orders as $order)
                        <div
                            class="bg-white rounded-lg border shadow-sm relative group hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                            <!-- Link for entire card -->
                            <a href="{{ route('orders.show', $order) }}" class="absolute inset-0 z-10"></a>

                            <!-- Card Header -->
                            <div class="px-4 py-3 border-b bg-gray-50 rounded-t-lg flex justify-between items-center">
                                <div class="flex items-center">
                                    <span class="text-lg font-bold text-gray-800">#{{ $order->id }}</span>
                                    <span
                                        class="ml-2 inline-flex items-center text-xs font-medium {{ $order->order_type == 'dine-in' ? 'text-blue-600' : 'text-orange-600' }}">
                                        @if($order->order_type == 'dine-in')
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                            </svg>
                                        @endif
                                        {{ ucfirst($order->order_type) }}
                                    </span>
                                </div>
                                <!-- Removed Time as requested -->
                            </div>

                            <!-- Card Body -->
                            <div class="p-4">
                                <!-- Customer/Table Info -->
                                <div class="mb-3">
                                    <div class="text-sm text-gray-600">
                                        @if($order->order_type == 'dine-in')
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                                </svg>
                                                {{ $order->table ? $order->table->name : 'No Table' }}
                                            </span>
                                        @else
                                            <span class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-gray-400" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                </svg>
                                                {{ $order->customer_name ?: 'Walk-in Customer' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Badges -->
                                <div class="mb-3 space-y-2">
                                    <!-- Order Status -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 font-medium">Order Status:</span>
                                        <span class="inline-flex items-center text-xs font-semibold
                                                        {{ $order->status == 'completed' ? 'text-green-600' :
                    ($order->status == 'cancelled' ? 'text-red-600' :
                        ($order->status == 'new' ? 'text-blue-600' :
                            ($order->status == 'preparing' ? 'text-yellow-600' :
                                ($order->status == 'ready' ? 'text-indigo-600' : 'text-purple-600')))) }}">
                                            @if($order->status == 'completed')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            @elseif($order->status == 'cancelled')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            @elseif($order->status == 'new')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 4v16m8-8H4" />
                                                </svg>
                                            @elseif($order->status == 'preparing')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @elseif($order->status == 'ready')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>

                                    <!-- Payment Status -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600 font-medium">Payment Status:</span>
                                        <span
                                            class="inline-flex items-center text-xs font-semibold
                                                        {{ $order->payment_status == 'paid' ? 'text-green-600' :
                    ($order->payment_status == 'refunded' ? 'text-red-600' : 'text-yellow-600') }}">
                                            @if($order->payment_status == 'paid')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            @elseif($order->payment_status == 'refunded')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            @endif
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Date and Total Amount Row -->
                                <div class="flex justify-between items-end mt-4 pt-3 border-t">
                                    <div class="text-xs text-gray-500">
                                        {{ $order->created_at->format('M d, Y H:i') }}
                                    </div>
                                    <div class="text-xl font-bold text-gray-800">
                                        ₱{{ number_format($order->total_amount, 2) }}
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer - Actions -->
                            <!-- View button removed as card is clickable -->
                            @if($order->payment_status == 'pending')
                                <div class="px-4 py-3 bg-gray-50 rounded-b-lg flex justify-end items-center gap-2">
                                    <a href="{{ route('payments.create', ['order' => $order->id, 'source' => 'list']) }}"
                                        class="flex items-center px-3 py-1 text-sm text-white bg-purple-600 hover:bg-purple-700 rounded relative z-20">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Pay
                                    </a>
                                </div>
                            @endif
                        </div>
                @empty
                    <div class="col-span-full text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p class="text-lg font-medium">No orders found</p>
                        <p class="text-sm">Orders will appear here once created.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
@endsection