@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Kitchen Display</h1>
                <div class="text-sm text-gray-500">
                    Auto-refreshes every 60 seconds
                </div>
            </div>

            @if($orders->isEmpty())
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <p class="text-blue-700">No orders to prepare at the moment.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($orders as $order)
                        <div class="bg-white rounded-lg shadow border p-4 {{ $order->status == 'new' ? 'border-blue-500' : 'border-yellow-500' }}"
                            id="order-card-{{ $order->id }}">
                            <div class="flex justify-between items-center mb-2">
                                <h2 class="text-lg font-semibold">
                                    Order #{{ $order->id }}
                                    <span id="order-status-{{ $order->id }}"
                                        class="ml-2 inline-flex text-xs leading-5 font-semibold items-center {{ $order->status == 'new' ? 'text-blue-600' : 'text-yellow-600' }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </h2>
                                <span class="text-sm text-gray-500">
                                    {{ $order->created_at->diffForHumans() }}
                                </span>
                            </div>

                            <div class="mb-2">
                                <span class="text-sm font-medium">
                                    {{ $order->table ? 'Table: ' . $order->table->name : 'Takeout' }}
                                </span>
                            </div>

                            <div class="border-t border-gray-200 pt-2 mt-2">
                                <h3 class="text-sm font-medium mb-2">Items:</h3>
                                <ul class="space-y-2">
                                    @foreach($order->orderItems as $item)
                                        <li class="flex justify-between">
                                            <div>
                                                <span class="font-medium">{{ $item->quantity }}x</span>
                                                {{ $item->menuItem->name }}
                                                @if($item->notes)
                                                    <p class="text-xs text-gray-500 mt-1">Note: {{ $item->notes }}</p>
                                                @endif
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-4 flex justify-end">
                                @if($order->status == 'new')
                                    <form action="{{ route('orders.update-status', $order) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="preparing">
                                        <button type="submit"
                                            class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center inline-flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9.879 16.121A3 3 0 1012.015 11L11 14H9c0 .768.293 1.536.879 2.121z" />
                                            </svg>
                                            Start Preparing
                                        </button>
                                    </form>
                                @elseif($order->status == 'preparing')
                                    <form action="{{ route('orders.update-status', $order) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="ready">
                                        <button type="submit"
                                            class="px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600 flex items-center inline-flex">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            Mark as Ready
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script>
        // Auto-refresh the page every 60 seconds
        setTimeout(function () {
            location.reload();
        }, 60000);
    </script>
@endsection