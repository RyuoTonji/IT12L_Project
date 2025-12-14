@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Menu Items Report
                </h1>
                <div>
                    <button onclick="showExportConfirmation()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zm2-10l4 4m-4 0l4-4m-4 4V7" />
                        </svg>
                        Export PDF
                    </button>
                    <a href="{{ route('admin.reports') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Date Range Filter -->
            <div class="bg-gray-50 p-4 rounded-lg border mb-6">
                <form action="{{ route('admin.reports.items') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="this.form.submit()">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="this.form.submit()">
                    </div>


                </form>
            </div>

            <!-- Category Performance -->
            <div class="bg-white p-4 rounded-lg shadow border mb-6">
                <h2 class="text-lg font-medium mb-4">Category Performance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Category</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Items Sold</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total Sales</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($categories as $category)
                                        <tr>
                                            <td class="py-2 px-4">{{ $category->name }}</td>
                                            <td class="py-2 px-4 text-right">{{ $category->total_quantity }}</td>
                                            <td class="py-2 px-4 text-right">₱{{ number_format($category->total_sales, 2) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 px-4 text-center text-gray-500">No category data found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <div id="categoryChart" class="w-full flex justify-center items-center"></div>
                    </div>
                </div>
            </div>

            <!-- Top Selling Items -->
            <div class="bg-white p-4 rounded-lg shadow border">
                <h2 class="text-lg font-medium mb-4">Top Selling Items</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Item</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Category</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity Sold</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Sales</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($topItems as $item)
                                <tr>
                                    <td class="py-2 px-4">{{ $item->name }}</td>
                                    <td class="py-2 px-4">{{ $item->category_name }}</td>
                                    <td class="py-2 px-4 text-right">{{ $item->total_quantity }}</td>
                                    <td class="py-2 px-4 text-right">₱{{ number_format($item->total_sales, 2) }}</td>
                                    <td class="py-2 px-4 text-center">
                                        <a href="{{ route('menu.show', $item->id) }}"
                                            class="text-blue-600 hover:text-blue-900">View Item</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-4 px-4 text-center text-gray-500">No item data found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // ApexCharts Implementation
            const categoryData = {
                series: [
                    @foreach($categories as $category)
                        {{ $category->total_sales }},
                    @endforeach
                        ],
                labels: [
                    @foreach($categories as $category)
                        '{{ $category->name }}',
                    @endforeach
                        ]
            };

            const options = {
                series: categoryData.series,
                chart: {
                    width: '100%',
                    height: 350,
                    type: 'pie',
                },
                labels: categoryData.labels,
                colors: [
                    '#3B82F6', // Blue
                    '#10B981', // Emerald
                    '#8B5CF6', // Violet
                    '#F472B6', // Pink
                    '#FBBF24', // Amber
                    '#6366F1', // Indigo
                    '#EC4899', // Pink-600
                ],
                legend: {
                    position: 'bottom'
                },
                title: {
                    text: 'Sales Distribution by Category',
                    align: 'center',
                    style: {
                        fontSize: '16px',
                        fontWeight: '600',
                        color: '#374151'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function (value) {
                            return '₱' + value.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            });
                        }
                    }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: {
                            width: 300
                        },
                        legend: {
                            position: 'bottom'
                        }
                    }
                }]
            };

            const chart = new ApexCharts(document.querySelector("#categoryChart"), options);
            chart.render();
        });

        function showExportConfirmation() {
            const startDate = '{{ $startDate }}';
            const endDate = '{{ $endDate }}';
            AlertModal.showConfirm(
                'Are you sure you want to export this items report as PDF?',
                function () {
                    window.location.href = `/export/items?start_date=${startDate}&end_date=${endDate}`;
                },
                null,
                'Export Confirmation'
            );
        }
    </script>
@endsection