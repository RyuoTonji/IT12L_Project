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
                    Sales Report
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
                <form id="dateFilterForm" action="{{ route('admin.reports.sales') }}" method="GET"
                    class="flex flex-wrap items-end gap-4">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="document.getElementById('dateFilterForm').submit()">
                    </div>

                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="document.getElementById('dateFilterForm').submit()">
                    </div>

                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-1">Filter Staff</label>
                        <select name="user_id" id="user_id" onchange="document.getElementById('dateFilterForm').submit()"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 w-full md:w-48">
                            <option value="">All Staff</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ isset($userId) && $userId == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="bg-white p-4 rounded-lg shadow border">
                    <h2 class="text-lg font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Total Sales
                    </h2>
                    <p class="text-2xl font-bold text-blue-600">₱{{ number_format($totalSales, 2) }}</p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border">
                    <h2 class="text-lg font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Total Orders
                    </h2>
                    <p class="text-2xl font-bold text-green-600">{{ $totalOrders }}</p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border">
                    <h2 class="text-lg font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-purple-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Average Order Value
                    </h2>
                    <p class="text-2xl font-bold text-purple-600">
                        ₱{{ $totalOrders > 0 ? number_format($totalSales / $totalOrders, 2) : '0.00' }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-lg shadow border">
                    <h2 class="text-lg font-medium text-gray-700 mb-2 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-gray-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Date Range
                    </h2>
                    <p class="text-md font-medium">
                        {{ \Carbon\Carbon::parse($startDate)->format('M d, Y') }} -
                        {{ \Carbon\Carbon::parse($endDate)->format('M d, Y') }}
                    </p>
                </div>
            </div>

            <!-- Sales Chart -->
            <div class="bg-white p-4 rounded-lg shadow border mb-6">
                <h2 class="text-lg font-medium mb-4">Sales Trend</h2>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Daily Sales Table -->
            <div class="bg-white p-4 rounded-lg shadow border mb-6">
                <h2 class="text-lg font-medium mb-4">Daily Sales</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Orders</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sales</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Average Order</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($sales as $day)
                                <tr>
                                    <td class="py-2 px-4">{{ \Carbon\Carbon::parse($day->date)->format('M d, Y') }}</td>
                                    <td class="py-2 px-4 text-right">{{ $day->order_count }}</td>
                                    <td class="py-2 px-4 text-right">₱{{ number_format($day->total_sales, 2) }}</td>
                                    <td class="py-2 px-4 text-right">
                                        ₱{{ number_format($day->total_sales / $day->order_count, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-4 px-4 text-center text-gray-500">No sales data found for the
                                        selected period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    </div>
                    <div class="mt-4">
                        {{ $sales->links('vendor.pagination.custom') }}
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white p-4 rounded-lg shadow border">
                <h2 class="text-lg font-medium mb-4">Payment Methods</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Method</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Count</th>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Total</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse($paymentMethods as $method)
                                        <tr>
                                            <td class="py-2 px-4">{{ ucfirst($method->payment_method) }}</td>
                                            <td class="py-2 px-4 text-right">{{ $method->count }}</td>
                                            <td class="py-2 px-4 text-right">₱{{ number_format($method->total, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="py-4 px-4 text-center text-gray-500">No payment data found
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div>
                        <div class="h-64">
                            <canvas id="paymentChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Sales Chart

            Chart.defaults.font.size = 16;

            const salesCtx = document.getElementById('salesChart').getContext('2d');
            const salesChart = new Chart(salesCtx, {
                type: 'line',
                data: {
                    labels: [
                        @foreach($sales as $day)
                            '{{ \Carbon\Carbon::parse($day->date)->format("M d") }}',
                        @endforeach
                                                                ],
                    datasets: [{
                        label: 'Daily Sales',
                        data: [
                            @foreach($sales as $day)
                                {{ $day->total_sales }},
                            @endforeach
                                                                    ],
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        pointBackgroundColor: 'rgba(59, 130, 246, 1)',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    onClick: (event, activeElements) => {
                        if (activeElements.length > 0) {
                            const index = activeElements[0].index;
                            const selectedLabel = salesChart.data.labels[index];
                            filterGraphByDate(index, selectedLabel);
                        }
                    },
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

            // Store original data for resetting
            const originalLabels = [...salesChart.data.labels];
            const originalData = [...salesChart.data.datasets[0].data];
            let currentFilterIndex = null;

            function filterGraphByDate(index, label) {
                if (currentFilterIndex === index) {
                    // If clicking the same date, reset
                    resetGraphFilter();
                } else {
                    // Filter to show only selected date
                    currentFilterIndex = index;
                    salesChart.data.labels = [label];
                    salesChart.data.datasets[0].data = [originalData[index]];
                    salesChart.update();
                    showResetButton();
                }
            }

            function resetGraphFilter() {
                currentFilterIndex = null;
                salesChart.data.labels = [...originalLabels];
                salesChart.data.datasets[0].data = [...originalData];
                salesChart.update();
                hideResetButton();
            }

            function showResetButton() {
                let resetBtn = document.getElementById('resetGraphBtn');
                if (!resetBtn) {
                    resetBtn = document.createElement('button');
                    resetBtn.id = 'resetGraphBtn';
                    resetBtn.textContent = 'Reset Filter';
                    resetBtn.className = 'mt-2 px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700';
                    resetBtn.onclick = resetGraphFilter;
                    document.getElementById('salesChart').parentElement.appendChild(resetBtn);
                }
            }

            function hideResetButton() {
                const resetBtn = document.getElementById('resetGraphBtn');
                if (resetBtn) {
                    resetBtn.remove();
                }
            }

            // Payment Methods Chart
            const paymentCtx = document.getElementById('paymentChart').getContext('2d');
            const paymentChart = new Chart(paymentCtx, {
                type: 'pie',
                data: {
                    labels: [
                        @foreach($paymentMethods as $method)
                            '{{ ucfirst($method->payment_method) }}',
                        @endforeach
                                                                ],
                    datasets: [{
                        data: [
                            @foreach($paymentMethods as $method)
                                {{ $method->total }},
                            @endforeach
                                                                    ],
                        backgroundColor: [
                            'rgba(59, 130, 246, 0.7)',
                            'rgba(16, 185, 129, 0.7)',
                            'rgba(139, 92, 246, 0.7)',
                        ],
                        borderColor: [
                            'rgba(59, 130, 246, 1)',
                            'rgba(16, 185, 129, 1)',
                            'rgba(139, 92, 246, 1)',
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.raw;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = Math.round((value / total) * 100);
                                    return `${label}: ₱${value.toFixed(2)} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
        });

        function showExportConfirmation() {
            AlertModal.showConfirm(
                'Are you sure you want to export this sales report as PDF?',
                function () {
                    window.location.href = '{{ route('export.sales') }}';
                },
                null,
                'Export Confirmation'
            );
        }
    </script>
@endsection