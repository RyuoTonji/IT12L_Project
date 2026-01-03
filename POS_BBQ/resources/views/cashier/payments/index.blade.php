@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Payments</h1>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Payment ID</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Order #</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Method</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-50 cursor-pointer transition-colors"
                                onclick="showPaymentDetails({{ $payment->id }})">
                                <td class="py-2 px-4">{{ $payment->id }}</td>
                                <td class="py-2 px-4">
                                    <span class="text-blue-600 font-medium">
                                        Order #{{ $payment->order_id }}
                                    </span>
                                </td>
                                <td class="py-2 px-4">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="py-2 px-4 text-right">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="py-2 px-4 text-center">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-gray-500">No payments found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $payments->links() }}
            </div>
        </div>
    </div>

    <!-- Payment Details Modal -->
    <div id="payment-details-modal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-10 mx-auto p-5 border w-11/12 max-w-4xl shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Payment Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div id="modal-content" class="px-4 py-3">
                    <!-- Content will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <script>
        const paymentsData = {
            @foreach($payments as $payment)
                {{ $payment->id }}: {
                    id: {{ $payment->id }},
                    order_id: {{ $payment->order_id }},
                    payment_method: "{{ ucfirst($payment->payment_method) }}",
                    amount: "{{ number_format($payment->amount, 2) }}",
                    date: "{{ $payment->created_at->format('M d, Y H:i') }}",
                    order: {
                        id: {{ $payment->order->id }},
                        order_type: "{{ ucfirst($payment->order->order_type) }}",
                        status: "{{ ucfirst($payment->order->status) }}",
                        payment_status: "{{ ucfirst($payment->order->payment_status) }}",
                        customer_name: "{{ $payment->order->customer_name }}",
                        table_name: "{{ $payment->order->table ? $payment->order->table->name : '' }}",
                        total_amount: "{{ number_format($payment->order->total_amount, 2) }}",
                        created_by: "{{ $payment->order->user->name }}",
                        created_at: "{{ $payment->order->created_at->format('M d, Y H:i') }}",
                        items: [
                            @foreach($payment->order->orderItems as $item)
                                {
                                    name: "{{ $item->menuItem->name }}",
                                    quantity: {{ $item->quantity }},
                                    unit_price: "{{ number_format($item->unit_price, 2) }}",
                                    subtotal: "{{ number_format($item->unit_price * $item->quantity, 2) }}",
                                    notes: "{{ $item->notes ?? '' }}"
                                }{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        ],
                        void_requests: [
                            @foreach($payment->order->voidRequests as $request)
                                {
                                    status: "{{ ucfirst($request->status) }}",
                                    reason: "{{ $request->reason ?? '' }}",
                                    reason_tags: @json(is_array($request->reason_tags) ? $request->reason_tags : json_decode($request->reason_tags, true)),
                                    requester: "{{ $request->requester->name }}",
                                    created_at: "{{ $request->created_at->format('M d, Y H:i') }}"
                                }{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        ]
                    }
                }{{ !$loop->last ? ',' : '' }}
            @endforeach
        };

        function showPaymentDetails(paymentId) {
            const payment = paymentsData[paymentId];
            if (!payment) return;

            const order = payment.order;

            let itemsHtml = '';
            order.items.forEach(item => {
                itemsHtml += `
                        <tr>
                            <td class="py-2 px-4">${item.name}</td>
                            <td class="py-2 px-4 text-right">₱${item.unit_price}</td>
                            <td class="py-2 px-4 text-center">${item.quantity}</td>
                            <td class="py-2 px-4">${item.notes || '-'}</td>
                            <td class="py-2 px-4 text-right">₱${item.subtotal}</td>
                        </tr>
                    `;
            });

            let voidRequestHtml = '';
            if (order.void_requests && order.void_requests.length > 0) {
                order.void_requests.forEach(request => {
                    const statusColor = request.status === 'Approved' ? 'text-green-600' :
                        request.status === 'Rejected' ? 'text-red-600' : 'text-yellow-600';

                    let reasonTagsHtml = '';
                    if (request.reason_tags && request.reason_tags.length > 0) {
                        reasonTagsHtml = request.reason_tags.map(tag => {
                            const displayTag = tag.replace(/_/g, ' ');
                            return `<span class="inline-block bg-gray-200 text-gray-700 px-2 py-1 rounded text-xs mr-1">${displayTag}</span>`;
                        }).join('');
                    }

                    voidRequestHtml += `
                            <div class="bg-yellow-50 border border-yellow-200 p-4 rounded-lg mb-4">
                                <div class="flex justify-between items-start mb-2">
                                    <h4 class="font-semibold text-gray-900">Void/Refund Request</h4>
                                    <span class="font-semibold ${statusColor}">${request.status}</span>
                                </div>
                                <div class="text-sm text-gray-700 space-y-1">
                                    <p><strong>Requested by:</strong> ${request.requester}</p>
                                    <p><strong>Date:</strong> ${request.created_at}</p>
                                    ${reasonTagsHtml ? `<p><strong>Reason Tags:</strong><br>${reasonTagsHtml}</p>` : ''}
                                    ${request.reason ? `<p><strong>Additional Notes:</strong> ${request.reason}</p>` : ''}
                                </div>
                            </div>
                        `;
                });
            }

            const modalContent = `
                    ${voidRequestHtml}

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <h4 class="text-md font-medium mb-3">Payment Information</h4>
                            <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Payment ID:</span>
                                    <span class="font-medium">${payment.id}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Method:</span>
                                    <span class="font-medium">${payment.payment_method}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Amount:</span>
                                    <span class="font-medium">₱${payment.amount}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Date:</span>
                                    <span class="font-medium">${payment.date}</span>
                                </div>
                            </div>
                        </div>

                        <div>
                            <h4 class="text-md font-medium mb-3">Order Information</h4>
                            <div class="bg-gray-50 p-4 rounded-lg space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Order #:</span>
                                    <span class="font-medium">${order.id}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Type:</span>
                                    <span class="font-medium">${order.order_type}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">${order.order_type === 'Dine-in' ? 'Table' : 'Customer'}:</span>
                                    <span class="font-medium">${order.table_name || order.customer_name || 'Takeout'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-medium">${order.status}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Created By:</span>
                                    <span class="font-medium">${order.created_by}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-6">
                        <h4 class="text-md font-medium mb-3">Order Items</h4>
                        <div class="bg-white rounded-lg border overflow-hidden">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase">Item</th>
                                        <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase">Price</th>
                                        <th class="py-2 px-4 text-center text-xs font-medium text-gray-500 uppercase">Qty</th>
                                        <th class="py-2 px-4 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                        <th class="py-2 px-4 text-right text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    ${itemsHtml}
                                </tbody>
                                <tfoot class="bg-gray-50">
                                    <tr>
                                        <td colspan="4" class="py-2 px-4 text-right font-bold">Total:</td>
                                        <td class="py-2 px-4 text-right font-bold">₱${order.total_amount}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <a href="/cashier/orders/${order.id}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            View Full Order
                        </a>
                    </div>
                `;

            document.getElementById('modal-content').innerHTML = modalContent;
            document.getElementById('payment-details-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('payment-details-modal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('payment-details-modal')?.addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });

        // Close modal with ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
@endsection