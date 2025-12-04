@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">


            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Order #{{ $order->id }}</h1>
                <div>
                    <a href="{{ route('orders.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2">Back to Orders</a>

                    @if(!in_array($order->status, ['completed', 'cancelled']))
                        <a href="{{ route('orders.edit', $order) }}"
                            class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 mr-2">Edit Order</a>
                    @endif

                    @if($order->payment_status == 'pending')
                        <a href="{{ route('payments.create', ['order' => $order->id]) }}"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Process Payment</a>
                    @endif

                    @if($order->status != 'cancelled')
                        @if($order->voidRequests()->where('status', 'pending')->exists())
                            <span class="px-4 py-2 bg-yellow-100 text-yellow-800 rounded border border-yellow-300 ml-2">
                                Void/Refund Requested
                            </span>
                        @else
                            <button onclick="document.getElementById('void-request-modal').classList.remove('hidden')"
                                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 ml-2">
                                Request Void/Refund
                            </button>
                        @endif
                    @endif

                    @if((Auth::user()->role == 'admin' || Auth::user()->role == 'manager') && $order->status != 'cancelled')
                        <form action="{{ route('orders.destroy', $order) }}" method="POST" class="inline-block ml-2"
                            onsubmit="return confirm('Are you sure you want to VOID this order? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 bg-red-800 text-white rounded hover:bg-red-900">Force
                                Void</button>
                        </form>
                    @endif
                </div>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
            </div> @endif @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3
                                                rounded relative mb-4" role="alert">
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
                        Please provide a reason for voiding/refunding this order. This will be sent to a manager/admin for approval.
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
                                    <input type="checkbox" name="reason_tags[]" value="Special Concern (Allergic Reaction)" class="mr-2">
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
                                class="px-4 py-2 bg-gray-300 text-gray-800 rounded hover:bg-gray-400">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">Submit
                                Request</button>
                        </div>
                    </form>
                    <script>
                        document.getElementById('other-checkbox').addEventListener('change', function () {
                            const container = document.getElementById('other-reason-container');
                            if (this.checked) {
                                container.classList.remove('hidden');
                            } else {
                                container.classList.add('hidden');
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection