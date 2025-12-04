@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <h1 class="text-2xl font-semibold mb-8">Admin Dashboard</h1>

            <!-- Top Section: Sales & Operations + Inventory & Menu -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- LEFT COLUMN: Sales & Operations -->
                <div class="space-y-6">
                    <h2 class="text-xl font-medium text-gray-800 border-b pb-3 mb-6">Sales & Operations</h2>

                    <!-- Today's Sales Card -->
                    <div class="bg-blue-100 p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-medium text-blue-800">Today's Sales</h2>
                                <p class="text-sm text-blue-600 mt-1">Total revenue for today</p>
                            </div>
                            <p class="text-3xl font-bold text-blue-900">₱{{ number_format($todaySales, 2) }}</p>
                        </div>
                    </div>

                    <!-- Today's Orders Card -->
                    <div class="bg-green-100 p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-medium text-green-800">Today's Orders</h2>
                                <p class="text-sm text-green-600 mt-1">Total orders placed today</p>
                            </div>
                            <p class="text-3xl font-bold text-green-900">{{ $todayOrders }}</p>
                        </div>
                    </div>

                    <!-- Active Orders Card -->
                    <div class="bg-yellow-100 p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-medium text-yellow-800">Active Orders</h2>
                                <p class="text-sm text-yellow-600 mt-1">Orders currently in progress</p>
                            </div>
                            <p class="text-3xl font-bold text-yellow-900">{{ $activeOrders }}</p>
                        </div>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Inventory & Menu -->
                <div class="space-y-6">
                    <h2 class="text-xl font-medium text-gray-800 border-b pb-3 mb-6">Inventory & Menu</h2>

                    <!-- Menu Items Card -->
                    <div class="bg-purple-100 p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center">
                            <div>
                                <h2 class="text-lg font-medium text-purple-800">Available Menu Items</h2>
                                <p class="text-sm text-purple-600 mt-1">Dishes currently available</p>
                            </div>
                            <p class="text-3xl font-bold text-purple-900">{{ $menuItemsCount }}</p>
                        </div>
                    </div>

                    <!-- Low Stock Items Card -->
                    <div class="bg-red-100 p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h2 class="text-lg font-medium text-red-800">Low Stock Items</h2>
                                <p class="text-sm text-red-600 mt-1">Items below reorder level</p>
                            </div>
                            <p class="text-3xl font-bold text-red-900">{{ $lowStockItems->count() }}</p>
                        </div>
                        @if($lowStockItems->count() > 0)
                            <div class="mt-4 bg-white rounded p-3 max-h-32 overflow-y-auto">
                                <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                                    @foreach($lowStockItems as $item)
                                        <li>{{ $item->name }} ({{ $item->quantity }} {{ $item->unit }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>

                    <!-- New Stock Items Card -->
                    <div class="bg-indigo-100 p-6 rounded-lg shadow">
                        <div class="flex justify-between items-center mb-3">
                            <div>
                                <h2 class="text-lg font-medium text-indigo-800">New Stock Items</h2>
                                <p class="text-sm text-indigo-600 mt-1">Added in last 7 days</p>
                            </div>
                            <p class="text-3xl font-bold text-indigo-900">{{ $newStockItems->count() }}</p>
                        </div>
                        @if($newStockItems->count() > 0)
                            <div class="mt-4 bg-white rounded p-3 max-h-32 overflow-y-auto">
                                <ul class="list-disc list-inside text-indigo-700 text-sm space-y-1">
                                    @foreach($newStockItems as $item)
                                        <li>{{ $item->name }} ({{ $item->created_at->format('M d') }})</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Top Selling Items & Recent Orders -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <!-- Top Selling Items -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Top Selling Items</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item</th>
                                    <th
                                        class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity Sold</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($topSellingItems as $item)
                                    <tr class="hover:bg-gray-50">
                                        <td class="py-3 px-4 text-sm text-gray-900">{{ $item->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">
                                            {{ $item->total_quantity }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="py-4 px-4 text-center text-gray-500 text-sm">No data available
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800">Recent Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order #</th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($recentOrders as $order)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="py-3 px-4 text-sm text-gray-900">#{{ $order->id }}</td>
                                                            <td class="py-3 px-4 text-sm">
                                                                <span
                                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                            {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' :
                                    ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                                    {{ ucfirst($order->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">
                                                                ₱{{ number_format($order->total_amount, 2) }}</td>
                                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="py-4 px-4 text-center text-gray-500 text-sm">No recent orders
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection