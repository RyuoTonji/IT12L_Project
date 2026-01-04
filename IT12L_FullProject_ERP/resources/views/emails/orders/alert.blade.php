<x-mail::message>
# New Order Received!

A new order has been placed and confirmed via PayMongo.

**Order ID:** #{{ $order->id }}  
**Customer:** {{ $order->customer_name ?? 'Guest' }}  
**Total Amount:** ₱{{ number_format((float)$order->total_amount, 2) }}  
**Payment Method:** {{ ucfirst($order->payment_method) }}  

<x-mail::button :url="config('app.url') . '/admin/orders/' . $order->id">
View Order Details
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
