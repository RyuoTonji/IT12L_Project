@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">
                    {{ $table->name }}
                    <span class="text-sm font-normal ml-2 px-2 py-1 rounded
                            {{ $table->status == 'available' ? 'bg-green-100 text-green-800' :
        ($table->status == 'occupied' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                        {{ ucfirst($table->status) }}
                    </span>
                </h1>
                <div>
                    <a href="{{ route('tables.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Tables
                    </a>
                    @if(!$activeOrder)
                        <a href="{{ route('orders.create', ['table_id' => $table->id]) }}"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Order
                        </a>
                    @endif
                </div>
            </div>

            @if($activeOrder)
                <div class="mb-6">
                    <h2 class="text-xl font-medium mb-4">Current Order #{{ $activeOrder->id }}</h2>
                    <div class="bg-white rounded-lg shadow border p-4">
                        <div class="flex justify-between mb-4">
                            <div>
                                <p><strong>Status:</strong>
                                    <span
                                        class="px-2 py-1 rounded text-sm
                                                    {{ $activeOrder->status == 'new' ? 'bg-blue-100 text-blue-800' :
                ($activeOrder->status == 'preparing' ? 'bg-yellow-100 text-yellow-800' :
                    ($activeOrder->status == 'ready' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800')) }}">
                                        {{ ucfirst($activeOrder->status) }}
                                    </span>
                                </p>
                                <p><strong>Created:</strong> {{ $activeOrder->created_at->format('M d, Y H:i') }}</p>
                            </div>
                            <div>
                                <p><strong>Total:</strong> ₱{{ number_format($activeOrder->total_amount, 2) }}</p>
                                <p><strong>Payment:</strong>
                                    <span
                                        class="px-2 py-1 rounded text-sm
                                                    {{ $activeOrder->payment_status == 'paid' ? 'bg-green-100 text-green-800' :
                ($activeOrder->payment_status == 'refunded' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($activeOrder->payment_status) }}
                                    </span>
                                </p>
                            </div>
                        </div>

                        <h3 class="font-medium mb-2">Order Items</h3>
                        <table class="min-w-full">
                            <thead>
                                <tr>
                                    <th
                                        class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item</th>
                                    <th
                                        class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Qty</th>
                                    <th
                                        class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price</th>
                                    <th
                                        class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeOrder->orderItems as $item)
                                    <tr>
                                        <td class="py-2 px-4 border-b">{{ $item->menuItem->name }}</td>
                                        <td class="py-2 px-4 border-b text-center">{{ $item->quantity }}</td>
                                        <td class="py-2 px-4 border-b text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                        <td class="py-2 px-4 border-b text-right">
                                            ₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="py-2 px-4 text-right font-bold">Total:</td>
                                    <td class="py-2 px-4 text-right font-bold">
                                        ₱{{ number_format($activeOrder->total_amount, 2) }}</td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="mt-4 flex justify-end space-x-2">
                            <a href="{{ route('orders.edit', $activeOrder) }}"
                                class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Order
                            </a>
                            @if($activeOrder->payment_status != 'paid')
                                <a href="{{ route('payments.create', ['order' => $activeOrder->id]) }}"
                                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center inline-flex">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Process Payment
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @else
                <div class="bg-blue-50 p-4 rounded-lg mb-6">
                    <p class="text-blue-700">This table is currently {{ $table->status }}. Create a new order to occupy it.</p>
                </div>
            @endif
        </div>
    </div>
@endsection