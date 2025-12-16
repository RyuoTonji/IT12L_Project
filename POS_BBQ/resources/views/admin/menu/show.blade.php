<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Menu Item Details') }}
            </h2>
            <div>
                @if(request('source') === 'category')
                    <a href="{{ route('categories.show', $menu->category_id) }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Category
                    </a>
                @elseif(request('source') === 'report')
                    <a href="{{ route('admin.reports.items') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Reports
                    </a>
                @else
                    <a href="{{ route('menu.index') }}"
                        class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Menu
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Main Content Card (Reverted Style, spans 1 column) -->
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg h-fit">
                    <!-- Header with image -->
                    @if ($menu->image)
                        <div class="h-64 w-full overflow-hidden">
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}"
                                class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                        </div>
                    @endif

                    <!-- Content Section -->
                    <div class="p-8">
                        <div>
                            <!-- Title and Price Row -->
                            <div class="flex justify-between items-start mb-6">
                                <div>
                                    <h1 class="text-6xl font-black text-gray-900 mb-2">{{ $menu->name }}</h1>
                                    <div class="flex items-center">
                                        <span class="inline-flex items-center text-xl font-medium text-gray-500">
                                            {{ $menu->category->name }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right flex flex-col items-end gap-1">
                                    @if ($menu->is_available)
                                        <span class="inline-flex items-center text-lg font-bold text-green-600">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-lg font-bold text-red-600">
                                            <svg class="w-5 h-5 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                    clip-rule="evenodd"></path>
                                            </svg>
                                            Not Available
                                        </span>
                                    @endif

                                    <p class="text-3xl font-bold text-green-600">₱{{ number_format($menu->price, 2) }}
                                    </p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mb-8">
                                <div class="prose max-w-none">
                                    <p class="text-gray-700 text-lg leading-relaxed">{{ $menu->description }}</p>
                                </div>
                            </div>

                            <!-- Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                                <!-- Category Card -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                                            </path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900">Category</h3>
                                    </div>
                                    <p class="text-gray-600">{{ $menu->category->name }}</p>
                                </div>

                                <!-- Availability Card -->
                                <div class="bg-gray-50 rounded-lg p-6">
                                    <div class="flex items-center mb-3">
                                        <svg class="w-5 h-5 text-gray-600 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <h3 class="text-lg font-semibold text-gray-900">Availability</h3>
                                    </div>
                                    <p class="text-gray-600">
                                        {{ $menu->is_available ? 'Currently Available' : 'Currently Unavailable' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="pt-6 border-t border-gray-200">
                                <a href="{{ route('menu.edit', $menu->id) }}"
                                    class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                    Edit Menu Item
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Sales Graph (Spans 1 column) -->
                <div>
                    <div class="bg-white rounded-xl shadow-xl border border-gray-100 p-6 sticky top-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-bold text-gray-900">Sales Performance</h3>
                            <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">30
                                Days</span>
                        </div>

                        <div class="mb-6">
                            <div class="text-sm text-gray-500 mb-1">Total Sales</div>
                            <div class="text-3xl font-black text-gray-900">
                                ₱{{ number_format(collect($formattedSales)->sum('total_sales'), 2) }}
                            </div>
                        </div>

                        <div id="salesChart" class="w-full h-64"></div>

                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500">Total Quantity Sold</span>
                                <span
                                    class="font-bold text-gray-900">{{ collect($formattedSales)->sum('total_quantity') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const salesData = @json($formattedSales);

            const options = {
                series: [{
                    name: 'Sales',
                    data: salesData.map(item => item.total_sales)
                }],
                chart: {
                    type: 'area',
                    height: 250,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit'
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3,
                    colors: ['#4F46E5'] // Indigo-600
                },
                xaxis: {
                    categories: salesData.map(item => item.date),
                    labels: {
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
                    },
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    }
                },
                yaxis: {
                    labels: {
                        formatter: function (value) {
                            return '₱' + value.toLocaleString();
                        },
                        style: {
                            colors: '#6B7280',
                            fontSize: '12px'
                        }
                    }
                },
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.7,
                        opacityTo: 0.3,
                        stops: [0, 90, 100],
                        colorStops: [
                            {
                                offset: 0,
                                color: '#4F46E5',
                                opacity: 0.5
                            },
                            {
                                offset: 100,
                                color: '#4F46E5',
                                opacity: 0.1
                            }
                        ]
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
                grid: {
                    strokeDashArray: 4,
                    borderColor: '#E5E7EB'
                }
            };

            const chart = new ApexCharts(document.querySelector("#salesChart"), options);
            chart.render();
        });
    </script>
</x-app-layout>