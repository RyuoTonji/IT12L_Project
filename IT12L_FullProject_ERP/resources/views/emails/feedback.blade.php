<x-mail::message>
# New Customer Communication

You have received a new contact from the website.

**Kind of Email:** {{ ucfirst($data['feedback_type']) }}  
**Customer Name:** {{ $data['customer_name'] }}  
**Customer Type:** {{ $data['customer_type'] }}  
**Customer Email:** {{ $data['customer_email'] }}

**Message:**  
{{ $data['message'] }}

<x-mail::button :url="config('app.url') . '/admin'">
Go to Admin Panel
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
