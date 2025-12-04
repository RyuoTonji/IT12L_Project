@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-2xl font-semibold">{{ $branch->name }} Operations</h1>
                    <p class="text-gray-600">{{ $branch->code }} - {{ $branch->address }}</p>
                </div>
                <a href="{{ route('admin.branches.index') }}" class="text-blue-600 hover:text-blue-800">
                    ← Back to Branches
                </a>
            </div>

            <!-- Branch Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-blue-100 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-blue-800">Today's Sales</h3>
                    <p class="text-3xl font-bold text-blue-900 mt-2">₱{{ number_format($todaySales, 2) }}</p>
                </div>
                <div class="bg-green-100 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-green-800">Today's Orders</h3>
                    <p class="text-3xl font-bold text-green-900 mt-2">{{ $todayOrders }}</p>
                </div>
                <div class="bg-yellow-100 p-6 rounded-lg shadow">
                    <h3 class="text-lg font-medium text-yellow-800">Active Orders</h3>
                    <p class="text-3xl font-bold text-yellow-900 mt-2">{{ $activeOrders->count() }}</p>
                </div>
            </div>

            <!-- Tabs for different operations -->
            <div x-data="{ activeTab: 'orders' }" class="mt-8">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'orders'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'orders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'orders' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Ordering
                        </button>
                        <button @click="activeTab = 'kitchen'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'kitchen', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'kitchen' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Kitchen Display
                        </button>
                        <button @click="activeTab = 'menu'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'menu', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'menu' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Available Menu
                        </button>
                    </nav>
                </div>

                <!-- Orders Tab -->
                <div x-show="activeTab === 'orders'" class="mt-6">
                    <h2 class="text-xl font-semibold mb-4">Recent Orders</h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Table</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($recentOrders as $order)
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                #{{ $order->id }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $order->table->name ?? 'N/A' }}</td>
                                                            <td class="px-6 py-4 text-sm text-gray-500">{{ $order->orderItems->count() }} items</td>
                                                            <td class="px-6 py-4 whitespace-nowrap">
                                                                <span
                                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                                            {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' :
                                    ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                                    {{ ucfirst($order->status) }}
                                                                </span>
                                                            </td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                ₱{{ number_format($order->total_amount, 2) }}</td>
                                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                {{ $order->created_at->format('h:i A') }}</td>
                                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 text-center text-gray-500">No recent orders</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Kitchen Display Tab -->
                <div x-show="activeTab === 'kitchen'" class="mt-6">
                    <h2 class="text-xl font-semibold mb-4">Active Orders in Kitchen</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @forelse($activeOrders as $order)
                            <div class="bg-white border-2 border-orange-500 rounded-lg p-4 shadow">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h3 class="text-lg font-bold">Order #{{ $order->id }}</h3>
                                        <p class="text-sm text-gray-600">{{ $order->table->name ?? 'N/A' }}</p>
                                    </div>
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </div>
                                <div class="space-y-2">
                                    @foreach($order->orderItems as $item)
                                        <div class="flex justify-between text-sm">
                                            <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                                        </div>
                                    @endforeach
                                </div>
                                <div class="mt-3 pt-3 border-t border-gray-200 text-xs text-gray-500">
                                    {{ $order->created_at->diffForHumans() }}
                                </div>
                            </div>
                        @empty
                            <div class="col-span-3 text-center text-gray-500 py-8">
                                No active orders in kitchen
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Menu Tab -->
                <div x-show="activeTab === 'menu'" class="mt-6">
                    <h2 class="text-xl font-semibold mb-4">Available Menu Items</h2>
                    @forelse($menuItems as $categoryName => $items)
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-800 mb-3">{{ $categoryName }}</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach($items as $item)
                                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                                        <h4 class="font-semibold text-gray-900">{{ $item->name }}</h4>
                                        <p class="text-sm text-gray-600 mt-1">{{ $item->description }}</p>
                                        <p class="text-lg font-bold text-blue-600 mt-2">₱{{ number_format($item->price, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 py-8">No menu items available for this branch</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection