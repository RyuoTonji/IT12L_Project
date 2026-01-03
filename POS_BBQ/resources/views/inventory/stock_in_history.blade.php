@extends('layouts.inventory')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-green-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Stock-In History
                    </h3>
                    <p class="text-sm text-gray-500 ml-10">Track all stock-in transactions with before/after quantities</p>
                </div>
                <a href="{{ route('inventory.dashboard') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center inline-flex transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Inventory
                </a>
            </div>

            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form action="{{ route('inventory.stock-in-history') }}" method="GET" class="flex items-end gap-4">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Filter by Date</label>
                        <input type="date" name="date" id="date" value="{{ $date }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="this.form.submit()">
                    </div>
                    @if($date)
                        <a href="{{ route('inventory.stock-in-history') }}"
                            class="px-4 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300 flex items-center">
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

            <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Ingredient</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider text-green-600">
                                    Quantity Added</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Before</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    After</th>
                                <th
                                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider text-blue-600">
                                    Difference</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Recorded By</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($stockInHistory as $record)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $record->created_at->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $record->inventory->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-green-600 font-bold">
                                        +{{ number_format($record->quantity, 2) }} {{ $record->inventory->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">
                                        {{ number_format($record->quantity_before ?? 0, 2) }} {{ $record->inventory->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-600">
                                        {{ number_format($record->quantity_after ?? 0, 2) }} {{ $record->inventory->unit }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center font-bold text-blue-600">
                                        {{ number_format(($record->quantity_after ?? 0) - ($record->quantity_before ?? 0), 2) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ $record->reason }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $record->recorder->name }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-sm text-gray-500">
                                        @if($date)
                                            No stock-in records found for this date.
                                        @else
                                            No stock-in records found.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($stockInHistory->hasPages())
                <div class="mt-4">
                    {{ $stockInHistory->links('vendor.pagination.custom') }}
                </div>
            @endif
        </div>
    </div>
@endsection