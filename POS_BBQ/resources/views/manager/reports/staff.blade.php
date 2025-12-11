@extends('layouts.manager')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Staff Performance Report</h1>
                <div>
                    <button onclick="showExportConfirmation()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export PDF
                    </button>
                    <a href="{{ route('manager.reports') }}"
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
                <form action="{{ route('manager.reports.staff') }}" method="GET" class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>

                    <div>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V4z" />
                            </svg>
                            Apply Filter
                        </button>
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
                        <div class="h-64">
                            <canvas id="staffChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Staff Performance Chart
            Chart.defaults.font.size = 16;

            const staffCtx = document.getElementById('staffChart').getContext('2d');
            const staffChart = new Chart(staffCtx, {
                type: 'bar',
                data: {
                    labels: [
                        @foreach($staffPerformance as $staff)
                            '{{ $staff->name }}',
                        @endforeach
                                    ],
                    datasets: [{
                        label: 'Total Sales',
                        data: [
                            @foreach($staffPerformance as $staff)
                                {{ $staff->total_sales }},
                            @endforeach
                                        ],
                        backgroundColor: 'rgba(59, 130, 246, 0.7)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return '₱' + value;
                                }
                            }
                        }
                    }
                }
            });
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