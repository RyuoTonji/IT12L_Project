@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-800">{{ $branch->name }} Operations</h1>
                    <p class="text-sm text-gray-600">{{ $branch->address }} | {{ $branch->phone }}</p>
                </div>
                <div>
                    <a href="{{ route('admin.branches.index') }}"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>

            <!-- Branch Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
                    <p class="text-sm text-blue-600 font-medium">Today's Sales</p>
                    <p class="text-2xl font-bold text-blue-900">₱{{ number_format($todaySales, 2) }}</p>
                </div>
                <div class="bg-green-50 p-4 rounded-lg border border-green-100">
                    <p class="text-sm text-green-600 font-medium">Today's Orders</p>
                    <p class="text-2xl font-bold text-green-900">{{ $todayOrders }}</p>
                </div>
                <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-100">
                    <p class="text-sm text-yellow-600 font-medium">Active Orders</p>
                    <p class="text-2xl font-bold text-yellow-900">{{ $activeOrders->count() }}</p>
                </div>
                <div class="bg-purple-50 p-4 rounded-lg border border-purple-100">
                    <p class="text-sm text-purple-600 font-medium">Available Menu Items</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $menuItems->flatten()->count() }}</p>
                </div>
            </div>

            <!-- Tabs -->
            <div x-data="{ activeTab: 'orders' }">
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8">
                        <button @click="activeTab = 'orders'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'orders', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'orders' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Active Orders
                        </button>
                        <button @click="activeTab = 'kitchen'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'kitchen', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'kitchen' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Kitchen Display
                        </button>
                        <button @click="activeTab = 'menu'"
                            :class="{ 'border-blue-500 text-blue-600': activeTab === 'menu', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'menu' }"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                            Menu Availability
                        </button>
                    </nav>
                </div>

                <!-- Active Orders Tab -->
                <div x-show="activeTab === 'orders'" class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900">Active Orders</h3>
                    @if($activeOrders->count() > 0)
                        <div id="active-orders-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($activeOrders as $order)
                                <div class="bg-white border rounded-lg shadow-sm p-4 cursor-pointer hover:shadow-md transition-shadow active-order-card"
                                    onclick="openOrderModal({{ $order->id }})">
                                    <div class="flex justify-between items-start mb-2">
                                        <span class="font-bold text-lg">Order #{{ $order->id }}</span>
                                        <span
                                            class="inline-flex text-xs leading-5 font-semibold items-center 
                                                                                                                                                                                                            {{ $order->status === 'pending' ? 'text-yellow-600' : 'text-blue-600' }}">
                                            {{ ucfirst($order->status) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-2">Table: {{ $order->table->name ?? 'N/A' }}</p>
                                    <div class="border-t border-b py-2 my-2 text-sm">
                                        @foreach($order->orderItems as $item)
                                            <div class="flex justify-between">
                                                <span>{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                                                <span>₱{{ number_format($item->price * $item->quantity, 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="flex justify-between items-center font-bold mt-2">
                                        <span>Total:</span>
                                        <span>₱{{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!-- Pagination Controls -->
                        <div id="pagination-active-orders" class="mt-4 flex justify-end items-center space-x-2"></div>
                    @else
                        <p class="text-gray-500 italic">No active orders at the moment.</p>
                    @endif
                </div>

                <!-- Kitchen Tab -->
                <div x-show="activeTab === 'kitchen'" style="display: none;">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Kitchen Display System</h3>
                    <div id="kitchen-items-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($activeOrders as $order)
                            @if($order->status !== 'ready')
                                <div class="bg-white border-2 border-gray-200 rounded-lg p-4 kitchen-order-card">
                                    <div class="flex justify-between items-center mb-3 pb-2 border-b">
                                        <span class="font-bold text-xl">#{{ $order->id }}</span>
                                        <span class="text-sm text-gray-500">{{ $order->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($order->orderItems as $item)
                                            <div class="flex justify-between items-center">
                                                <span class="font-medium text-lg">{{ $item->quantity }}x
                                                    {{ $item->menuItem->name }}</span>
                                            </div>
                                            @if($item->notes)
                                                <p class="text-sm text-red-500 italic ml-4">Note: {{ $item->notes }}</p>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    <!-- Pagination Controls -->
                    <div id="pagination-kitchen-items" class="mt-4 flex justify-end items-center space-x-2"></div>
                </div>

                <!-- Menu Tab -->
                <div x-show="activeTab === 'menu'" style="display: none;">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Menu Availability</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @foreach($menuItems as $category => $items)
                            <div class="bg-white border rounded-lg p-4">
                                <h4 class="font-semibold text-gray-800 mb-3 border-b pb-2">{{ $category }}</h4>
                                <div class="space-y-2">
                                    @foreach($items as $item)
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm text-gray-700">{{ $item->name }}</span>
                                            <span
                                                class="inline-flex text-xs leading-5 font-semibold text-green-600">Available</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modals -->
    @foreach($activeOrders as $order)
        <div id="order-modal-{{ $order->id }}" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Dark overlay background -->
                <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    onclick="closeOrderModal({{ $order->id }})"></div>

                <div class="relative bg-white rounded-lg shadow-xl transform transition-all max-w-md w-full z-10">
                    <div class="absolute top-0 right-0 pt-4 pr-4 z-20">
                        <button type="button" class="text-gray-400 hover:text-gray-500 focus:outline-none"
                            onclick="closeOrderModal({{ $order->id }})">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="bg-white px-6 py-6 rounded-lg">
                        @include('admin.orders.partials.details', ['order' => $order])
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <script>
        function openOrderModal(orderId) {
            document.getElementById(`order-modal-${orderId}`).classList.remove('hidden');
        }

        function closeOrderModal(orderId) {
            document.getElementById(`order-modal-${orderId}`).classList.add('hidden');
        }

        // Pagination Logic
        document.addEventListener('DOMContentLoaded', function () {
            // Function to initialize pagination
            function initPagination(containerId, itemClass, paginationId) {
                const container = document.getElementById(containerId);
                const paginationDiv = document.getElementById(paginationId);

                if (!container || !paginationDiv) return;

                const items = Array.from(container.getElementsByClassName(itemClass));
                const itemsPerPage = 9;
                const pageCount = Math.ceil(items.length / itemsPerPage);

                if (items.length <= itemsPerPage) {
                    paginationDiv.classList.add('hidden');
                    return;
                }

                // Render function
                window['render_' + containerId] = function (page) {
                    // Show/Hide Items
                    items.forEach((item, i) => {
                        item.style.display = (i >= (page - 1) * itemsPerPage && i < page * itemsPerPage) ? '' : 'none';
                    });

                    // Render Controls
                    const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>`;
                    const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>`;

                    let html = '';

                    // Previous Button
                    html += `<button onclick="window['render_' + '${containerId}'](${page - 1})" 
                                            class="w-8 h-8 flex items-center justify-center mr-2 rounded hover:bg-gray-100 ${page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-black'}" 
                                            ${page === 1 ? 'disabled' : ''}>
                                            ${prevIcon}
                                         </button>`;

                    // Generate Page Numbers
                    let range = [];
                    if (pageCount <= 7) {
                        for (let i = 1; i <= pageCount; i++) range.push(i);
                    } else {
                        if (page <= 4) {
                            range = [1, 2, 3, 4, 5, '...', pageCount];
                        } else if (page >= pageCount - 3) {
                            range = [1, '...', pageCount - 4, pageCount - 3, pageCount - 2, pageCount - 1, pageCount];
                        } else {
                            range = [1, '...', page - 1, page, page + 1, '...', pageCount];
                        }
                    }

                    range.forEach(item => {
                        if (item === '...') {
                            html += `<span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>`;
                        } else {
                            const isActive = item === page;
                            const classes = isActive
                                ? 'bg-gray-800 text-white font-bold'
                                : 'text-black hover:bg-gray-100';
                            html += `<button onclick="window['render_' + '${containerId}'](${item})" 
                                                    class="w-8 h-8 flex items-center justify-center rounded mx-1 ${classes}">
                                                    ${item}
                                                 </button>`;
                        }
                    });

                    // Next Button
                    html += `<button onclick="window['render_' + '${containerId}'](${page + 1})" 
                                            class="w-8 h-8 flex items-center justify-center ml-2 rounded hover:bg-gray-100 ${page === pageCount ? 'text-gray-300 cursor-not-allowed' : 'text-black'}" 
                                            ${page === pageCount ? 'disabled' : ''}>
                                            ${nextIcon}
                                         </button>`;

                    paginationDiv.innerHTML = html;
                };

                // Initial Render
                window['render_' + containerId](1);
            }

            // Initialize for Active Orders
            initPagination('active-orders-container', 'active-order-card', 'pagination-active-orders');

            // Initialize for Kitchen Display
            initPagination('kitchen-items-container', 'kitchen-order-card', 'pagination-kitchen-items');
        });
    </script>
@endsection