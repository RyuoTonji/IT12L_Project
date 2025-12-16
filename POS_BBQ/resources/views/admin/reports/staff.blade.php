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
                    Staff Performance Report
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
                <form action="{{ route('admin.reports.staff') }}" method="GET" class="flex flex-wrap items-end gap-4">
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

            <!-- Staff Performance -->
            <div class="bg-white p-4 rounded-lg shadow border mb-6">
                <h2 class="text-lg font-medium mb-4">Staff Performance</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Staff Member</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Orders</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total Sales</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Avg. Order Value</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($staffPerformance as $staff)
                                        <tr>
                                            <td class="py-2 px-4">{{ $staff->name }}</td>
                                            <td class="py-2 px-4 text-right">{{ $staff->total_orders }}</td>
                                            <td class="py-2 px-4 text-right">₱{{ number_format($staff->total_sales, 2) }}</td>
                                            <td class="py-2 px-4 text-right">
                                                ₱{{ $staff->total_orders > 0 ? number_format($staff->total_sales / $staff->total_orders, 2) : '0.00' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="py-4 px-4 text-center text-gray-500">No staff performance
                                                data found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <div class="h-96">
                            <div id="staffChart"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Staff Performance Chart using ApexCharts
            const staffNames = [
                @foreach($staffPerformance as $staff)
                    '{{ $staff->name }}',
                @endforeach
                    ];

            const salesData = [
                @foreach($staffPerformance as $staff)
                    {{ $staff->total_sales }},
                @endforeach
                    ];

            const ordersData = [
                @foreach($staffPerformance as $staff)
                    {{ $staff->total_orders }},
                @endforeach
                    ];

            const options = {
                series: [{
                    name: 'Total Sales',
                    data: salesData
                }, {
                    name: 'Orders',
                    data: ordersData
                }],
                chart: {
                    type: 'bar',
                    height: 350,
                    toolbar: {
                        show: true,
                        tools: {
                            download: true,
                            zoom: false,
                            zoomin: false,
                            zoomout: false,
                            pan: false,
                            reset: false
                        }
                    }
                },
                plotOptions: {
                    bar: {
                        horizontal: false,
                        columnWidth: '55%',
                        borderRadius: 4,
                        dataLabels: {
                            position: 'top'
                        }
                    }
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
                    categories: staffNames,
                    labels: {
                        style: {
                            fontSize: '12px'
                        }
                    }
                },
                yaxis: [
                    {
                        title: {
                            text: 'Sales (₱)'
                        },
                        labels: {
                            formatter: function (value) {
                                return '₱' + value.toLocaleString();
                            }
                        }
                    },
                    {
                        opposite: true,
                        title: {
                            text: 'Orders'
                        },
                        labels: {
                            formatter: function (value) {
                                return value.toFixed(0);
                            }
                        }
                    }
                ],
                fill: {
                    opacity: 1,
                    colors: ['#3B82F6', '#10B981']
                },
                tooltip: {
                    y: {
                        formatter: function (val, opts) {
                            if (opts.seriesIndex === 0) {
                                return '₱' + val.toLocaleString();
                            }
                            return val + ' orders';
                        }
                    }
                },
                legend: {
                    position: 'top',
                    horizontalAlign: 'center'
                },
                colors: ['#3B82F6', '#10B981']
            };

            const chart = new ApexCharts(document.querySelector("#staffChart"), options);
            chart.render();
        });

        function showExportConfirmation() {
            const startDate = '{{ $startDate }}';
            const endDate = '{{ $endDate }}';
            AlertModal.showConfirm(
                'Are you sure you want to export this staff report as PDF?',
                function () {
                    window.location.href = `/export/staff?start_date=${startDate}&end_date=${endDate}`;
                },
                null,
                'Export Confirmation'
            );
        }
    </script>
@endsection