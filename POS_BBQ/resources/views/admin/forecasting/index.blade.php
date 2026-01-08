@extends(auth()->user()->role == 'admin' ? 'layouts.admin' : 'layouts.inventory')

@section('content')
    <div class="p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">
                Forecasting Dashboard
            </h1>
            <div class="text-sm text-gray-500">
                Based on historical data and current inventory.
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Predicted Sales (Tomorrow)</p>
                <p class="text-3xl font-black text-gray-900 line-height-tight text-right">
                    ₱{{ number_format($nextDayForecast['total_predicted_sales'], 2) }}
                </p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Predicted Sales (Next Month)</p>
                <p class="text-3xl font-black text-gray-900 line-height-tight text-right">
                    ₱{{ number_format($nextMonthForecast['total_predicted_sales'], 2) }}
                </p>
            </div>
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 transition-all hover:shadow-md">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Stock Alerts</p>
                <p class="text-3xl font-black text-gray-900 line-height-tight text-right">
                    {{ collect($nextDayForecast['ingredients'])->where('to_buy', '>', 0)->count() }} Items
                </p>
                <p class="text-xs text-gray-500 mt-1 font-medium text-right">Below Tomorrow's Forecast</p>
            </div>
        </div>

        <!-- Graphs Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 mb-8">
            <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-black" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Recent Sales Trend
            </h2>
            <div class="h-80">
                <canvas id="salesTrendChart"></canvas>
            </div>
        </div>

        <!-- Forecasting Tables -->
        <div x-data="{ activeTab: 'tomorrow' }">
            <div class="flex space-x-4 mb-4 border-b border-gray-200">
                <button @click="activeTab = 'tomorrow'"
                    :class="activeTab === 'tomorrow' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 px-4 border-b-2 font-bold transition-all focus:outline-none">
                    Tomorrow's Forecast
                </button>
                <button @click="activeTab = 'month'"
                    :class="activeTab === 'month' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-gray-700'"
                    class="pb-2 px-4 border-b-2 font-bold transition-all focus:outline-none">
                    Next Month's Forecast
                </button>
            </div>

            <!-- Tomorrow's Content -->
            <div x-show="activeTab === 'tomorrow'" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Menu Forecast -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800">Predicted Menu Item Sales (Tomorrow)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="table-predict-menu-tomorrow">
                                <thead
                                    class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3">Menu Item</th>
                                        <th class="px-6 py-3">Predicted Qty</th>
                                        <th class="px-6 py-3">Estimated Sales</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($nextDayForecast['items'] as $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item['predicted_qty'] }}</td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                                ₱{{ number_format($item['subtotal'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">No historical
                                                data found for prediction.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-predict-menu-tomorrow"></div>
                    </div>

                    <!-- Ingredient Forecast -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800">Required Ingredients & Stock (Tomorrow)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="table-require-ing-tomorrow">
                                <thead
                                    class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3">Ingredient</th>
                                        <th class="px-6 py-3">Needed</th>
                                        <th class="px-6 py-3">In Stock</th>
                                        <th class="px-6 py-3">To Buy</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($nextDayForecast['ingredients'] as $ing)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ing['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ing['total_needed'] }}
                                                {{ $ing['unit'] }}</td>
                                            <td
                                                class="px-6 py-4 text-sm {{ $ing['current_stock'] < $ing['total_needed'] ? 'text-black font-bold' : 'text-green-600' }}">
                                                {{ $ing['current_stock'] }} {{ $ing['unit'] }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($ing['to_buy'] > 0)
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                                                        +{{ $ing['to_buy'] }} {{ $ing['unit'] }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="text-xs font-bold text-green-700 text-center inline-block min-w-[60px]">
                                                        Sufficient
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No ingredient
                                                data needed for prediction.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-require-ing-tomorrow"></div>
                    </div>
                </div>
            </div>

            <!-- Next Month's Content -->
            <div x-show="activeTab === 'month'" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Menu Forecast -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800">Predicted Menu Item Sales (Next Month)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="table-predict-menu-month">
                                <thead
                                    class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3">Menu Item</th>
                                        <th class="px-6 py-3">Predicted Qty</th>
                                        <th class="px-6 py-3">Estimated Sales</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($nextMonthForecast['items'] as $item)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $item['predicted_qty'] }}</td>
                                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                                ₱{{ number_format($item['subtotal'], 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-10 text-center text-gray-500 italic">No historical
                                                data found for prediction.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-predict-menu-month"></div>
                    </div>

                    <!-- Ingredient Forecast -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800">Total Ingredients Needed (Next Month)</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200" id="table-require-ing-month">
                                <thead
                                    class="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                                    <tr>
                                        <th class="px-6 py-3">Ingredient</th>
                                        <th class="px-6 py-3">Monthly Need</th>
                                        <th class="px-6 py-3">In Stock</th>
                                        <th class="px-6 py-3">Shortfall</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($nextMonthForecast['ingredients'] as $ing)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $ing['name'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ing['total_needed'] }}
                                                {{ $ing['unit'] }}</td>
                                            <td class="px-6 py-4 text-sm text-gray-600">{{ $ing['current_stock'] }}
                                                {{ $ing['unit'] }}</td>
                                            <td class="px-6 py-4">
                                                @if($ing['to_buy'] > 0)
                                                    <span
                                                        class="px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-700">
                                                        -{{ $ing['to_buy'] }} {{ $ing['unit'] }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="text-xs font-bold text-green-700">
                                                        Covered
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">No ingredient
                                                data needed for prediction.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div id="pagination-require-ing-month"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('salesTrendChart').getContext('2d');
            const salesData = @json($dailyTrends);

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: salesData.map(d => d.date),
                    datasets: [{
                        label: 'Daily Sales (₱)',
                        data: salesData.map(d => d.total),
                        borderColor: '#111827',
                        backgroundColor: 'rgba(17, 24, 39, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#111827',
                        pointBorderColor: '#fff',
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.05)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });

        // Pagination Logic
        function paginateTable(tableId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            let paginationDivId = 'pagination-' + tableId.replace('table-', '');
            let paginationDiv = document.getElementById(paginationDivId);

            if (rowCount <= rowsPerPage) {
                if (paginationDiv) paginationDiv.style.display = 'none';
                rows.forEach(row => row.style.display = '');
                return;
            }

            if (paginationDiv) {
                paginationDiv.style.display = 'flex';
                paginationDiv.className = 'p-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center space-x-2';
            }

            window.renderPagination = function(tId, page, pCount, rPP) {
                const t = document.getElementById(tId);
                const tb = t.querySelector('tbody');
                const rs = Array.from(tb.querySelectorAll('tr'));

                // Show/Hide rows
                rs.forEach((row, i) => row.style.display = (i >= (page - 1) * rPP && i < page * rPP) ? '' : 'none');
                
                // Find Pagination Div
                let pDivId = 'pagination-' + tId.replace('table-', '');
                let pDiv = document.getElementById(pDivId);
                if (!pDiv) return;

                const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>`;
                const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>`;

                let html = '';
                html += `<button onclick="renderPagination('${tId}', ${page - 1}, ${pCount}, ${rPP})" 
                    class="w-8 h-8 flex items-center justify-center mr-2 rounded hover:bg-white border border-transparent hover:border-gray-200 ${page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700'}" 
                    ${page === 1 ? 'disabled' : ''}>${prevIcon}</button>`;

                let range = [];
                if (pCount <= 5) {
                    for (let i = 1; i <= pCount; i++) range.push(i);
                } else {
                    if (page <= 3) range = [1, 2, 3, '...', pCount];
                    else if (page >= pCount - 2) range = [1, '...', pCount - 2, pCount - 1, pCount];
                    else range = [1, '...', page, '...', pCount];
                }

                range.forEach(item => {
                    if (item === '...') {
                        html += `<span class="w-8 h-8 flex items-center justify-center text-gray-400">...</span>`;
                    } else {
                        const isActive = item === page;
                        html += `<button onclick="renderPagination('${tId}', ${item}, ${pCount}, ${rPP})" 
                            class="w-8 h-8 flex items-center justify-center rounded mx-1 text-sm font-bold ${isActive ? 'bg-black text-white' : 'text-gray-600 hover:bg-white border border-transparent hover:border-gray-200'}">
                            ${item}</button>`;
                    }
                });

                html += `<button onclick="renderPagination('${tId}', ${page + 1}, ${pCount}, ${rPP})" 
                    class="w-8 h-8 flex items-center justify-center ml-2 rounded hover:bg-white border border-transparent hover:border-gray-200 ${page === pCount ? 'text-gray-300 cursor-not-allowed' : 'text-gray-700'}" 
                    ${page === pCount ? 'disabled' : ''}>${nextIcon}</button>`;

                pDiv.innerHTML = html;
            };

            window.renderPagination(tableId, 1, pageCount, rowsPerPage);
        }

        document.addEventListener('DOMContentLoaded', function() {
            paginateTable('table-predict-menu-tomorrow', 10);
            paginateTable('table-require-ing-tomorrow', 10);
            paginateTable('table-predict-menu-month', 10);
            paginateTable('table-require-ing-month', 10);
        });
    </script>
@endsection