@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <h1 class="text-2xl font-semibold mb-6">Cashier Dashboard</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
                <!-- Today's Sales Card -->
                <div class="bg-blue-100 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-medium text-blue-800">Your Sales Today</h2>
                    <p class="text-2xl font-bold">₱{{ number_format($todaySales, 2) }}</p>
                </div>

                <!-- Completed Orders Card -->
                <div class="bg-green-100 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-medium text-green-800">Orders Completed Today</h2>
                    <p class="text-2xl font-bold">{{ $completedOrders }}</p>
                </div>

                <!-- Active Orders Card -->
                <div class="bg-yellow-100 p-4 rounded-lg shadow">
                    <h2 class="text-lg font-medium text-yellow-800">Active Orders</h2>
                    <p class="text-2xl font-bold">{{ $activeOrders->count() }}</p>
                </div>
            </div>

            <!-- Table Status Grid -->
            <div class="mb-8">
                <h2 class="text-xl font-medium mb-4">Table Status</h2>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                    @foreach($tables as $table)
                                <a href="{{ route('tables.show', $table) }}" class="block">
                                    <div
                                        class="aspect-square flex flex-col items-center justify-center rounded-lg shadow border
                                                            {{ $table->status == 'available' ? 'bg-green-100 border-green-300' :
                        ($table->status == 'occupied' ? 'bg-red-100 border-red-300' : 'bg-yellow-100 border-yellow-300') }}">
                                        <span class="text-lg font-bold">{{ $table->name }}</span>
                                        <span class="text-sm">{{ ucfirst($table->status) }}</span>
                                        <span class="text-xs mt-1">Capacity: {{ $table->capacity }}</span>
                                    </div>
                                </a>
                    @endforeach
                </div>
            </div>

            <!-- Active Orders -->
            <div class="bg-white p-4 rounded-lg shadow border">
                <h2 class="text-xl font-medium mb-4">Active Orders</h2>
                <div class="overflow-x-auto">
                    <table class="min-w-full" id="cashier-active-orders-table">
                        <thead>
                            <tr>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Order #</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Table</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($activeOrders as $order)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="py-2 px-4 border-b cursor-pointer" onclick="openOrderModal({{ $order->id }})">{{ $order->id }}</td>
                                                    <td class="py-2 px-4 border-b cursor-pointer" onclick="openOrderModal({{ $order->id }})">{{ $order->table ? $order->table->name : 'Takeout' }}</td>
                                                    <td class="py-2 px-4 border-b cursor-pointer" onclick="openOrderModal({{ $order->id }})">
                                                        <span class="inline-flex text-xs leading-5 font-semibold items-center
                                                                                        {{ $order->status == 'new' ? 'text-blue-600' :
                                ($order->status == 'preparing' ? 'text-yellow-600' : 'text-green-600') }}">
                                                            @if($order->status == 'new')
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                                </svg>
                                                            @elseif($order->status == 'preparing')
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                            @else
                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                                </svg>
                                                            @endif
                                                            {{ ucfirst($order->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="py-2 px-4 border-b text-right cursor-pointer" onclick="openOrderModal({{ $order->id }})">₱{{ number_format($order->total_amount, 2) }}</td>
                                                    <td class="py-2 px-4 border-b text-center">
                                                        <a href="{{ route('orders.show', $order) }}"
                                                            class="text-blue-600 hover:text-blue-900 mr-2 flex items-center inline-flex">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                            </svg>
                                                            View
                                                        </a>
                                                        <a href="{{ route('orders.edit', $order) }}"
                                                            class="text-green-600 hover:text-green-900 mr-2 flex items-center inline-flex">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                            </svg>
                                                            Edit
                                                        </a>
                                                        <a href="{{ route('payments.create', ['order' => $order->id]) }}"
                                                            class="text-purple-600 hover:text-purple-900 flex items-center inline-flex">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                                viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                            </svg>
                                                            Payment
                                                        </a>
                                                    </td>
                                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-2 px-4 text-center text-gray-500">No active orders</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="pagination-cashier-active" class="p-4 flex justify-end items-center space-x-2"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Details Modals -->
    <!-- Order Details Modal (Singleton) -->
    <div id="order-details-modal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity" aria-hidden="true" onclick="closeOrderModal()"></div>

            <!-- Modal panel -->
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full">
                
                <!-- Close button -->
                <div class="absolute top-0 right-0 pt-4 pr-4 z-10">
                    <button type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" onclick="closeOrderModal()">
                        <span class="sr-only">Close</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div id="order-modal-content">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Order Modal Functions
        function openOrderModal(orderId) {
            const modal = document.getElementById('order-details-modal');
            const content = document.getElementById('order-modal-content');

            // Show modal
            modal.classList.remove('hidden');

            // Show loading state
            content.innerHTML = `
                <div class="flex justify-center items-center py-12">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
            `;

            // Fetch order details
            fetch(`/cashier/orders/${orderId}/details`)
                .then(response => response.text())
                .then(html => {
                    content.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error fetching order details:', error);
                    content.innerHTML = `
                        <div class="text-center py-8 text-red-600">
                            <p>Failed to load order details. Please try again.</p>
                        </div>
                    `;
                });
        }

        // Pagination Logic
        document.addEventListener('DOMContentLoaded', function() {
            paginateTable('cashier-active-orders-table', 10);
        });

        function paginateTable(tableId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            const paginationDiv = document.getElementById('pagination-cashier-active');
            if (!paginationDiv) return;

            if (rowCount <= rowsPerPage) {
                paginationDiv.classList.add('hidden');
                rows.forEach(row => row.style.display = '');
                return;
            }

            paginationDiv.classList.remove('hidden');

            const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>`;
            const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>`;

            const render = (page) => {
                rows.forEach((row, i) => row.style.display = (i >= (page - 1) * rowsPerPage && i < page * rowsPerPage) ? '' : 'none');
                paginationDiv.innerHTML = `
                        <button onclick="changePage('${tableId}', -1, ${rowsPerPage}, ${pageCount})" class="w-8 h-8 flex items-center justify-center rounded border transition-colors duration-200 ${page === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-blue-600 hover:bg-blue-50 border-blue-200'}" ${page === 1 ? 'disabled' : ''}>${prevIcon}</button>
                        <span class="text-sm text-gray-500 mx-4">Page ${page} of ${pageCount}</span>
                        <button onclick="changePage('${tableId}', 1, ${rowsPerPage}, ${pageCount})" class="w-8 h-8 flex items-center justify-center rounded border transition-colors duration-200 ${page === pageCount ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-blue-600 hover:bg-blue-50 border-blue-200'}" ${page === pageCount ? 'disabled' : ''}>${nextIcon}</button>
                    `;
                table.dataset.currentPage = page;
            };

            window.changePage = (tId, dir, rPP, pCount) => {
                const t = document.getElementById(tId);
                if(!t) return;
                let cur = parseInt(t.dataset.currentPage || 1);
                let newPage = cur + dir;
                
                if (newPage >= 1 && newPage <= pCount) {
                    const tb = t.querySelector('tbody');
                    const rs = Array.from(tb.querySelectorAll('tr'));
                    rs.forEach((row, i) => row.style.display = (i >= (newPage - 1) * rPP && i < newPage * rPP) ? '' : 'none');
                    
                    const pDiv = document.getElementById('pagination-cashier-active');
                    pDiv.innerHTML = `
                        <button onclick="changePage('${tId}', -1, ${rPP}, ${pCount})" class="w-8 h-8 flex items-center justify-center rounded border transition-colors duration-200 ${newPage === 1 ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-blue-600 hover:bg-blue-50 border-blue-200'}" ${newPage === 1 ? 'disabled' : ''}>${prevIcon}</button>
                        <span class="text-sm text-gray-500 mx-4">Page ${newPage} of ${pCount}</span>
                        <button onclick="changePage('${tId}', 1, ${rPP}, ${pCount})" class="w-8 h-8 flex items-center justify-center rounded border transition-colors duration-200 ${newPage === pCount ? 'bg-gray-100 text-gray-400 cursor-not-allowed' : 'bg-white text-blue-600 hover:bg-blue-50 border-blue-200'}" ${newPage === pCount ? 'disabled' : ''}>${nextIcon}</button>
                     `;
                    t.dataset.currentPage = newPage;
                }
            };

            render(1);
        }

        function closeOrderModal() {
            const modal = document.getElementById('order-details-modal');
            modal.classList.add('hidden');
        }
    </script>
@endsection