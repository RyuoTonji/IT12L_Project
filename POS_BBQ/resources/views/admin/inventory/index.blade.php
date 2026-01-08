@extends('layouts.admin')

@section('content')
    <!-- Forecasting Summary Div -->
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
        <div class="p-6 text-gray-900 border-b border-gray-100 flex justify-between items-center">
            <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-black" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                Forecasting Summary (Tomorrow)
            </h3>
            <a href="{{ route('admin.forecasting.index') }}"
                class="text-sm font-semibold text-black hover:text-gray-700 transition-colors flex items-center">
                View Detailed Forecast
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
        </div>
        <div class="p-6 grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Summary Stats -->
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 transition-all hover:bg-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Predicted Sales</p>
                        <p class="text-3xl font-black text-gray-900 line-height-tight text-right">
                            ₱{{ number_format($nextDayForecast['total_predicted_sales'], 2) }}</p>
                    </div>
                    <div class="p-5 bg-gray-50 rounded-2xl border border-gray-100 transition-all hover:bg-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Stock Shortfalls</p>
                        <p class="text-3xl font-black text-gray-900 line-height-tight text-right">
                            {{ collect($nextDayForecast['ingredients'])->where('to_buy', '>', 0)->count() }} Items</p>
                    </div>
                </div>

                <!-- Mini Ingredient Alert -->
                <div class="overflow-hidden rounded-xl border border-gray-100">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50 text-[10px] font-bold text-gray-500 uppercase tracking-wider">
                            <tr>
                                <th class="px-4 py-2 text-left">Top Shortfall Ingredients</th>
                                <th class="px-4 py-2 text-right">To Buy</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-50">
                            @forelse(collect($nextDayForecast['ingredients'])->where('to_buy', '>', 0)->take(3) as $ing)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-700 font-medium">{{ $ing['name'] }}</td>
                                    <td class="px-4 py-2 text-sm text-black font-bold text-right">+{{ $ing['to_buy'] }}
                                        {{ $ing['unit'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="px-4 py-6 text-center text-sm text-green-600 font-medium">All stocks
                                        sufficient for tomorrow's forecast!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mini Trend Graph -->
            <div class="h-full min-h-[150px]">
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-3">14-Day Sales Trend</p>
                <div class="h-40">
                    <canvas id="miniTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('miniTrendChart').getContext('2d');
            const salesData = @json($dailyTrends);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesData.map(d => d.date),
                    datasets: [{
                        data: salesData.map(d => d.total),
                        borderColor: '#111827',
                        backgroundColor: 'rgba(17, 24, 39, 0.05)',
                        borderWidth: 2,
                        label: 'Sales'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: { display: false, beginAtZero: true },
                        x: { display: false }
                    }
                }
            });
        });
    </script>

    <hr class="mb-8 border-gray-200" />

    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ activeTab: 0 }">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Inventory Management
                </h3>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.inventory.stock-in-history') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Stock-In History
                    </a>
                    <a href="{{ route('admin.inventory.report') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Daily Report
                    </a>
                    <button onclick="document.getElementById('addStockModal').classList.remove('hidden')"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Stock In
                    </button>
                    <button onclick="showExportConfirmation()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zm2-10l4 4m-4 0l4-4m-4 4V7" />
                        </svg>
                        Export PDF
                    </button>
                    <a href="{{ route('admin.inventory.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </a>
                </div>
            </div>

            <!-- Filter Form (Date Only) -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form id="filterForm" action="{{ route('admin.inventory.index') }}" method="GET"
                    class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date Added</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-black focus:ring focus:ring-gray-200 focus:ring-opacity-50"
                            onchange="document.getElementById('filterForm').submit()">
                    </div>
                    <div class="flex items-center pb-1">
                        @if(request()->filled('date'))
                            <a href="{{ route('admin.inventory.index') }}"
                                class="text-gray-600 hover:text-gray-900 flex items-center inline-flex ml-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Category Tabs & Tables -->
            @if(!request('date'))
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto custom-scrollbar" aria-label="Tabs">
                        @foreach($inventoryByCategory as $categoryName => $items)
                            <button @click="activeTab = {{ $loop->index }}"
                                :class="activeTab === {{ $loop->index }} ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200 outline-none focus:outline-none">
                                {{ $categoryName ?: 'Uncategorized' }}
                                <span
                                    :class="activeTab === {{ $loop->index }} ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-900'"
                                    class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium inline-block transition-colors duration-200">
                                    {{ $items->count() }}
                                </span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Tab Contents (Tables) -->
                @forelse($inventoryByCategory as $categoryName => $items)
                    <div x-show="activeTab === {{ $loop->index }}" class="category-section" style="display: none;">
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full bg-white divide-y divide-gray-200" id="table-cat-{{ $loop->index }}">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Supplier</th>
                                        <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity</th>
                                        <th
                                            class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit</th>
                                        <th
                                            class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                            onclick="window.location='{{ route('admin.inventory.show', $item) }}'">
                                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                            <td class="py-4 px-6 text-sm text-gray-500">{{ $item->supplier ?: '-' }}</td>
                                            <td class="py-4 px-6 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                                            <td class="py-4 px-6 text-sm text-center text-gray-500">{{ $item->unit }}</td>
                                            <td class="py-4 px-6 text-center">
                                                <span
                                                    class="inline-flex items-center text-xs font-medium {{ $item->quantity <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    @if($item->quantity <= 0)
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Out of Stock
                                                    @else
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        In Stock
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-center text-sm" onclick="event.stopPropagation();">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('admin.inventory.show', $item) }}"
                                                        class="text-black hover:text-gray-700 inline-flex items-center transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                            <circle cx="11.5" cy="15.5" r="2.5"></circle>
                                                            <path d="M16 20l-2-2"></path>
                                                        </svg>
                                                        View
                                                    </a>
                                                    <button type="button" onclick="confirmArchive({{ $item->id }})"
                                                        class="text-gray-500 hover:text-red-600 inline-flex items-center transition-colors ml-2">
                                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                                            stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                            <path d="M4 10h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V10Z"></path>
                                                            <path d="M6 10V7h12v3"></path>
                                                            <path d="M8 7V5h8v2"></path>
                                                            <rect x="9" y="14" width="6" height="2" rx="0.5"></rect>
                                                            <path d="M4 10l2-4h12l2 4"></path>
                                                        </svg>
                                                        Archive
                                                    </button>
                                                    <form id="deleteInventoryForm{{ $item->id }}"
                                                        action="{{ route('admin.inventory.destroy', $item) }}" method="POST"
                                                        class="hidden">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="pagination-cat-{{ $loop->index }}" class="p-4 bg-gray-50 border-t border-gray-200"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Inventory Items</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new inventory item.</p>
                        <div class="mt-6">
                            <a href="{{ route('admin.inventory.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Item
                            </a>
                        </div>
                    </div>
                @endforelse

            @else
                <!-- Single List (Filtered) -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full bg-white divide-y divide-gray-200" id="table-filtered-result">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                                </th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supplier</th>
                                <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Unit</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($inventoryItems as $item)
                                <tr class="hover:bg-blue-50 transition-colors cursor-pointer"
                                    onclick="window.location='{{ route('admin.inventory.show', $item) }}'">
                                    <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="py-4 px-6 text-sm text-gray-500">{{ $item->supplier ?: '-' }}</td>
                                    <td class="py-4 px-6 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                                    <td class="py-4 px-6 text-sm text-center text-gray-500">{{ $item->unit }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span
                                            class="inline-flex items-center text-xs font-medium {{ $item->quantity <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                            @if($item->quantity <= 0)
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Out of Stock
                                            @else
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                In Stock
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center text-sm" onclick="event.stopPropagation();">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('admin.inventory.show', $item) }}"
                                                class="text-blue-600 hover:text-blue-900 inline-flex items-center transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                    <polyline points="14 2 14 8 20 8"></polyline>
                                                    <circle cx="11.5" cy="15.5" r="2.5"></circle>
                                                    <path d="M16 20l-2-2"></path>
                                                </svg>
                                                View
                                            </a>
                                            <button type="button" onclick="confirmArchive({{ $item->id }})"
                                                class="text-gray-500 hover:text-red-600 inline-flex items-center transition-colors ml-2">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                    <path d="M4 10h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V10Z"></path>
                                                    <path d="M6 10V7h12v3"></path>
                                                    <path d="M8 7V5h8v2"></path>
                                                    <rect x="9" y="14" width="6" height="2" rx="0.5"></rect>
                                                    <path d="M4 10l2-4h12l2 4"></path>
                                                </svg>
                                                Archive
                                            </button>
                                            <form id="deleteInventoryForm{{ $item->id }}"
                                                action="{{ route('admin.inventory.destroy', $item) }}" method="POST" class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">No inventory items found matching your
                                        filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="pagination-filtered-result" class="p-4 bg-gray-50 border-t border-gray-200"></div>
                </div>
            @endif
        </div>
    </div>

    <div id="addStockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Stock In</h3>
                <button onclick="document.getElementById('addStockModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.inventory.stock-in') }}" method="POST" id="stockInForm">
                @csrf
                <div class="mb-4">
                    <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                    <select name="category_id" id="category_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                        onchange="loadMenuItems()">
                        <option value="">Select Category</option>
                        @php
                            $categories = \App\Models\Category::orderBy('sort_order')->get();
                        @endphp
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-4">
                    <label for="menu_item_id" class="block text-sm font-medium text-gray-700 mb-1">Menu Item</label>
                    <select name="menu_item_id" id="menu_item_id" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black" disabled>
                        <option value="">Select category first</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity to Add</label>
                    <input type="number" name="quantity" id="stock_quantity" step="0.01" min="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black">
                </div>
                <div class="mb-4">
                    <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason / Notes</label>
                    <textarea name="reason" id="reason" rows="2"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-black focus:ring-black"
                        placeholder="e.g. Daily delivery, New stock"></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addStockModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Add Stock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function loadMenuItems() {
            const categoryId = document.getElementById('category_id').value;
            const menuItemSelect = document.getElementById('menu_item_id');

            if (!categoryId) {
                menuItemSelect.disabled = true;
                menuItemSelect.innerHTML = '<option value="">Select category first</option>';
                return;
            }

            // Fetch menu items for this category
            fetch(`/api/menu-items/${categoryId}`)
                .then(response => response.json())
                .then(data => {
                    menuItemSelect.disabled = false;
                    menuItemSelect.innerHTML = '<option value="">Select menu item</option>';
                    data.forEach(item => {
                        const option = document.createElement('option');
                        option.value = item.id;
                        option.textContent = item.name;
                        menuItemSelect.appendChild(option);
                    });
                });
        }

        function showExportConfirmation() {
            AlertModal.showConfirm(
                'Are you sure you want to export the inventory report as PDF?',
                function () {
                    window.location.href = '{{ route('export.inventory') }}';
                },
                null,
                'Export Confirmation'
            );
        }

        function confirmArchive(itemId) {
            AlertModal.showConfirm(
                'Are you sure you want to archive this item?',
                function () {
                    document.getElementById('deleteInventoryForm' + itemId).submit();
                },
                null,
                'Archive Confirmation'
            );
        }

        // Pagination Logic
        document.addEventListener('DOMContentLoaded', function () {
            @if(!request('date'))
                @foreach($inventoryByCategory as $categoryName => $items)
                    paginateTable('table-cat-{{ $loop->index }}', 10);
                @endforeach
            @else
                paginateTable('table-filtered-result', 10);
            @endif
                                                                });

        function paginateTable(tableId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            let paginationDivId;
            if (tableId === 'table-filtered-result') {
                paginationDivId = 'pagination-filtered-result';
            } else {
                paginationDivId = 'pagination-' + tableId.replace('table-', '');
            }

            let paginationDiv = document.getElementById(paginationDivId);
            if (!paginationDiv) {
                paginationDiv = document.createElement('div');
                paginationDiv.id = paginationDivId;
                paginationDiv.className = 'mt-4 flex justify-end items-center space-x-2';
                table.parentElement.appendChild(paginationDiv);
            }

            if (rowCount <= rowsPerPage) {
                paginationDiv.classList.add('hidden');
                rows.forEach(row => row.style.display = '');
                return;
            }

            paginationDiv.classList.remove('hidden');
            paginationDiv.className = 'p-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center space-x-2';

            window.renderPagination = function (tId, page, pCount, rPP) {
                const t = document.getElementById(tId);
                const tb = t.querySelector('tbody');
                const rs = Array.from(tb.querySelectorAll('tr'));

                // Show/Hide rows
                rs.forEach((row, i) => row.style.display = (i >= (page - 1) * rPP && i < page * rPP) ? '' : 'none');
                t.dataset.currentPage = page;

                // Find Pagination Div
                let pDivId = 'pagination-' + tId.replace('table-', '');
                if (tId === 'table-filtered-result') pDivId = 'pagination-filtered-result';

                let pDiv = document.getElementById(pDivId);
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
                            ? 'bg-gray-800 text-white font-bold'
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
    </script>
@endsection