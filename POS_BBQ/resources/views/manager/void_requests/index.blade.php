@extends('layouts.' . (Auth::user()->role === 'manager' ? 'manager' : 'admin'))

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Void/Refund Requests</h1>
                <a href="{{ route(Auth::user()->role === 'manager' ? 'manager.void-requests.export-pdf' : 'admin.void-requests.export-pdf') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Export PDF
                </a>
            </div>

            <div x-data="{ activeTab: 'pending' }">
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="activeTab = 'pending'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'pending', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pending' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pending Requests
                            @if($voidRequests->total() > 0)
                                <span class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium bg-red-100 text-red-800">{{ $voidRequests->total() }}</span>
                            @endif
                        </button>
                        <button @click="activeTab = 'history'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'history', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'history' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Request History
                        </button>
                    </nav>
                </div>

                <!-- Pending Tab -->
                <div x-show="activeTab === 'pending'">
                    @if($voidRequests->isEmpty())
                        <p class="text-gray-500">No pending void/refund requests.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requested At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($voidRequests as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('orders.show', $request->order_id) }}" class="text-blue-600 hover:text-blue-900">#{{ $request->order_id }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->requester->name }}</td>
                                            <td class="px-6 py-4">
                                                @if($request->reason_tags && count($request->reason_tags) > 0)
                                                    <div class="mb-1">
                                                        @foreach($request->reason_tags as $tag)
                                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $tag }}</span>
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
                                                    <form id="approveForm{{ $request->id }}" action="{{ route(Auth::user()->role === 'manager' ? 'manager.void-requests.approve' : 'admin.void-requests.approve', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="button" onclick="showConfirm('Are you sure you want to approve this void/refund request? The order will be cancelled.', function() { document.getElementById('approveForm{{ $request->id }}').submit(); })" class="text-green-600 hover:text-green-900 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form id="rejectForm{{ $request->id }}" action="{{ route(Auth::user()->role === 'manager' ? 'manager.void-requests.reject' : 'admin.void-requests.reject', $request) }}" method="POST">
                                                        @csrf
                                                        <button type="button" onclick="showConfirm('Are you sure you want to reject this void/refund request?', function() { document.getElementById('rejectForm{{ $request->id }}').submit(); })" class="text-red-600 hover:text-red-900 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Reject
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $voidRequests->appends(['history_page' => $voidRequestHistory->currentPage()])->links() }}
                        </div>
                    @endif
                </div>

                <!-- History Tab -->
                <div x-show="activeTab === 'history'" style="display: none;">
                    @if($voidRequestHistory->isEmpty())
                        <p class="text-gray-500">No history of void/refund requests.</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Requester</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action By</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($voidRequestHistory as $request)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <a href="{{ route('orders.show', $request->order_id) }}" class="text-blue-600 hover:text-blue-900">#{{ $request->order_id }}</a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->requester->name }}</td>
                                            <td class="px-6 py-4">
                                                @if($request->reason_tags && count($request->reason_tags) > 0)
                                                    <div class="mb-1">
                                                        @foreach($request->reason_tags as $tag)
                                                            <span class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded mr-1 mb-1">{{ $tag }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                                @if($request->reason)
                                                    <div class="text-sm text-gray-700">{{ $request->reason }}</div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $request->status === 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->approver ? $request->approver->name : '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->updated_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-4">
                            {{ $voidRequestHistory->appends(['pending_page' => $voidRequests->currentPage()])->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection