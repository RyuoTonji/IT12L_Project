@extends('layouts.' . (Auth::user()->role === 'manager' ? 'manager' : 'admin'))

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Void/Refund Requests
                </h1>

                </h1>

                <!-- Search Form -->
                <form action="" method="GET" class="w-80 mx-4">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}"
                            class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Search Order ID, Branch, or Customer">
                    </div>
                </form>

                <a href="{{ route(Auth::user()->role === 'manager' ? 'manager.void-requests.export-pdf' : 'admin.void-requests.export-pdf') }}"
                    class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zm2-10l4 4m-4 0l4-4m-4 4V7" />
                    </svg>
                    Export PDF
                </a>
            </div>

            <div x-data="{ activeTab: '{{ request('history_page') ? 'history' : 'pending' }}' }">
                <div class="border-b border-gray-200 mb-4">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <button @click="activeTab = 'pending'"
                            :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'pending', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'pending' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Pending Requests
                            @if($voidRequests->total() > 0)
                                <span
                                    class="ml-2 inline-flex text-xs leading-5 font-semibold text-red-600">{{ $voidRequests->total() }}</span>
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
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requester</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Reason</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requested At</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($voidRequests as $request)
                                        <tr onclick="openOrderDetails({{ $request->order_id }})" class="hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-blue-600 font-medium hover:text-blue-900">#{{ $request->order_id }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->requester->name }}</td>
                                            <td class="px-6 py-4">
                                                @if($request->reason)
                                                    <div class="text-sm text-gray-700 mb-1">{{ $request->reason }}</div>
                                                @endif
                                                @if($request->reason_tags && count($request->reason_tags) > 0)
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($request->reason_tags as $tag)
                                                            <span
                                                                class="text-xs font-medium text-blue-600">{{ str_replace('_', ' ', $tag) }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex space-x-2">
                                                    <form id="approveForm{{ $request->id }}"
                                                        action="{{ route(Auth::user()->role === 'manager' ? 'manager.void-requests.approve' : 'admin.void-requests.approve', $request) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="button"
                                                            onclick="event.stopPropagation(); showConfirm('Are you sure you want to approve this void/refund request? The order will be cancelled.', function() { document.getElementById('approveForm{{ $request->id }}').submit(); })"
                                                            class="text-green-600 hover:text-green-900 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Approve
                                                        </button>
                                                    </form>
                                                    <form id="rejectForm{{ $request->id }}"
                                                        action="{{ route(Auth::user()->role === 'manager' ? 'manager.void-requests.reject' : 'admin.void-requests.reject', $request) }}"
                                                        method="POST">
                                                        @csrf
                                                        <button type="button"
                                                            onclick="event.stopPropagation(); showConfirm('Are you sure you want to reject this void/refund request?', function() { document.getElementById('rejectForm{{ $request->id }}').submit(); })"
                                                            class="text-red-600 hover:text-red-900 flex items-center">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2" d="M6 18L18 6M6 6l12 12" />
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
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Order ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Requester</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Reason</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Action By</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($voidRequestHistory as $request)
                                        <tr onclick="openOrderDetails({{ $request->order_id }})" class="hover:bg-gray-50 cursor-pointer transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="text-blue-600 font-medium hover:text-blue-900">#{{ $request->order_id }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->requester->name }}</td>
                                            <td class="px-6 py-4">
                                                @if($request->reason)
                                                    <div class="text-sm text-gray-700 mb-1">{{ $request->reason }}</div>
                                                @endif
                                                @if($request->reason_tags && count($request->reason_tags) > 0)
                                                    <div class="flex flex-wrap gap-2">
                                                        @foreach($request->reason_tags as $tag)
                                                            <span
                                                                class="text-xs font-medium text-blue-600">{{ str_replace('_', ' ', $tag) }}</span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span
                                                    class="inline-flex text-xs leading-5 font-semibold items-center {{ $request->status === 'approved' ? 'text-green-600' : 'text-red-600' }}">
                                                    {{ ucfirst($request->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                {{ $request->approver ? $request->approver->name : '-' }}</td>
                                            <td class="px-6 py-4 whitespace-nowrap">{{ $request->updated_at->format('M d, Y H:i') }}
                                            </td>
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
    </div>
    </div>

    <!-- Order Details Pre-rendered Content -->
    @foreach($voidRequests as $request)
        <div id="details-{{ $request->order_id }}" class="hidden">
            @include('admin.orders.partials.details', ['order' => $request->order])
        </div>
    @endforeach

    @foreach($voidRequestHistory as $request)
        <div id="details-{{ $request->order_id }}" class="hidden">
            @include('admin.orders.partials.details', ['order' => $request->order])
        </div>
    @endforeach

    <!-- Order Details Modal -->
    <div id="orderDetailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 text-center">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="closeOrderModal()"></div>

            <!-- Modal panel -->
            <div class="relative bg-white rounded-lg shadow-xl transform transition-all max-w-md w-full z-10 text-left">
                <!-- Close Button (Absolute Top Right) -->
                <div class="absolute top-0 right-0 pt-4 pr-4 z-20">
                    <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none" onclick="closeOrderModal()">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="bg-white px-6 py-6 rounded-lg">
                    <div id="modalContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openOrderDetails(orderId) {
            const modal = document.getElementById('orderDetailsModal');
            const content = document.getElementById('modalContent');
            const source = document.getElementById('details-' + orderId);
            
            if (source) {
                content.innerHTML = source.innerHTML;
                modal.classList.remove('hidden');
            } else {
                console.error('Order details not found for ID:', orderId);
            }
        }

        function closeOrderModal() {
            const modal = document.getElementById('orderDetailsModal');
            modal.classList.add('hidden');
        }
    </script>
@endsection