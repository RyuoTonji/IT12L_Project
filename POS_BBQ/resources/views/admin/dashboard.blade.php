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
                        <p class="text-3xl font-bold text-yellow-900 mt-2 text-right">{{ $activeOrders }}</p>
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
                        @if($filterDate)
                            <a href="{{ route('admin.dashboard') }}"
                                class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md text-sm hover:bg-gray-300 transition flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filter
                            </a>
                        @endif
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
                                                        <tr class="hover:bg-gray-50">
                                                            <td class="py-3 px-4 text-sm text-gray-900">#{{ $order->id }}</td>
                                                            <td class="py-3 px-4 text-sm text-gray-600">{{ $order->branch->name ?? 'N/A' }}</td>
                                                            <td class="py-3 px-4 text-sm">
                                                                <span
                                                                    class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded
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
    </script>
@endsection