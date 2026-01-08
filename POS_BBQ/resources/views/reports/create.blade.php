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
                            class="w-full border rounded p-2 bg-gray-100 cursor-not-allowed" readonly>
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

                    @if($reportType === 'sales' && isset($orders))
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Shift Orders</h3>
                            <div class="overflow-x-auto border rounded-lg">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Order ID</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Time</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Type</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Status</th>
                                            <th
                                                class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @forelse($orders as $order)
                                                                <tr class="hover:bg-gray-50 cursor-pointer transition-colors duration-150"
                                                                    onclick="document.getElementById('order-modal-{{ $order->id }}').classList.remove('hidden')">
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                                        #{{ $order->id }}</td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ $order->created_at->format('h:i A') }}</td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                                        {{ ucfirst($order->order_type) }}</td>
                                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                                        <span
                                                                            class="inline-flex text-xs leading-5 font-semibold 
                                                                                        {{ $order->payment_status === 'paid' ? 'text-green-600' :
                                            ($order->payment_status === 'refunded' ? 'text-red-600' : 'text-yellow-600') }}">
                                                                            {{ ucfirst($order->payment_status) }}
                                                                        </span>
                                                                    </td>
                                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                                        ₱{{ number_format($order->total_amount, 2) }}</td>
                                                                </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5"
                                                    class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">No orders
                                                    found for this shift.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Order Details Modals --}}
                        @foreach($orders as $order)
                                <div id="order-modal-{{ $order->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto"
                                    aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                    <div
                                        class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                                        <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true"
                                            onclick="document.getElementById('order-modal-{{ $order->id }}').classList.add('hidden')">
                                        </div>
                                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen"
                                            aria-hidden="true">&#8203;</span>
                                        <div
                                            class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">

                                            <!-- Close button -->
                                            <div class="absolute top-0 right-0 pt-4 pr-4 z-10">
                                                <button type="button"
                                                    class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                                                    onclick="document.getElementById('order-modal-{{ $order->id }}').classList.add('hidden')">
                                                    <span class="sr-only">Close</span>
                                                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                <h2 class="text-xl font-semibold mb-4">Order Details #{{ $order->id }}</h2>

                                                <div class="space-y-4">
                                                    <!-- Order Information -->
                                                    <div class="border-b pb-3">
                                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                                            <div>
                                                                <span class="text-gray-600">Table:</span>
                                                                <span class="font-medium">{{ $order->table->name ?? 'Takeout' }}</span>
                                                            </div>
                                                            <div>
                                                                <span class="text-gray-600">Status:</span>
                                                                <span
                                                                    class="inline-flex text-xs leading-5 font-semibold items-center
                                                                                {{ $order->status == 'completed' ? 'text-green-600' :
                            ($order->status == 'cancelled' ? 'text-red-600' : 'text-yellow-600') }}">
                                                                    @if($order->status == 'completed')
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                                        </svg>
                                                                    @elseif($order->status == 'cancelled')
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    @else
                                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1"
                                                                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                                stroke-width="2"
                                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                        </svg>
                                                                    @endif
                                                                    {{ ucfirst($order->status) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Order Items -->
                                                    <div>
                                                        <h3 class="font-semibold mb-2">Order Items</h3>
                                                        <div class="space-y-2">
                                                            @foreach($order->orderItems as $item)
                                                                <div class="flex justify-between items-start text-sm">
                                                                    <div class="flex-1">
                                                                        <span class="font-medium">{{ $item->quantity }}x
                                                                            {{ $item->menuItem->name }}</span>
                                                                        @if($item->notes)
                                                                            <p class="text-xs text-gray-500 italic ml-4">Note:
                                                                                {{ $item->notes }}</p>
                                                                        @endif
                                                                    </div>
                                                                    <span
                                                                        class="font-medium">₱{{ number_format($item->unit_price * $item->quantity, 2) }}</span>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>

                                                    <!-- Order Total -->
                                                    <div class="border-t pt-3">
                                                        <div class="flex justify-between items-center">
                                                            <span class="text-lg font-semibold">Total Amount:</span>
                                                            <span
                                                                class="text-xl font-bold">₱{{ number_format($order->total_amount, 2) }}</span>
                                                        </div>
                                                    </div>

                                                    <!-- Payment Information if available -->
                                                    <div class="border-t pt-3">
                                                        <h3 class="font-semibold mb-2">Payment Details</h3>
                                                        <div class="grid grid-cols-2 gap-2 text-sm">
                                                            <div>
                                                                <span class="text-gray-600">Method:</span>
                                                                <span class="font-medium">
                                                                    @if($order->payments && $order->payments->isNotEmpty())
                                                                        {{ ucfirst($order->payments->first()->payment_method) }}
                                                                    @else
                                                                        Pending/Cash
                                                                    @endif
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <span class="text-gray-600">Status:</span>
                                                                <span
                                                                    class="inline-flex items-center text-xs leading-5 font-semibold
                                                                                                    {{ $order->payment_status == 'paid' ? 'text-green-600' :
                            ($order->payment_status == 'refunded' ? 'text-red-600' : 'text-yellow-600') }}">
                                                                    {{ ucfirst($order->payment_status) }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Timestamps -->
                                                    <div class="border-t pt-3 text-xs text-gray-500">
                                                        <div>Created: {{ $order->created_at->format('M d, Y h:i A') }}</div>
                                                        @if($order->updated_at != $order->created_at)
                                                            <div>Updated: {{ $order->updated_at->format('M d, Y h:i A') }}</div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        @endforeach
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
                                                class="inline-flex text-xs leading-5 font-semibold
                                                                                    {{ $report->status === 'reviewed' ? 'text-green-600' : 'text-yellow-600' }}">
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