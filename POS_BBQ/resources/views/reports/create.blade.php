@extends('layouts.' . (Auth::user()->role === 'server' || Auth::user()->role === 'griller' ? 'app' : Auth::user()->role))

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900" x-data="{ activeTab: 'create' }">
            <h1 class="text-2xl font-semibold mb-6">Shift Reports</h1>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button @click="activeTab = 'create'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'create', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'create' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Create Report
                    </button>
                    <button @click="activeTab = 'list'"
                        :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'list', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'list' }"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Report List
                    </button>
                </nav>
            </div>

            <!-- Create Report Tab -->
            <div x-show="activeTab === 'create'">
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
                                <input type="number" name="stock_in" step="0.01" min="0" required
                                    class="w-full border rounded p-2" placeholder="0.00">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Out (End of Day)</label>
                                <input type="number" name="stock_out" step="0.01" min="0" required
                                    class="w-full border rounded p-2" placeholder="0.00">
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
                            class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Cancel
                        </a>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Submit Report
                        </button>
                    </div>
                </form>
            </div>

            <!-- Report List Tab -->
            <div x-show="activeTab === 'list'" style="display: none;">
                @if($reportHistory->isEmpty())
                    <p class="text-gray-500 text-center py-8">You haven't submitted any reports yet.</p>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    @if($reportType === 'sales')
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Sales</th>
                                    @endif
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Submitted At</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($reportHistory as $report)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $report->shift_date->format('M d, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($report->report_type) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                            {{ $report->status === 'reviewed' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ ucfirst($report->status) }}
                                            </span>
                                        </td>
                                        @if($reportType === 'sales')
                                            <td class="px-6 py-4 whitespace-nowrap">₱{{ number_format($report->total_sales, 2) }}</td>
                                        @endif
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $report->created_at->format('M d, Y H:i') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            {{-- Assuming there might be a show route for users later, or just export --}}
                                            @if(Auth::user()->role === 'manager' || Auth::user()->role === 'admin')
                                                {{-- Managers might access via admin route or manager route --}}
                                            @endif
                                            <!-- Simple view button logic if route exists or just status -->
                                            <span class="text-gray-500 text-xs">View/Export (Coming Soon)</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection