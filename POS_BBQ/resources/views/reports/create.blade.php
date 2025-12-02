@extends('layouts.' . (Auth::user()->role === 'server' || Auth::user()->role === 'griller' ? 'app' : Auth::user()->role))

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-semibold mb-6">Create Shift Report</h1>

            <form action="{{ route('shift-reports.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Shift Date</label>
                    <input type="date" name="shift_date" value="{{ $today->format('Y-m-d') }}"
                        class="w-full border rounded p-2" required>
                </div>

                @if($reportType === 'sales')
                    {{-- Sales Report Statistics --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $totalOrders }}</p>
                        </div>
                        <div class="bg-green-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Sales</p>
                            <p class="text-2xl font-bold text-green-600">₱{{ number_format($totalSales, 2) }}</p>
                        </div>
                        <div class="bg-red-50 p-4 rounded-lg">
                            <p class="text-sm text-gray-600">Total Refunds</p>
                            <p class="text-2xl font-bold text-red-600">₱{{ number_format($totalRefunds, 2) }}</p>
                        </div>
                    </div>
                @else
                    {{-- Inventory Report Fields --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock In (End of Day)</label>
                            <input type="number" name="stock_in" step="0.01" min="0" required class="w-full border rounded p-2"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Stock Out (End of Day)</label>
                            <input type="number" name="stock_out" step="0.01" min="0" required class="w-full border rounded p-2"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Remaining Stock</label>
                            <input type="number" name="remaining_stock" step="0.01" min="0" required
                                class="w-full border rounded p-2" placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Spoilage</label>
                            <input type="number" name="spoilage" step="0.01" min="0" class="w-full border rounded p-2"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Returns</label>
                            <input type="number" name="returns" step="0.01" min="0" class="w-full border rounded p-2"
                                placeholder="0.00">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Return Reason (if any)</label>
                            <input type="text" name="return_reason" class="w-full border rounded p-2"
                                placeholder="Describe reason for returns">
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Report Content</label>
                    <textarea name="content" rows="8" class="w-full border rounded p-2"
                        placeholder="Write your shift report here... Include any notable events, issues, or observations from your shift."
                        required></textarea>
                    <p class="text-sm text-gray-500 mt-1">Include details about your shift, any issues encountered,
                        @if($reportType === 'sales')
                            customer feedback, or other relevant information.
                        @else
                            inventory issues, damaged goods, or other relevant observations.
                        @endif
                    </p>
                </div>

                <div class="flex justify-end space-x-2">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</a>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Submit
                        Report</button>
                </div>
            </form>
        </div>
    </div>
@endsection