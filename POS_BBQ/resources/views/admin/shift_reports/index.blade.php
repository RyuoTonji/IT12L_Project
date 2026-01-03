@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
                <h1 class="text-2xl font-semibold flex items-center">
                    <svg class="w-8 h-8 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Shift Reports
                </h1>
                <div class="flex flex-col md:flex-row items-center gap-4 w-full md:w-auto">
                    <form action="{{ route('admin.shift-reports.index') }}" method="GET"
                        class="flex flex-col md:flex-row items-center gap-2 w-full md:w-auto">
                        <input type="hidden" name="tab" value="{{ $activeTab }}">

                        <!-- Search Input -->
                        <div class="relative rounded-md shadow-sm w-full md:w-48">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search..."
                                class="w-full border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500 text-sm pl-8">
                            <div class="absolute inset-y-0 left-0 pl-2 flex items-center pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                        </div>

                        <!-- Date Filter -->
                        <div class="w-full md:w-auto">
                            <input type="date" name="date" id="date" value="{{ request('date') }}"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm"
                                onchange="this.form.submit()">
                        </div>

                        <!-- Staff Filter -->
                        <div class="w-full md:w-auto">
                            <select name="user_id" onchange="this.form.submit()"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                                <option value="">All Staff</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                    <a href="{{ route('admin.shift-reports.export-all', request()->all()) }}"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center inline-flex whitespace-nowrap mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export All
                    </a>
                    <a href="{{ route('admin.reports') }}"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center inline-flex whitespace-nowrap">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Reports
                    </a>
                </div>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    @foreach(['cashier' => 'Cashier', 'inventory' => 'Inventory', 'manager' => 'Manager'] as $key => $label)
                        <a href="{{ route('admin.shift-reports.index', ['tab' => $key]) }}"
                            class="{{ $activeTab === $key ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} 
                                                    whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                            {{ $label }}
                        </a>
                    @endforeach
                </nav>
            </div>

            @if($reports->isEmpty())
                <p class="text-gray-500 text-center py-8">No {{ $activeTab }} shift reports submitted yet.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Staff
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Written Report</th>

                                @if($activeTab === 'inventory')
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock In</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock Out</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remaining</th>
                                @else
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Orders</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sales</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Refunds</th>
                                @endif

                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($reports as $report)
                                <tr class="hover:bg-gray-50 transition-colors cursor-pointer"
                                    onclick="window.location='{{ route('admin.shift-reports.show', $report) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $report->shift_date->format('M d, Y') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $report->user->name }}</td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs">
                                            @if(in_array($report->report_type, ['inventory', 'inventory_start', 'inventory_end']))
                                                <span class="text-gray-600 italic">Detailed inventory report - Click to view</span>
                                            @else
                                                <span class="truncate" title="{{ $report->content }}">{{ Str::limit($report->content, 50) }}</span>
                                            @endif
                                        </div>
                                    </td>

                                    @if($activeTab === 'inventory')
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-green-600 font-medium">
                                            {{ number_format($report->stock_in, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-orange-600 font-medium">
                                            {{ number_format($report->stock_out, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-blue-600 font-medium">
                                            {{ number_format($report->remaining_stock, 2) }}
                                        </td>
                                    @else
                                        <td class="px-6 py-4 whitespace-nowrap text-center">{{ $report->total_orders }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-green-600">
                                            ₱{{ number_format($report->total_sales, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right font-medium text-red-600">
                                            ₱{{ number_format($report->total_refunds, 2) }}
                                        </td>
                                    @endif

                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <span
                                            class="text-xs font-semibold {{ $report->status == 'reviewed' ? 'text-green-600' : 'text-yellow-600' }}">
                                            {{ ucfirst($report->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $reports->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection