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
                <span class="inline-flex text-xs leading-5 font-semibold items-center
                    {{ $order->status == 'completed' ? 'text-green-600' :
    ($order->status == 'cancelled' ? 'text-red-600' : 'text-yellow-600') }}">
                    @if($order->status == 'completed')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    @elseif($order->status == 'cancelled')
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
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
                        <span class="font-medium">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                        @if($item->notes)
                            <p class="text-xs text-gray-500 italic ml-4">Note: {{ $item->notes }}</p>
                        @endif
                    </div>
                    <span class="font-medium">₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Order Total -->
    <div class="border-t pt-3">
        <div class="flex justify-between items-center">
            <span class="text-lg font-semibold">Total Amount:</span>
            <span class="text-xl font-bold">₱{{ number_format($order->total_amount, 2) }}</span>
        </div>
    </div>

    <!-- Payment Information if available -->
    @if($order->payments && $order->payments->isNotEmpty())
        <div class="border-t pt-3">
            <h3 class="font-semibold mb-2">Payment Details</h3>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                    <span class="text-gray-600">Method:</span>
                    <span class="font-medium">{{ ucfirst($order->payments->first()->payment_method) }}</span>
                </div>
                <div>
                    <span class="text-gray-600">Status:</span>
                    <span class="inline-flex items-center text-xs leading-5 font-semibold
                                            {{ $order->payment_status == 'paid' ? 'text-green-600' :
            ($order->payment_status == 'refunded' ? 'text-red-600' : 'text-yellow-600') }}">
                        @if($order->payment_status == 'paid')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @elseif($order->payment_status == 'refunded')
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                            </svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>
            </div>
        </div>
    @endif

    <!-- Timestamps -->
    <div class="border-t pt-3 text-xs text-gray-500">
        <div>Created: {{ $order->created_at->format('M d, Y h:i A') }}</div>
        @if($order->updated_at != $order->created_at)
            <div>Updated: {{ $order->updated_at->format('M d, Y h:i A') }}</div>
        @endif
    </div>
</div>