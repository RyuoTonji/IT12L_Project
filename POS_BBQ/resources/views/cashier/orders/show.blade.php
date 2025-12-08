@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">


            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Order #{{ $order->id }}</h1>
                <div>
                    <a href="{{ route('orders.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Orders
                    </a>

                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <a href="{{ route('orders.edit', $order) }}"
                            class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-2 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Edit Order
                        </a>
                    @endif

                    @if($order->payment_status == 'pending')
                        <a href="{{ route('payments.create', ['order' => $order->id]) }}"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Process Payment
                        </a>
                    @endif

                    @if($order->status != 'cancelled')
                        @if($order->voidRequests()->where('status', 'pending')->exists())
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded border border-yellow-300 ml-2">
                                Void/Refund Requested
                            </span>
                        @else
                            <button onclick="document.getElementById('void-request-modal').classList.remove('hidden')"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 ml-2 flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                Request Void/Refund
                            </button>
                        @endif
                    @endif

                    @if((Auth::user()->role == 'admin' || Auth::user()->role == 'manager') && $order->status != 'cancelled')
                        <form id="forceVoidForm" action="{{ route('orders.destroy', $order) }}" method="POST"
                            class="inline-block ml-2">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmForceVoid()"
                                class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-900 flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Force Void
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
            </div> @endif @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3
                                                                                                rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-lg font-medium mb-4">Order Information</h2>

                    <div class="bg-white p-4 rounded-lg shadow border">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Order Type</p>
                                <p class="font-medium">{{ ucfirst($order->order_type) }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">
                                    {{ $order->order_type == 'dine-in' ? 'Table' : 'Customer' }}
                                </p>
                                <p class="font-medium">
                                    @if($order->order_type == 'dine-in')
                                        {{ $order->table ? $order->table->name : 'N/A' }}
                                    @else
                                        {{ $order->customer_name ?: 'Takeout' }}
                                    @endif
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Status</p>
                                <p>
                                    <span
                                        class="px-2 py-1 rounded text-xs font-semibold
                                                                    {{ $order->status == 'completed' ? 'bg-green-100 text-green-800' :
        ($order->status == 'cancelled' ? 'bg-red-100 text-red-800' :
            ($order->status == 'new' ? 'bg-blue-100 text-blue-800' :
                ($order->status == 'preparing' ? 'bg-yellow-100 text-yellow-800' :
                    ($order->status == 'ready' ? 'bg-indigo-100 text-indigo-800' : 'bg-purple-100 text-purple-800')))) }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Payment Status</p>
                                <p>
                                    <span class="px-2 py-1 rounded text-xs font-semibold
                                                                                {{ $order->payment_status == 'paid' ? 'bg-green-100 text-green-800' :
        ($order->payment_status == 'refunded' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ ucfirst($order->payment_status) }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Created By</p>
                                <p class="font-medium">{{ $order->user->name }}</p>
                            </div>

                            <div>
                                <p class="text-sm text-gray-600">Created At</p>
                                <p class="font-medium">{{ $order->created_at->format('M d, Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-medium mb-4">Order Summary</h2>

                    <div class="bg-white p-4 rounded-lg shadow border">
                        <div class="flex justify-between mb-2">
                            <span class="font-medium">Total Items:</span>
                            <span>{{ $order->orderItems->sum('quantity') }}</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="font-medium">Subtotal:</span>
                            <span>₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between text-lg font-bold">
                            <span>Total:</span>
                            <span>₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mb-6">
                <h2 class="text-lg font-medium mb-4">Order Items</h2>

                <div class="bg-white rounded-lg shadow border overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Item</th>
                                <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Price</th>
                                <th
                                    class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity</th>
                                <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Notes</th>
                                <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td class="py-3 px-4">{{ $item->menuItem->name }}</td>
                                    <td class="py-3 px-4 text-right">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="py-3 px-4 text-center">{{ $item->quantity }}</td>
                                    <td class="py-3 px-4">{{ $item->notes ?: '-' }}</td>
                                    <td class="py-3 px-4 text-right">
                                        ₱{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-50">
                                <td colspan="4" class="py-3 px-4 text-right font-bold">Total:</td>
                                <td class="py-3 px-4 text-right font-bold">₱{{ number_format($order->total_amount, 2) }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @if($order->payments->count() > 0)
                <div>
                    <h2 class="text-lg font-medium mb-4">Payment History</h2>

                    <div class="bg-white rounded-lg shadow border overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment ID</th>
                                    <th class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Method</th>
                                    <th class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($order->payments as $payment)
                                    <tr>
                                        <td class="py-3 px-4">{{ $payment->id }}</td>
                                        <td class="py-3 px-4">{{ ucfirst($payment->payment_method) }}</td>
                                        <td class="py-3 px-4 text-right">₱{{ number_format($payment->amount, 2) }}</td>
                                        <td class="py-3 px-4 text-center">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Void Request Modal -->
    <div id="void-request-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Request Order Void/Refund</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500 mb-4">
                        Please provide a reason for voiding/refunding this order. This will be sent to a manager/admin for
                        approval.
                    </p>
                    <form action="{{ route('orders.request-void', $order) }}" method="POST" id="void-request-form">
                        @csrf
                        <div class="text-left mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Reason for Void/Refund:</label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="reason_tags[]" value="Customer Changed Mind" class="mr-2">
                                    <span class="text-sm">Customer Changed Mind</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="reason_tags[]" value="Dish Issue/s" class="mr-2">
                                    <span class="text-sm">Dish Issue/s</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="reason_tags[]" value="Special Concern (Allergic Reaction)"
                                        class="mr-2">
                                    <span class="text-sm">Special Concern (Allergic Reaction)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="reason_tags[]" value="Others" class="mr-2"
                                        id="other-checkbox">
                                    <span class="text-sm">Others</span>
                                </label>
                            </div>
                        </div>
                        <div id="other-reason-container" class="mb-4 hidden">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Please specify:</label>
                            <textarea name="reason" rows="2" class="w-full border rounded p-2"
                                placeholder="Specify other reason..."></textarea>
                        </div>
                        <div class="flex justify-end space-x-2">
                            <button type="button"
                                onclick="document.getElementById('void-request-modal').classList.add('hidden')"
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400 flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                                Submit Request
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle "Others" checkbox to show/hide textarea
            const otherCheckbox = document.getElementById('other-checkbox');
            const otherReasonContainer = document.getElementById('other-reason-container');

            if (otherCheckbox) {
                otherCheckbox.addEventListener('change', function () {
                    if (this.checked) {
                        otherReasonContainer.classList.remove('hidden');
                    } else {
                        otherReasonContainer.classList.add('hidden');
                    }
                });
            }

            // Handle void request form submission with confirmation
            const voidRequestForm = document.getElementById('void-request-form');
            if (voidRequestForm) {
                voidRequestForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    // Check if at least one checkbox is selected
                    const checkboxes = voidRequestForm.querySelectorAll('input[name="reason_tags[]"]');
                    const isAnyChecked = Array.from(checkboxes).some(cb => cb.checked);

                    if (!isAnyChecked) {
                        alert('Please select at least one reason for the void/refund request.');
                        return;
                    }

                    showConfirm('Are you sure you want to submit this void/refund request?', function () {
                        voidRequestForm.submit();
                    });
                });
            }
        });

        function confirmForceVoid() {
            AlertModal.showConfirm(
                'Are you sure you want to VOID this order? This action cannot be undone.',
                function () {
                    document.getElementById('forceVoidForm').submit();
                },
                null,
                'Force Void Confirmation'
            );
        }
    </script>
@endsection