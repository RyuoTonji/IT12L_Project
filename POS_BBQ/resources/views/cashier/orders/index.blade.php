@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">

            {{-- ----------------------------------------- --}}
            <div class="flex justify-between items-center mb-6">

                <h1 class="text-2xl font-semibold">Orders</h1>

                <a href="{{ route('orders.create') }}"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    New Order
                </a>

            </div>
            {{-- ----------------------------------------- --}}

            {{-- Search Form --}}
            <div class="mb-6">
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

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order #</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Table/Customer</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Created</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($orders as $order)
                                        <tr>
                                            <td class="py-2 px-4">{{ $order->id }}</td>
                                            <td class="py-2 px-4">{{ ucfirst($order->order_type) }}</td>
                                            <td class="py-2 px-4">
                                                @if($order->order_type == 'dine-in')
                                                    {{ $order->table ? $order->table->name : 'N/A' }}
                                                @else
                                                    {{ $order->customer_name ?: 'Takeout' }}
                                                @endif
                                            </td>
                                            <td class="py-2 px-4">
                                                <span class="inline-flex text-xs leading-5 font-semibold items-center
                                                                                                                                                                                                                                                                {{ $order->status == 'completed' ? 'text-green-600' :
                            ($order->status == 'cancelled' ? 'text-red-600' :
                                ($order->status == 'new' ? 'text-blue-600' :
                                    ($order->status == 'preparing' ? 'text-yellow-600' :
                                        ($order->status == 'ready' ? 'text-indigo-600' : 'text-purple-600')))) }}">
                                                    @if($order->status == 'completed')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @elseif($order->status == 'cancelled')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    @elseif($order->status == 'new')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 4v16m8-8H4" />
                                                        </svg>
                                                    @elseif($order->status == 'preparing')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @elseif($order->status == 'ready')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                        </svg>
                                                    @endif
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="py-2 px-4">
                                                <span class="inline-flex text-xs leading-5 font-semibold items-center
                                                                                                                                                                                                                                                                {{ $order->payment_status == 'paid' ? 'text-green-600' :
                            ($order->payment_status == 'refunded' ? 'text-red-600' : 'text-yellow-600') }}">
                                                    @if($order->payment_status == 'paid')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @elseif($order->payment_status == 'refunded')
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                        </svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                    @endif
                                                    {{ ucfirst($order->payment_status) }}
                                                </span>
                                            </td>


                                            <td class="py-2 px-4 text-right">₱{{ number_format($order->total_amount, 2) }}</td>


                                            <td class="py-2 px-4 text-center">{{ $order->created_at->format('M d, Y H:i') }}</td>


                                            <td class="py-2 px-4 text-center">
                                                <div class="flex justify-center items-center space-x-2">
                                                    <a href="{{ route('orders.show', $order) }}"
                                                        class="text-blue-600 hover:text-blue-900 flex items-center" title="View">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View
                                                    </a>

                                                    @if($order->payment_status == 'pending')
                                                        <a href="{{ route('payments.create', ['order' => $order->id, 'source' => 'list']) }}"
                                                            class="text-purple-600 hover:text-purple-900 flex items-center" title="Payment">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                                            </svg>
                                                            Payment
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>

                                        </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500">No orders found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $orders->links() }}
            </div>

        </div>
    </div>
@endsection