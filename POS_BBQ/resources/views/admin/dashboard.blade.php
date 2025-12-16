@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <h1 class="text-2xl font-semibold mb-8">Admin Dashboard - Multi-Branch Overview</h1>

            <!-- Combined Analytics Section -->
            <div class="mb-8">
                <h2 class="text-xl font-medium text-gray-800 border-b pb-3 mb-6">Combined Analytics (All Branches)</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                    <!-- Total Sales (All Time) -->
                    <div class="bg-indigo-100 p-6 rounded-lg shadow cursor-pointer hover:bg-indigo-200 transition"
                        onclick="openModal('salesCombinedModal')">
                        <h3 class="text-lg font-medium text-indigo-800">Total Sales (All Time)</h3>
                        <p class="text-3xl font-bold text-indigo-900 mt-2 text-right">₱{{ number_format($totalSales, 2) }}
                        </p>
                    </div>

                    <!-- Total Orders (All Time) -->
                    <div class="bg-teal-100 p-6 rounded-lg shadow cursor-pointer hover:bg-teal-200 transition"
                        onclick="openModal('ordersCombinedModal')">
                        <h3 class="text-lg font-medium text-teal-800">Total Orders (All Time)</h3>
                        <p class="text-3xl font-bold text-teal-900 mt-2 text-right">{{ $totalOrders }}</p>
                    </div>

                    <!-- Total Lost (All Time) -->
                    <div class="bg-red-100 p-6 rounded-lg shadow cursor-pointer hover:bg-red-200 transition"
                        onclick="openModal('lostCombinedModal')">
                        <h3 class="text-lg font-medium text-red-800">Total Lost (All Time)</h3>
                        <p class="text-3xl font-bold text-red-900 mt-2 text-right">{{ $totalLostCombined }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-blue-100 p-6 rounded-lg shadow cursor-pointer hover:bg-blue-200 transition"
                        onclick="openModal('salesDailyModal')">
                        <h3 class="text-lg font-medium text-blue-800">
                            Total Sales
                            @if($filterDate)
                                ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }})
                            @else
                                Today
                            @endif
                        </h3>
                        <p class="text-3xl font-bold text-blue-900 mt-2 text-right">₱{{ number_format($todaySales, 2) }}</p>
                        <div class="mt-3 text-sm text-blue-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Branch 1:</span>
                                <span class="font-semibold">₱{{ number_format($branch1Sales, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Branch 2:</span>
                                <span class="font-semibold">₱{{ number_format($branch2Sales, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-green-100 p-6 rounded-lg shadow cursor-pointer hover:bg-green-200 transition"
                        onclick="openModal('ordersDailyModal')">
                        <h3 class="text-lg font-medium text-green-800">
                            Total Orders
                            @if($filterDate)
                                ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }})
                            @else
                                Today
                            @endif
                        </h3>
                        <p class="text-3xl font-bold text-green-900 mt-2 text-right">{{ $todayOrders }}</p>
                        <div class="mt-3 text-sm text-green-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Branch 1:</span>
                                <span class="font-semibold">{{ $branch1Orders }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Branch 2:</span>
                                <span class="font-semibold">{{ $branch2Orders }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Moved Refunds Card Here -->
                    <div class="bg-pink-100 p-6 rounded-lg shadow cursor-pointer hover:bg-pink-200 transition"
                        onclick="openModal('refundsModal')">
                        <h3 class="text-lg font-medium text-pink-800 flex justify-between items-center">
                            Total Refunds
                            @if($filterDate)
                                ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }})
                            @endif
                        </h3>
                        <p class="text-3xl font-bold text-pink-900 mt-2 text-right">{{ $refundsCount }}</p>
                        <p class="text-sm font-semibold text-pink-700 mt-1 text-right">₱{{ number_format($refundsCost, 2) }}
                        </p>
                    </div>

                    <div class="bg-yellow-100 p-6 rounded-lg shadow cursor-pointer hover:bg-yellow-200 transition"
                        onclick="openModal('activeOrdersModal')">
                        <h3 class="text-lg font-medium text-yellow-800">Active Orders</h3>
                        <p class="text-3xl font-bold text-yellow-900 mt-2 text-right">{{ $activeOrdersCount }}</p>
                        <div class="mt-3 text-sm text-yellow-700 space-y-1">
                            <div class="flex justify-between">
                                <span>Branch 1:</span>
                                <span class="font-semibold">{{ $branch1ActiveOrders }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Branch 2:</span>
                                <span class="font-semibold">{{ $branch2ActiveOrders }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="mb-8">
                <div class="flex flex-col md:flex-row justify-between items-center border-b pb-3 mb-6">
                    <h2 class="text-xl font-medium text-gray-800">Analytics Charts</h2>
                    <form action="{{ route('admin.dashboard') }}" method="GET"
                        class="flex items-center space-x-4 mt-4 md:mt-0">
                        <div class="flex items-center space-x-2">
                            <label for="date" class="text-sm text-gray-600">Date:</label>
                            <input type="date" name="date" id="date" value="{{ $filterDate }}"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                onchange="this.form.submit()">
                        </div>
                    </form>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-6">
                    <!-- Sales Chart -->
                    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">
                            Sales Comparison
                            @if($filterDate)
                                ({{ \Carbon\Carbon::parse($filterDate)->format('M d, Y') }})
                            @else
                                (Last 7 Days)
                            @endif
                        </h3>
                        <div id="salesChart"></div>
                    </div>

                    <!-- Orders Chart -->
                    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                        <h3 class="text-lg font-semibold mb-4 text-gray-800">
                            Orders Comparison
                            @if($filterDate)
                                ({{ \Carbon\Carbon::parse($filterDate)->format('M d, Y') }})
                            @else
                                (Last 7 Days)
                            @endif
                        </h3>
                        <div id="ordersChart"></div>
                    </div>
                </div>

                <!-- Inventory Chart (Full Width) -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h3 class="text-lg font-semibold mb-4 text-gray-800">
                        Inventory Tracking
                        @if($filterDate)
                            ({{ \Carbon\Carbon::parse($filterDate)->format('M d, Y') }})
                        @else
                            (Last 7 Days)
                        @endif
                    </h3>
                    <div id="inventoryChart"></div>
                </div>
            </div>

            <!-- Branch-Specific Sections -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Branch 1 Section -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-blue-900">Branch 1</h2>
                        <a href="{{ route('admin.branches.show', 1) }}"
                            class="text-blue-600 hover:text-blue-800 text-sm font-medium flex items-center inline-flex">
                            View Details
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">
                                Sales (@if($filterDate) {{ \Carbon\Carbon::parse($filterDate)->format('M d') }} @else Today
                                @endif)
                            </p>
                            <p class="text-2xl font-bold text-blue-900 text-right">₱{{ number_format($branch1Sales, 2) }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">
                                Orders (@if($filterDate) {{ \Carbon\Carbon::parse($filterDate)->format('M d') }} @else Today
                                @endif)
                            </p>
                            <p class="text-2xl font-bold text-green-900 text-right">{{ $branch1Orders }}</p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">Available Menu Items</p>
                            <p class="text-2xl font-bold text-purple-900 text-right">{{ $branch1MenuCount }}</p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">Low Stock Items</p>
                            <p class="text-2xl font-bold text-red-900 text-right">{{ $branch1LowStock }}</p>
                        </div>
                    </div>
                </div>

                <!-- Branch 2 Section -->
                <div class="bg-gradient-to-br from-green-50 to-green-100 p-6 rounded-lg shadow-md">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-green-900">Branch 2</h2>
                        <a href="{{ route('admin.branches.show', 2) }}"
                            class="text-green-600 hover:text-green-800 text-sm font-medium flex items-center inline-flex">
                            View Details
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                    <div class="space-y-3">
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">
                                Sales (@if($filterDate) {{ \Carbon\Carbon::parse($filterDate)->format('M d') }} @else Today
                                @endif)
                            </p>
                            <p class="text-2xl font-bold text-blue-900 text-right">₱{{ number_format($branch2Sales, 2) }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">
                                Orders (@if($filterDate) {{ \Carbon\Carbon::parse($filterDate)->format('M d') }} @else Today
                                @endif)
                            </p>
                            <p class="text-2xl font-bold text-green-900 text-right">{{ $branch2Orders }}</p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">Available Menu Items</p>
                            <p class="text-2xl font-bold text-purple-900 text-right">{{ $branch2MenuCount }}</p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">Low Stock Items</p>
                            <p class="text-2xl font-bold text-red-900 text-right">{{ $branch2LowStock }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory & Menu Overview + NEW METRICS -->
            <div class="mb-8">
                <h2 class="text-xl font-medium text-gray-800 border-b pb-3 mb-6">Inventory & Menu Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Total Menu Items -->
                    <div class="bg-purple-100 p-6 rounded-lg shadow cursor-pointer hover:bg-purple-200 transition"
                        onclick="openModal('menuItemsModal')">
                        <h3 class="text-lg font-medium text-purple-800 flex justify-between items-center">
                            Total Menu Items
                        </h3>
                        <p class="text-3xl font-bold text-purple-900 mt-2 text-right">{{ $menuItemsCount }}</p>
                    </div>

                    <!-- Low Stock Items -->
                    <div class="bg-red-100 p-6 rounded-lg shadow cursor-pointer hover:bg-red-200 transition"
                        onclick="openModal('lowStockModal')">
                        <h3 class="text-lg font-medium text-red-800 flex justify-between items-center">
                            Low Stock Items
                        </h3>
                        <p class="text-3xl font-bold text-red-900 mt-2 text-right">{{ $lowStockItems->count() }}</p>
                    </div>

                    <!-- New Stock Items -->
                    <div class="bg-indigo-100 p-6 rounded-lg shadow cursor-pointer hover:bg-indigo-200 transition"
                        onclick="openModal('newStockModal')">
                        <h3 class="text-lg font-medium text-indigo-800 flex justify-between items-center">
                            New Stock Items
                        </h3>
                        <p class="text-3xl font-bold text-indigo-900 mt-2 text-right">{{ $newStockItems->count() }}</p>
                    </div>

                    <!-- Spoilage -->
                    <div class="bg-orange-300 p-6 rounded-lg shadow cursor-pointer hover:bg-orange-400 transition"
                        style="background-color: #fdba74;" onclick="openModal('spoilageModal')">
                        <h3 class="text-lg font-medium text-orange-900 flex justify-between items-center">
                            Spoilage
                        </h3>
                        <p class="text-3xl font-bold text-orange-900 mt-2 text-right">{{ $spoilageCount }}</p>
                    </div>

                    <!-- Lost Items -->
                    <div class="bg-gray-300 p-6 rounded-lg shadow cursor-pointer hover:bg-gray-400 transition"
                        style="background-color: #d1d5db;" onclick="openModal('lostModal')">
                        <h3 class="text-lg font-medium text-gray-800 flex justify-between items-center">
                            Lost Items
                        </h3>
                        <p class="text-3xl font-bold text-gray-900 mt-2 text-right">{{ $lostCount }}</p>
                    </div>

                    <!-- Returns -->
                    <div class="bg-blue-100 p-6 rounded-lg shadow cursor-pointer hover:bg-blue-200 transition"
                        onclick="openModal('returnsModal')">
                        <h3 class="text-lg font-medium text-blue-800 flex justify-between items-center">
                            Returns (Stock)
                        </h3>
                        <p class="text-3xl font-bold text-blue-900 mt-2 text-right">{{ $returnsCount }}</p>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Top Selling Items & Recent Orders -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-8">
                <!-- Top Selling Items -->
                <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500 mr-2" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                        </svg>
                        Top Selling Items
                        @if($filterDate)
                            ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }})
                        @endif
                    </h2>
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
                                    <tr class="hover:bg-gray-50 cursor-pointer transition"
                                        onclick="openModal('topSellingModal-{{ $item->id }}')">
                                        <td class="py-3 px-4 text-sm text-gray-900">{{ $item->name }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">
                                            {{ $item->total_quantity }}
                                        </td>
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
                    <h2 class="text-xl font-semibold mb-4 text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-800" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Recent Orders
                        @if($filterDate)
                            ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }})
                        @endif
                    </h2>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order #</th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Branch</th>
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
                                    <tr class="hover:bg-gray-50 cursor-pointer" onclick="openOrderModal({{ $order->id }})">
                                        <td class="py-3 px-4 text-sm text-gray-900">#{{ $order->id }}</td>
                                        <td class="py-3 px-4 text-sm text-gray-600">{{ $order->branch->name ?? 'N/A' }}</td>
                                        <td class="py-3 px-4 text-sm">
                                            @php
                                                $statusColors = [
                                                    'pending' => 'text-yellow-600',
                                                    'preparing' => 'text-orange-600',
                                                    'ready_for_pickup' => 'text-indigo-600',
                                                    'served' => 'text-blue-600',
                                                    'completed' => 'text-green-600',
                                                    'cancelled' => 'text-red-600',
                                                ];
                                                $colorClass = $statusColors[$order->status] ?? 'text-gray-600';
                                            @endphp
                                            <span
                                                class="inline-flex items-center text-xs leading-5 font-semibold {{ $colorClass }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 text-sm text-gray-900 text-right font-medium">
                                            ₱{{ number_format($order->total_amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="py-4 px-4 text-center text-gray-500 text-sm">No recent orders
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

    <!-- Modals Section -->
    <!-- Template for Layout: Title | Date Form ...... | Close X  -->

    <!-- Top Selling Item Modals -->
    @foreach($topSellingItems as $item)
        <div id="topSellingModal-{{ $item->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-hidden="true">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    onclick="closeModal('topSellingModal-{{ $item->id }}')"></div>
                <div class="relative bg-white rounded-lg shadow-xl transform transition-all max-w-6xl w-full p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex items-center space-x-4">
                            <h3 class="text-lg font-bold text-gray-900">{{ $item->name }}</h3>
                            <!-- In-Modal Date Filter (Away from X) -->
                            <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                                <input type="hidden" name="modal" value="topSellingModal-{{ $item->id }}">
                                <input type="date" name="date" value="{{ $filterDate }}"
                                    class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1"
                                    onchange="this.form.submit()">
                            </form>
                        </div>
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            onclick="closeModal('topSellingModal-{{ $item->id }}')">
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <!-- Details -->
                        <div>
                            <h4 class="font-semibold mb-2">Branch Breakdown</h4>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200" id="table-topSelling-{{ $item->id }}">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Branch
                                            </th>
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qty
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($item->branch_breakdown as $branch)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    {{ $branch->branch_name }}
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 text-right">
                                                    {{ $branch->quantity }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <div id="pagination-topSelling-{{ $item->id }}"
                                    class="mt-4 flex justify-between items-center hidden"></div>
                            </div>
                        </div>
                        <!-- Chart -->
                        <div class="h-80">
                            <div id="topSellingChart-{{ $item->id }}" class="w-full h-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- MODAL COMPONENT MACRO (To reduce repetition would be nice, but writing out for clarity and safety) -->

    <!-- NEW MODALS -->

    <!-- A. Combined Sales Modal -->
    <div id="salesCombinedModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('salesCombinedModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Total Sales (All Time)</h3>
                    </div>
                    <button onclick="closeModal('salesCombinedModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-salesCombined">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Branch</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4">Branch 1</td>
                                    <td class="text-right text-blue-900 font-bold">
                                        ₱{{ number_format($branch1SalesAllTime, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">Branch 2</td>
                                    <td class="text-right text-blue-900 font-bold">
                                        ₱{{ number_format($branch2SalesAllTime, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="h-80">
                        <div id="salesCombinedChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- B. Combined Orders Modal -->
    <div id="ordersCombinedModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('ordersCombinedModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Total Orders (All Time)</h3>
                    </div>
                    <button onclick="closeModal('ordersCombinedModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-ordersCombined">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Branch</th>
                                    <th class="text-right">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4">Branch 1</td>
                                    <td class="text-right text-teal-900 font-bold">{{ $branch1OrdersAllTime }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">Branch 2</td>
                                    <td class="text-right text-teal-900 font-bold">{{ $branch2OrdersAllTime }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="h-80">
                        <div id="ordersCombinedChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- C. Combined Lost Modal -->
    <div id="lostCombinedModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('lostCombinedModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Total Lost (All Time)</h3>
                    </div>
                    <button onclick="closeModal('lostCombinedModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-lostCombined">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Branch</th>
                                    <th class="text-right">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4">Branch 1</td>
                                    <td class="text-right text-red-900 font-bold">{{ $branch1LostAllTime }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">Branch 2</td>
                                    <td class="text-right text-red-900 font-bold">{{ $branch2LostAllTime }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="h-80">
                        <div id="lostCombinedChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- D. Daily Sales Modal -->
    <div id="salesDailyModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('salesDailyModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Total Sales @if($filterDate)
                        ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }}) @else (Today) @endif</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="salesDailyModal">
                            <input type="date" name="date" value="{{ $filterDate }}"
                                class="text-sm border-gray-300 rounded-md shadow-sm py-1" onchange="this.form.submit()">
                        </form>
                    </div>
                    <button onclick="closeModal('salesDailyModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-salesDaily">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Branch</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4">Branch 1</td>
                                    <td class="text-right text-blue-900 font-bold">₱{{ number_format($branch1Sales, 2) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">Branch 2</td>
                                    <td class="text-right text-blue-900 font-bold">₱{{ number_format($branch2Sales, 2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="h-80">
                        <div id="salesDailyChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- E. Daily Orders Modal -->
    <div id="ordersDailyModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('ordersDailyModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Total Orders @if($filterDate)
                        ({{ \Carbon\Carbon::parse($filterDate)->format('M d') }}) @else (Today) @endif</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="ordersDailyModal">
                            <input type="date" name="date" value="{{ $filterDate }}"
                                class="text-sm border-gray-300 rounded-md shadow-sm py-1" onchange="this.form.submit()">
                        </form>
                    </div>
                    <button onclick="closeModal('ordersDailyModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-ordersDaily">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Branch</th>
                                    <th class="text-right">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4">Branch 1</td>
                                    <td class="text-right text-green-900 font-bold">{{ $branch1Orders }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">Branch 2</td>
                                    <td class="text-right text-green-900 font-bold">{{ $branch2Orders }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="h-80">
                        <div id="ordersDailyChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- F. Active Orders Modal -->
    <div id="activeOrdersModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('activeOrdersModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Active Orders</h3>
                        <!-- No Date Filter for Active Orders usually, but keeping form for consistency if needed or removal -->
                    </div>
                    <button onclick="closeModal('activeOrdersModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-activeOrders">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Branch</th>
                                    <th class="text-right">Count</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td class="px-6 py-4">Branch 1</td>
                                    <td class="text-right text-yellow-900 font-bold">{{ $branch1ActiveOrders }}</td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4">Branch 2</td>
                                    <td class="text-right text-yellow-900 font-bold">{{ $branch2ActiveOrders }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="h-80">
                        <div id="activeOrdersChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 1. Menu Items Modal -->
    <div id="menuItemsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('menuItemsModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Menu Items</h3>
                        <!-- Date Filter (Not really applicable for Total Menu Items, but keeping for consistency/future) -->
                    </div>
                    <button onclick="closeModal('menuItemsModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-menuItems">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category
                                    </th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Count</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($menuItemsByCategory as $cat) <tr>
                                    <td class="px-6 py-4 text-sm">{{ $cat['name'] }}</td>
                                    <td class="px-6 py-4 text-sm text-right">{{ $cat['count'] }}</td>
                                </tr> @endforeach
                            </tbody>
                        </table>
                        <div id="pagination-menuItems" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="menuItemsChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. Low Stock Modal -->
    <div id="lowStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('lowStockModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Low Stock Items</h3>
                    </div>
                    <button onclick="closeModal('lowStockModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-lowStock">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Item</th>
                                    <th>Branch</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($lowStockItems as $item) <tr>
                                    <td class="px-6 py-4">{{ $item->name }}</td>
                                    <td>{{ $item->branch->name ?? 'N/A' }}</td>
                                    <td class="text-right text-red-600 font-bold">{{ $item->quantity }}</td>
                                </tr> @endforeach
                            </tbody>
                        </table>
                        <div id="pagination-lowStock" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="lowStockChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. New Stock Modal -->
    <div id="newStockModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('newStockModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">New Stock Items (Last 7 Days)</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="newStockModal">
                            <input type="date" name="date" value="{{ $filterDate }}"
                                class="text-sm border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 py-1"
                                onchange="this.form.submit()">
                        </form>
                    </div>
                    <button onclick="closeModal('newStockModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-newStock">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Item</th>
                                    <th>Branch</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($newStockItems as $item) <tr>
                                    <td class="px-6 py-4">{{ $item->name }}</td>
                                    <td>{{ $item->branch->name ?? 'N/A' }}</td>
                                    <td class="text-right text-green-600 font-bold">{{ $item->quantity }}</td>
                                </tr> @endforeach
                            </tbody>
                        </table>
                        <div id="pagination-newStock" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="newStockChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. Spoilage Modal -->
    <div id="spoilageModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('spoilageModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Spoilage</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="spoilageModal">
                            <input type="date" name="date" value="{{ $filterDate }}" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded py-1">
                        </form>
                    </div>
                    <button onclick="closeModal('spoilageModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-spoilage">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left">Item</th>
                                    <th>Branch</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spoilageItems as $item) <tr>
                                    <td class="px-6 py-4">{{ $item->inventory->name ?? 'Unknown' }}</td>
                                    <td>{{ $item->inventory->branch->name ?? 'N/A' }}</td>
                                    <td class="text-right">{{ $item->quantity }}</td>
                                </tr> @endforeach
                            </tbody>
                        </table>
                        <div id="pagination-spoilage" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="spoilageChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Lost Items Modal -->
    <div id="lostModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('lostModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Lost Items</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="lostModal">
                            <input type="date" name="date" value="{{ $filterDate }}" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded py-1">
                        </form>
                    </div>
                    <button onclick="closeModal('lostModal')" class="text-gray-400 hover:text-gray-500"><svg class="h-6 w-6"
                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-lost">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-3">Item</th>
                                    <th>Branch</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>@foreach($lostItems as $item)<tr>
                                <td class="px-6 py-4">{{ $item->inventory->name ?? 'Unknown' }}</td>
                                <td>{{ $item->inventory->branch->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                            </tr>@endforeach</tbody>
                        </table>
                        <div id="pagination-lost" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="lostChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 6. Returns Modal -->
    <div id="returnsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('returnsModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Returns (Stock)</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="returnsModal">
                            <input type="date" name="date" value="{{ $filterDate }}" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded py-1">
                        </form>
                    </div>
                    <button onclick="closeModal('returnsModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-returns">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-3">Item</th>
                                    <th>Branch</th>
                                    <th class="text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody>@foreach($returnItems as $item)<tr>
                                <td class="px-6 py-4">{{ $item->inventory->name ?? 'Unknown' }}</td>
                                <td>{{ $item->inventory->branch->name ?? 'N/A' }}</td>
                                <td class="text-right">{{ $item->quantity }}</td>
                            </tr>@endforeach</tbody>
                        </table>
                        <div id="pagination-returns" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="returnsChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 7. Refunds Modal -->
    <div id="refundsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeModal('refundsModal')"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-6xl w-full p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center space-x-4">
                        <h3 class="text-lg font-bold text-gray-900">Refunds (Orders)</h3>
                        <form action="{{ route('admin.dashboard') }}" method="GET" class="flex items-center">
                            <input type="hidden" name="modal" value="refundsModal">
                            <input type="date" name="date" value="{{ $filterDate }}" onchange="this.form.submit()"
                                class="text-sm border-gray-300 rounded py-1">
                        </form>
                    </div>
                    <button onclick="closeModal('refundsModal')" class="text-gray-400 hover:text-gray-500"><svg
                            class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <table class="min-w-full divide-y divide-gray-200" id="table-refunds">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="text-left px-6 py-3">Order #</th>
                                    <th>Branch</th>
                                    <th class="text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody>@foreach($refundItems as $item)<tr>
                                <td class="px-6 py-4">#{{ $item->id }}</td>
                                <td>{{ $item->branch->name ?? 'N/A' }}</td>
                                <td class="text-right text-pink-600 font-bold">
                                    ₱{{ number_format($item->total_amount, 2) }}</td>
                            </tr>@endforeach</tbody>
                        </table>
                        <div id="pagination-refunds" class="mt-4 hidden flex justify-between"></div>
                    </div>
                    <div class="h-80">
                        <div id="refundsChart" class="w-full h-full"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Order Details Modals (Existing) -->
    @foreach($recentOrders as $order)
        <div id="order-modal-{{ $order->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    onclick="closeOrderModal({{ $order->id }})"></div>
                <div class="relative bg-white rounded-lg shadow-xl transform transition-all max-w-md w-full z-10">
                    <div class="absolute top-0 right-0 pt-4 pr-4 z-20">
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            onclick="closeOrderModal({{ $order->id }})">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="bg-white px-6 py-6 rounded-lg">
                        @include('admin.orders.partials.details', ['order' => $order])
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // --- Helper to safely init chart ---
            function initChart(selector, options) {
                const el = document.querySelector(selector);
                if (el) {
                    new ApexCharts(el, options).render();
                }
            }

            // --- Existing Main Charts ---
            const salesChartData = @json($salesChartData ?? []);
            if (salesChartData.length > 0) {
                const salesOptions = {
                    series: [{ name: 'Branch 1', data: salesChartData.map(d => d.branch1) }, { name: 'Branch 2', data: salesChartData.map(d => d.branch2) }],
                    chart: { type: 'area', height: 350, toolbar: { show: true } }, dataLabels: { enabled: false }, stroke: { curve: 'smooth', width: 2 }, xaxis: { categories: salesChartData.map(d => d.date) }, colors: ['#3B82F6', '#10B981'], fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.7, opacityTo: 0.2 } }
                };
                initChart("#salesChart", salesOptions);
            }

            const ordersChartData = @json($ordersChartData ?? []);
            if (ordersChartData.length > 0) {
                const ordersOptions = {
                    series: [{ name: 'Branch 1', data: ordersChartData.map(d => d.branch1) }, { name: 'Branch 2', data: ordersChartData.map(d => d.branch2) }],
                    chart: { type: 'bar', height: 350 }, colors: ['#3B82F6', '#10B981'], xaxis: { categories: ordersChartData.map(d => d.date) }
                };
                initChart("#ordersChart", ordersOptions);
            }

            const inventoryData = @json($inventoryChartData ?? null);
            if (inventoryData) {
                const inventoryOptions = {
                    series: [{ name: 'Stock In', data: [inventoryData.branch1.stock_in, inventoryData.branch2.stock_in] }, { name: 'Stock Out', data: [inventoryData.branch1.stock_out, inventoryData.branch2.stock_out] }, { name: 'Spoilage', data: [inventoryData.branch1.spoilage, inventoryData.branch2.spoilage] }],
                    chart: { type: 'bar', height: 350 }, colors: ['#10B981', '#F59E0B', '#EF4444'], xaxis: { categories: ['Branch 1', 'Branch 2'] }
                };
                initChart("#inventoryChart", inventoryOptions);
            }

            // --- Modal Charts ---
            const branches = ['Branch 1', 'Branch 2'];

            // New Modals Charts (Pie Charts)
            // Use ?? 0 checks to ensure valid numbers if null
            initChart("#salesCombinedChart", { series: [{{ $branch1SalesAllTime ?? 0 }}, {{ $branch2SalesAllTime ?? 0 }}], labels: branches, chart: { type: 'pie', height: 300 }, colors: ['#3B82F6', '#10B981'] });
            initChart("#ordersCombinedChart", { series: [{{ $branch1OrdersAllTime ?? 0 }}, {{ $branch2OrdersAllTime ?? 0 }}], labels: branches, chart: { type: 'pie', height: 300 }, colors: ['#3B82F6', '#10B981'] });
            initChart("#lostCombinedChart", { series: [{{ $branch1LostAllTime ?? 0 }}, {{ $branch2LostAllTime ?? 0 }}], labels: branches, chart: { type: 'pie', height: 300 }, colors: ['#EF4444', '#FCD34D'] });

            initChart("#salesDailyChart", { series: [{{ $branch1Sales ?? 0 }}, {{ $branch2Sales ?? 0 }}], labels: branches, chart: { type: 'pie', height: 300 }, colors: ['#3B82F6', '#10B981'] });
            initChart("#ordersDailyChart", { series: [{{ $branch1Orders ?? 0 }}, {{ $branch2Orders ?? 0 }}], labels: branches, chart: { type: 'pie', height: 300 }, colors: ['#3B82F6', '#10B981'] });
            initChart("#activeOrdersChart", { series: [{{ $branch1ActiveOrders ?? 0 }}, {{ $branch2ActiveOrders ?? 0 }}], labels: branches, chart: { type: 'pie', height: 300 }, colors: ['#EAB308', '#F59E0B'] });

            // Existing Modal Charts
            // 1. Menu Items (Donut)
            const menuItemsByCategory = @json($menuItemsByCategory ?? []);
            initChart("#menuItemsChart", { series: menuItemsByCategory.map(c => c.count), labels: menuItemsByCategory.map(c => c.name), chart: { type: 'donut', height: 300 } });

            // 2. Low Stock (Bar)
            const lowStockByBranch = @json($lowStockByBranch ?? []);
            initChart("#lowStockChart", { series: [{ name: 'Low Stock', data: [lowStockByBranch['Branch 1'] || 0, lowStockByBranch['Branch 2'] || 0] }], chart: { type: 'bar', height: 300 }, xaxis: { categories: branches }, colors: ['#EF4444'], plotOptions: { bar: { horizontal: true } } });

            // 3. New Stock (Bar)
            const newStockByBranch = @json($newStockByBranch ?? []);
            initChart("#newStockChart", { series: [{ name: 'New Stock', data: [newStockByBranch['Branch 1'] || 0, newStockByBranch['Branch 2'] || 0] }], chart: { type: 'bar', height: 300 }, xaxis: { categories: branches }, colors: ['#10B981'], plotOptions: { bar: { horizontal: true } } });

            // 4. Spoilage (Bar)
            const spoilageByBranch = @json($spoilageByBranch ?? []);
            initChart("#spoilageChart", { series: [{ name: 'Spoilage', data: [spoilageByBranch['Branch 1'] || 0, spoilageByBranch['Branch 2'] || 0] }], chart: { type: 'bar', height: 300 }, xaxis: { categories: branches }, colors: ['#F97316'], plotOptions: { bar: { horizontal: true } } });

            // 5. Lost (Bar)
            const lostByBranch = @json($lostByBranch ?? []);
            initChart("#lostChart", { series: [{ name: 'Lost', data: [lostByBranch['Branch 1'] || 0, lostByBranch['Branch 2'] || 0] }], chart: { type: 'bar', height: 300 }, xaxis: { categories: branches }, colors: ['#6B7280'], plotOptions: { bar: { horizontal: true } } });

            // 6. Returns (Bar)
            const returnsByBranch = @json($returnsByBranch ?? []);
            initChart("#returnsChart", { series: [{ name: 'Returns', data: [returnsByBranch['Branch 1'] || 0, returnsByBranch['Branch 2'] || 0] }], chart: { type: 'bar', height: 300 }, xaxis: { categories: branches }, colors: ['#3B82F6'], plotOptions: { bar: { horizontal: true } } });

            // 7. Refunds (Bar - Amount)
            const refundsByBranch = @json($refundsByBranch ?? []);
            initChart("#refundsChart", { series: [{ name: 'Refunds (₱)', data: [refundsByBranch['Branch 1'] || 0, refundsByBranch['Branch 2'] || 0] }], chart: { type: 'bar', height: 300 }, xaxis: { categories: branches }, colors: ['#EC4899'], plotOptions: { bar: { horizontal: true } } });

            // Top Selling Loop
            const topSellingItems = @json($topSellingItems ?? []);
            topSellingItems.forEach(item => {
                const labels = item.branch_breakdown.map(b => b.branch_name);
                const series = item.branch_breakdown.map(b => parseInt(b.quantity));
                initChart(`#topSellingChart-${item.id}`, { series: series, labels: labels, chart: { type: 'pie', height: 300 }, colors: ['#3B82F6', '#10B981'] });
            });


            // Auto-open modal logic
            const urlParams = new URLSearchParams(window.location.search);
            const modalId = urlParams.get('modal');
            if (modalId && document.getElementById(modalId)) {
                openModal(modalId);
            }
        });

        function openModal(modalId) {
            const el = document.getElementById(modalId);
            if (el) {
                el.classList.remove('hidden');
                window.dispatchEvent(new Event('resize'));
                initPagination(modalId);
            }
        }

        function closeModal(modalId) {
            const el = document.getElementById(modalId);
            if (el) el.classList.add('hidden');
        }

        function initPagination(modalId) {
            const tableIdMap = {
                'salesCombinedModal': 'table-salesCombined',
                'ordersCombinedModal': 'table-ordersCombined',
                'lostCombinedModal': 'table-lostCombined',
                'salesDailyModal': 'table-salesDaily',
                'ordersDailyModal': 'table-ordersDaily',
                'activeOrdersModal': 'table-activeOrders',
                'menuItemsModal': 'table-menuItems',
                'lowStockModal': 'table-lowStock',
                'newStockModal': 'table-newStock',
                'spoilageModal': 'table-spoilage',
                'lostModal': 'table-lost',
                'returnsModal': 'table-returns',
                'refundsModal': 'table-refunds'
            };
            let tableId = modalId.startsWith('topSellingModal-') ? `table-topSelling-${modalId.split('-')[1]}` : tableIdMap[modalId];
            if (tableId) paginateTable(tableId, 10);
        }

        function paginateTable(tableId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            let paginationDivID = 'pagination-' + tableId.replace('table-', '');
            let paginationDiv = document.getElementById(paginationDivID);
            if (!paginationDiv) {
                paginationDiv = document.createElement('div');
                paginationDiv.id = paginationDivID;
                paginationDiv.className = 'mt-4 flex justify-end items-center space-x-2';
                table.parentElement.appendChild(paginationDiv);
            }

            if (rowCount <= rowsPerPage) {
                paginationDiv.classList.add('hidden');
                rows.forEach(row => row.style.display = '');
                return;
            }

            paginationDiv.classList.remove('hidden');

            window.renderPagination = function (tId, page, pCount, rPP) {
                const t = document.getElementById(tId);
                const tb = t.querySelector('tbody');
                const rs = Array.from(tb.querySelectorAll('tr'));

                // Show/Hide rows
                rs.forEach((row, i) => row.style.display = (i >= (page - 1) * rPP && i < page * rPP) ? '' : 'none');
                t.dataset.currentPage = page;

                // Find Pagination Div
                let pDiv = document.getElementById('pagination-' + tId.replace('table-', ''));
                if (!pDiv && tId === 'cashier-active-orders-table') {
                    pDiv = document.getElementById('pagination-cashier-active');
                }
                if (!pDiv) return;

                // Icons
                const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>`;
                const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>`;

                let html = '';

                // Previous Button
                html += `<button onclick="renderPagination('${tId}', ${page - 1}, ${pCount}, ${rPP})" 
                                class="w-8 h-8 flex items-center justify-center mr-2 rounded hover:bg-gray-100 ${page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-black'}" 
                                ${page === 1 ? 'disabled' : ''}>
                                ${prevIcon}
                             </button>`;

                // Generate Page Numbers
                let range = [];
                if (pCount <= 7) {
                    for (let i = 1; i <= pCount; i++) range.push(i);
                } else {
                    if (page <= 4) {
                        range = [1, 2, 3, 4, 5, '...', pCount];
                    } else if (page >= pCount - 3) {
                        range = [1, '...', pCount - 4, pCount - 3, pCount - 2, pCount - 1, pCount];
                    } else {
                        range = [1, '...', page - 1, page, page + 1, '...', pCount];
                    }
                }

                range.forEach(item => {
                    if (item === '...') {
                        html += `<span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>`;
                    } else {
                        const isActive = item === page;
                        const classes = isActive
                            ? 'bg-blue-600 text-white font-bold'
                            : 'text-black hover:bg-gray-100';
                        html += `<button onclick="renderPagination('${tId}', ${item}, ${pCount}, ${rPP})" 
                                        class="w-8 h-8 flex items-center justify-center rounded mx-1 ${classes}">
                                        ${item}
                                     </button>`;
                    }
                });

                // Next Button
                html += `<button onclick="renderPagination('${tId}', ${page + 1}, ${pCount}, ${rPP})" 
                                class="w-8 h-8 flex items-center justify-center ml-2 rounded hover:bg-gray-100 ${page === pCount ? 'text-gray-300 cursor-not-allowed' : 'text-black'}" 
                                ${page === pCount ? 'disabled' : ''}>
                                ${nextIcon}
                             </button>`;

                pDiv.innerHTML = html;
            };

            // Initial render
            window.renderPagination(tableId, 1, pageCount, rowsPerPage);
        }

        // Order Modal Functions
        function openOrderModal(orderId) {
            const el = document.getElementById(`order-modal-${orderId}`);
            if (el) el.classList.remove('hidden');
        }
        function closeOrderModal(orderId) {
            const el = document.getElementById(`order-modal-${orderId}`);
            if (el) el.classList.add('hidden');
        }
    </script>
@endsection