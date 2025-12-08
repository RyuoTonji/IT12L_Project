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
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($payments as $payment)
                            <tr>
                                <td class="py-2 px-4">{{ $payment->id }}</td>
                                <td class="py-2 px-4">
                                    <a href="{{ route('orders.show', $payment->order) }}"
                                        class="text-blue-600 hover:text-blue-900">
                                        Order #{{ $payment->order_id }}
                                    </a>
                                </td>
                                <td class="py-2 px-4">{{ ucfirst($payment->payment_method) }}</td>
                                <td class="py-2 px-4 text-right">₱{{ number_format($payment->amount, 2) }}</td>
                                <td class="py-2 px-4 text-center">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                                <td class="py-2 px-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="{{ route('payments.show', $payment) }}"
                                            class="text-blue-600 hover:text-blue-900 flex items-center" title="View">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            View
                                        </a>

                                        @if($payment->order->status != 'completed')
                                            <form id="refundForm{{ $payment->id }}"
                                                action="{{ route('payments.destroy', $payment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="text-red-600 hover:text-red-900 flex items-center"
                                                    onclick="confirmRefund({{ $payment->id }})" title="Refund">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                                                    </svg>
                                                    Refund
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-4 px-4 text-center text-gray-500">No payments found</td>
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
@endsection

<script>
    function confirmRefund(paymentId) {
        AlertModal.showConfirm(
            'Are you sure you want to refund this payment?',
            function () {
                document.getElementById('refundForm' + paymentId).submit();
            },
            null,
            'Refund Confirmation'
        );
    }
</script>