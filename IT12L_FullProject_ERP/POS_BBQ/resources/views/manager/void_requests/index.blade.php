@extends('layouts.' . (Auth::user()->role === 'manager' ? 'manager' : 'admin'))

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-semibold mb-6">Void/Refund Requests</h1>

            @if($voidRequests->isEmpty())
                <p class="text-gray-500">No pending void/refund requests.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order
                                    ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requester</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Reason</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Requested At</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($voidRequests as $request)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('orders.show', $request->order_id) }}"
                                            class="text-blue-600 hover:text-blue-900">
                                            #{{ $request->order_id }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->requester->name }}</td>
                                    <td class="px-6 py-4">
                                        @if($request->reason_tags && count($request->reason_tags) > 0)
                                            <div class="mb-1">
                                                @foreach($request->reason_tags as $tag)
                                                    <span
                                                        class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $tag }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($request->reason)
                                            <div class="text-sm text-gray-700">{{ $request->reason }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">{{ $request->created_at->format('M d, Y H:i') }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <form id="approveForm{{ $request->id }}"
                                                action="{{ route('manager.void-requests.approve', $request) }}" method="POST">
                                                @csrf
                                                <button type="button"
                                                    onclick="showConfirm('Are you sure you want to approve this void/refund request? The order will be cancelled.', function() { document.getElementById('approveForm{{ $request->id }}').submit(); })"
                                                    class="text-green-600 hover:text-green-900">Approve</button>
                                            </form>
                                            <form id="rejectForm{{ $request->id }}"
                                                action="{{ route('manager.void-requests.reject', $request) }}" method="POST">
                                                @csrf
                                                <button type="button"
                                                    onclick="showConfirm('Are you sure you want to reject this void/refund request?', function() { document.getElementById('rejectForm{{ $request->id }}').submit(); })"
                                                    class="text-red-600 hover:text-red-900">Reject</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $voidRequests->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection