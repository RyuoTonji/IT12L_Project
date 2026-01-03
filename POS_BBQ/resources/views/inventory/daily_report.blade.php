@extends('layouts.inventory')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-blue-600" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Daily Inventory Reports
                    </h3>
                    <p class="text-sm text-gray-500 ml-10">Automated inventory tracking for start and end of day</p>
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

            <!-- Report Forms -->
            <div class="space-y-8 mb-8">
                <!-- Start of Day Report -->
                <div class="bg-gradient-to-br from-blue-50 to-white border-2 border-blue-200 rounded-lg p-6 shadow-sm">
                    <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        Start of Day Report ({{ $today->format('M d, Y') }})
                    </h4>

                    @if($startOfDayReport)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-green-800 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Submitted at {{ $startOfDayReport->created_at->format('h:i A') }}
                            </p>
                        </div>
                    @else
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-2/3">
                                <div class="bg-white rounded border overflow-hidden shadow-sm h-96 overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500">Item Name</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-500">Opening</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-500">Added Today</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-500">Available</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($reportData as $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-gray-900">{{ $item->name }}</td>
                                                    <td class="px-3 py-2 text-center text-gray-600">{{ number_format($item->start_count, 2) }} {{ $item->unit }}</td>
                                                    <td class="px-3 py-2 text-center text-green-600 font-medium">{{ $item->stock_in > 0 ? '+' . number_format($item->stock_in, 2) : '-' }}</td>
                                                    <td class="px-3 py-2 text-center text-blue-700 font-bold bg-blue-50">{{ number_format($item->start_count + $item->stock_in, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            
                            <div class="md:w-1/3">
                                <form action="{{ route('inventory.daily-report.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="report_type" value="inventory_start">
                                    <input type="hidden" name="shift_date" value="{{ $today->format('Y-m-d') }}">
                                    <!-- Send entire automated dataset -->
                                    <input type="hidden" name="report_json" value="{{ json_encode($reportData) }}">

                                    <div class="mb-4">
                                        <label for="start_content" class="block text-sm font-medium text-gray-700 mb-1">Remarks / Issues</label>
                                        <textarea name="content" id="start_content" rows="4" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                            placeholder="Any discrepancies or notes about the starting inventory..."></textarea>
                                    </div>

                                    <div class="bg-blue-50 p-3 rounded mb-4 text-xs text-blue-800">
                                        <p><strong>Note:</strong> The table on the left shows the system-recorded inventory. By submitting, you confirm these quantities are accurate.</p>
                                    </div>

                                    <button type="submit"
                                        class="w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 flex items-center justify-center transition-colors duration-200 shadow font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Confirm & Submit Start Report
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- End of Day Report -->
                <div class="bg-gradient-to-br from-orange-50 to-white border-2 border-orange-200 rounded-lg p-6 shadow-sm">
                    <h4 class="text-lg font-semibold text-orange-800 mb-4 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                        End of Day Report ({{ $today->format('M d, Y') }})
                    </h4>

                    @if($endOfDayReport)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-green-800 font-medium flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Submitted at {{ $endOfDayReport->created_at->format('h:i A') }}
                            </p>
                        </div>
                    @else
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-2/3">
                                <div class="bg-white rounded border overflow-hidden shadow-sm h-96 overflow-y-auto">
                                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                                        <thead class="bg-gray-50 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-3 py-2 text-left font-medium text-gray-500">Item Name</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-500">Added</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-500">Sold (Predicted)</th>
                                                <th class="px-3 py-2 text-center font-medium text-gray-500">Remaining</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200">
                                            @foreach($reportData as $item)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-3 py-2 text-gray-900">{{ $item->name }}</td>
                                                    <td class="px-3 py-2 text-center text-green-600">{{ $item->stock_in > 0 ? '+' . number_format($item->stock_in, 2) : '-' }}</td>
                                                    <td class="px-3 py-2 text-center text-orange-600">{{ $item->stock_out > 0 ? '-' . number_format($item->stock_out, 2) : '-' }}</td>
                                                    <td class="px-3 py-2 text-center text-blue-700 font-bold">{{ number_format($item->end_count, 2) }} {{ $item->unit }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="md:w-1/3">
                                <form action="{{ route('inventory.daily-report.store') }}" method="POST" id="endOfDayForm">
                                    @csrf
                                    <input type="hidden" name="report_type" value="inventory_end">
                                    <input type="hidden" name="shift_date" value="{{ $today->format('Y-m-d') }}">
                                    <!-- Send entire automated dataset -->
                                    <input type="hidden" name="report_json" value="{{ json_encode($reportData) }}">

                                    <!-- Optional Manual Adjustments -->
                                    <div class="grid grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label for="end_spoilage" class="block text-sm font-medium text-gray-700 mb-1">Total Spoilage</label>
                                            <input type="number" name="spoilage" id="end_spoilage" step="0.01" min="0" placeholder="0"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                        </div>
                                        <div>
                                            <label for="end_returns" class="block text-sm font-medium text-gray-700 mb-1">Total Returns</label>
                                            <input type="number" name="returns" id="end_returns" step="0.01" min="0" placeholder="0" onchange="toggleReturnReason()"
                                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50">
                                        </div>
                                    </div>

                                    <div class="mb-4" id="returnReasonDiv" style="display: none;">
                                        <label for="end_return_reason" class="block text-sm font-medium text-gray-700 mb-1">Return Reason <span class="text-red-500">*</span></label>
                                        <textarea name="return_reason" id="end_return_reason" rows="2"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                            placeholder="Describe reason for returns..."></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <label for="end_content" class="block text-sm font-medium text-gray-700 mb-1">Report Remarks</label>
                                        <textarea name="content" id="end_content" rows="3" 
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-orange-300 focus:ring focus:ring-orange-200 focus:ring-opacity-50"
                                            placeholder="Observations, issues with count, etc..."></textarea>
                                    </div>

                                    <div class="bg-orange-50 p-3 rounded mb-4 text-xs text-orange-800">
                                        <p><strong>Note:</strong> Sales data is automatically calculated from orders. Verify the remaining stock matches physical count.</p>
                                    </div>

                                    <button type="submit"
                                        class="w-full px-4 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 flex items-center justify-center transition-colors duration-200 shadow font-semibold">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                        Confirm & Submit End Report
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Report History -->
            <div class="mt-8">
                <h4 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Report History
                </h4>

                @if($reportHistory->isEmpty())
                    <p class="text-gray-500 text-center py-8 bg-gray-50 rounded-lg">No reports submitted yet.</p>
                @else
                    <div class="bg-white border rounded-lg overflow-hidden shadow-sm">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">In/Added</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Out/Sold</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Est. Remaining</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($reportHistory as $report)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ $report->shift_date->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($report->report_type === 'inventory_start')
                                                    <span class="text-xs font-semibold text-blue-600">Start of Day</span>
                                                @else
                                                    <span class="text-xs font-semibold text-orange-600">End of Day</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-green-600 font-medium">
                                                {{ number_format($report->stock_in, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-orange-600 font-medium">
                                                {{ number_format($report->stock_out, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-900 font-semibold">
                                                {{ number_format($report->remaining_stock, 2) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                <span class="text-xs font-semibold {{ $report->status == 'reviewed' ? 'text-green-600' : 'text-yellow-600' }}">
                                                    {{ ucfirst($report->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                <!-- Add View Details link if needed later -->
                                                <span class="text-gray-400">View (Admin)</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>

                    @if($reportHistory->hasPages())
                        <div class="mt-4">
                            {{ $reportHistory->links('vendor.pagination.custom') }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <script>
        function toggleReturnReason() {
            const returnsInput = document.getElementById('end_returns');
            const returnReasonDiv = document.getElementById('returnReasonDiv');
            const returnReasonTextarea = document.getElementById('end_return_reason');
            
            if (returnsInput && parseFloat(returnsInput.value) > 0) {
                returnReasonDiv.style.display = 'block';
                returnReasonTextarea.required = true;
            } else {
                returnReasonDiv.style.display = 'none';
                returnReasonTextarea.required = false;
                returnReasonTextarea.value = '';
            }
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', toggleReturnReason);
    </script>
@endsection
