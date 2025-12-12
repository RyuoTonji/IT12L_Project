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
                    <div class="bg-indigo-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-indigo-800">Total Sales (All Time)</h3>
                        <p class="text-3xl font-bold text-indigo-900 mt-2 text-right">₱{{ number_format($totalSales, 2) }}
                        </p>
                    </div>

                    <!-- Total Orders (All Time) -->
                    <div class="bg-teal-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-teal-800">Total Orders (All Time)</h3>
                        <p class="text-3xl font-bold text-teal-900 mt-2 text-right">{{ $totalOrders }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-blue-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-blue-800">Total Sales Today</h3>
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

                    <div class="bg-green-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-green-800">Total Orders Today</h3>
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

                    <div class="bg-yellow-100 p-6 rounded-lg shadow">
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
                            <p class="text-base font-semibold text-gray-700">Today's Sales</p>
                            <p class="text-2xl font-bold text-blue-900 text-right">₱{{ number_format($branch1Sales, 2) }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">Today's Orders</p>
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
                            <p class="text-base font-semibold text-gray-700">Today's Sales</p>
                            <p class="text-2xl font-bold text-blue-900 text-right">₱{{ number_format($branch2Sales, 2) }}
                            </p>
                        </div>
                        <div class="bg-white p-4 rounded shadow-sm">
                            <p class="text-base font-semibold text-gray-700">Today's Orders</p>
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

            <!-- Inventory & Menu Overview -->
            <div class="mb-8">
                <h2 class="text-xl font-medium text-gray-800 border-b pb-3 mb-6">Inventory & Menu</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-purple-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-purple-800">Total Menu Items</h3>
                        <p class="text-3xl font-bold text-purple-900 mt-2 text-right">{{ $menuItemsCount }}</p>
                    </div>

                    <div class="bg-red-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-red-800">Low Stock Items</h3>
                        <p class="text-3xl font-bold text-red-900 mt-2 text-right">{{ $lowStockItems->count() }}</p>
                    </div>

                    <div class="bg-indigo-100 p-6 rounded-lg shadow">
                        <h3 class="text-lg font-medium text-indigo-800">New Stock Items</h3>
                        <p class="text-3xl font-bold text-indigo-900 mt-2 text-right">{{ $newStockItems->count() }}</p>
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
                                                                <span class="inline-flex items-center text-xs leading-5 font-semibold
                                                                                                                                                                                                                                                                                {{ $order->status == 'completed' ? 'text-green-600' :
                                    ($order->status == 'cancelled' ? 'text-red-600' : 'text-yellow-600') }}">
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
                                                                    @else
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    @endif
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

    <!-- Order Details Modals -->
    @foreach($recentOrders as $order)
        <div id="order-modal-{{ $order->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Dark overlay background -->
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
            // Sales Chart
            const salesChartData = @json($salesChartData);
            const salesOptions = {
                series: [{
                    name: 'Branch 1',
                    data: salesChartData.map(d => d.branch1)
                }, {
                    name: 'Branch 2',
                    data: salesChartData.map(d => d.branch2)
                }],
                chart: {
                    type: 'area',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 2
                },
                xaxis: {
                    categories: salesChartData.map(d => d.date),
                    labels: {
                        style: {
                            fontSize: '14px'
                        }
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (val) {
                            return '₱' + val.toFixed(0);
                        },
                        style: {
                            fontSize: '14px'
                        }
                    }
                },
                colors: ['#3B82F6', '#10B981'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.2,
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return '₱' + val.toFixed(2);
                        }
                    }
                }
            };
            const salesChart = new ApexCharts(document.querySelector("#salesChart"), salesOptions);
            salesChart.render();

            // Orders Chart
            const ordersChartData = @json($ordersChartData);
            const ordersOptions = {
                series: [{
                    name: 'Branch 1',
                    data: ordersChartData.map(d => d.branch1)
                }, {
                    name: 'Branch 2',
                    data: ordersChartData.map(d => d.branch2)
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ordersChartData.map(d => d.date),
                    labels: {
                        style: {
                            fontSize: '14px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Number of Orders',
                        style: {
                            fontSize: '14px'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '14px'
                        }
                    }
                },
                colors: ['#3B82F6', '#10B981'],
                fill: {
                    opacity: 1
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " orders";
                        }
                    }
                }
            };
            const ordersChart = new ApexCharts(document.querySelector("#ordersChart"), ordersOptions);
            ordersChart.render();

            // Inventory Chart
            const inventoryData = @json($inventoryChartData);
            const inventoryOptions = {
                series: [{
                    name: 'Stock In',
                    data: [inventoryData.branch1.stock_in, inventoryData.branch2.stock_in]
                }, {
                    name: 'Stock Out',
                    data: [inventoryData.branch1.stock_out, inventoryData.branch2.stock_out]
                }, {
                    name: 'Spoilage',
                    data: [inventoryData.branch1.spoilage, inventoryData.branch2.spoilage]
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        endingShape: 'rounded'
                    },
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    show: true,
                    width: 2,
                    colors: ['transparent']
                },
                xaxis: {
                    categories: ['Branch 1', 'Branch 2'],
                    labels: {
                        style: {
                            fontSize: '14px'
                        }
                    }
                },
                yaxis: {
                    title: {
                        text: 'Quantity',
                        style: {
                            fontSize: '14px'
                        }
                    },
                    labels: {
                        style: {
                            fontSize: '14px'
                        }
                    }
                },
                colors: ['#10B981', '#F59E0B', '#EF4444'],
                fill: {
                    opacity: 1
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'left',
                    fontSize: '14px'
                },
                tooltip: {
                    y: {
                        formatter: function (val) {
                            return val + " units";
                        }
                    }
                }
            };
            const inventoryChart = new ApexCharts(document.querySelector("#inventoryChart"), inventoryOptions);
            inventoryChart.render();
        });

        // Order Modal Functions
        function openOrderModal(orderId) {
            document.getElementById(`order-modal-${orderId}`).classList.remove('hidden');
        }

        function closeOrderModal(orderId) {
            document.getElementById(`order-modal-${orderId}`).classList.add('hidden');
        }
    </script>
@endsection