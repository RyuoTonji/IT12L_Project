@extends('layouts.inventory')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data="{ activeTab: 0 }">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Inventory Management
                </h3>
                <a href="{{ route('inventory.report') }}"
                    class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 mr-2 flex items-center inline-flex transition-colors duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Daily Report
                </a>
                <button onclick="document.getElementById('addStockModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add New Ingredient
                </button>
            </div>

            <!-- Tabs Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8 overflow-x-auto custom-scrollbar" aria-label="Tabs">
                    @foreach($inventoriesByCategory as $categoryName => $items)
                        <button @click="activeTab = {{ $loop->index }}"
                            :class="activeTab === {{ $loop->index }} ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center transition-colors duration-200 outline-none focus:outline-none">
                            {{ $categoryName ?: 'Uncategorized' }}
                            <span
                                :class="activeTab === {{ $loop->index }} ? 'bg-blue-100 text-blue-600' : 'bg-gray-100 text-gray-900'"
                                class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium inline-block transition-colors duration-200">
                                {{ $items->count() }}
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Category-Grouped Tables -->
            @forelse($inventoriesByCategory as $categoryName => $items)
                <div x-show="activeTab === {{ $loop->index }}" class="mb-8 category-section"
                    style="{{ $loop->first ? '' : 'display: none;' }}">
                    <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                        <table class="min-w-full divide-y divide-gray-200" id="table-cat-{{ $loop->index }}">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col"
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Ingredient Name</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Sold</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unsold</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Spoilage</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock-In</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Stock-Out</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Remaining</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Unit</th>
                                    <th scope="col"
                                        class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($items as $inventory)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">{{ $inventory->name }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                            {{ number_format($inventory->sold, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                            {{ number_format($inventory->quantity - $inventory->sold, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-red-600">
                                            {{ number_format($inventory->spoilage, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-green-600">
                                            {{ number_format($inventory->stock_in, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-orange-600">
                                            {{ number_format($inventory->stock_out, 2) }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold {{ $inventory->quantity <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ number_format($inventory->quantity, 2) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                            {{ $inventory->unit }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                            <button onclick="openUpdateModal('{{ $inventory->id }}', '{{ $inventory->name }}')"
                                                class="text-indigo-600 hover:text-indigo-900 mr-3">Update Stock</button>
                                            <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST"
                                                class="inline-block" onsubmit="return false;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    onclick="showConfirm('Are you sure you want to archive this item?', () => this.closest('form').submit())"
                                                    class="text-gray-600 hover:text-gray-900">Archive</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div id="pagination-cat-{{ $loop->index }}" class="p-4 bg-gray-50 border-t border-gray-200"></div>
                </div>
            @empty
                <div class="text-center py-8 text-gray-500">
                    <p class="text-lg font-medium">No ingredients found</p>
                    <p class="text-sm">Click "Add New Ingredient" to get started.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Add Stock Modal -->
    <div id="addStockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Add New Ingredient</h3>
                <button onclick="document.getElementById('addStockModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('inventory.add') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Ingredient Name</label>
                    <input type="text" name="name" id="name" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Initial Quantity</label>
                    <input type="number" name="quantity" id="quantity" step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="mb-4">
                    <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                    <input type="text" name="unit" id="unit" placeholder="kg, pcs, liters, etc." required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addStockModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Ingredient
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Update Stock Modal -->
    <div id="updateStockModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold">Update Stock: <span id="updateItemName"></span></h3>
                <button onclick="document.getElementById('updateStockModal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="updateStockForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="mb-4">
                    <label for="update_quantity" class="block text-sm font-medium text-gray-700 mb-1">Add Quantity</label>
                    <input type="number" name="quantity" id="update_quantity" step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('updateStockModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Stock
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(id, name) {
            document.getElementById('updateItemName').textContent = name;
            document.getElementById('updateStockForm').action = `/inventory/${id}`;
            document.getElementById('updateStockModal').classList.remove('hidden');
        }

        // Pagination Logic
        document.addEventListener('DOMContentLoaded', function () {
            @foreach($inventoriesByCategory as $categoryName => $items)
                paginateTable('table-cat-{{ $loop->index }}', 10);
            @endforeach
                    });

        function paginateTable(tableId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            let paginationDivId = 'pagination-' + tableId.replace('table-', '');
            let paginationDiv = document.getElementById(paginationDivId);

            if (rowCount <= rowsPerPage) {
                if (paginationDiv) paginationDiv.style.display = 'none';
                rows.forEach(row => row.style.display = '');
                return;
            }

            if (paginationDiv) paginationDiv.style.display = 'flex';

            window.renderPagination = function (tId, page, pCount, rPP) {
                const t = document.getElementById(tId);
                const tb = t.querySelector('tbody');
                const rs = Array.from(tb.querySelectorAll('tr'));

                // Show/Hide rows
                rs.forEach((row, i) => row.style.display = (i >= (page - 1) * rPP && i < page * rPP) ? '' : 'none');

                // Find Pagination Div
                let pDivId = 'pagination-' + tId.replace('table-', '');
                let pDiv = document.getElementById(pDivId);
                if (!pDiv) return;

                // Icons
                const prevIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" /></svg>`;
                const nextIcon = `<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" /></svg>`;

                let html = '';

                // Previous Button
                html += `<button onclick="renderPagination('${tId}', ${page - 1}, ${pCount}, ${rPP})" 
                                                class="w-8 h-8 flex items-center justify-center mr-2 rounded hover:bg-gray-100 ${page === 1 ? 'text-gray-300 cursor-not-allowed' : 'text-black'}" 
                                                ${page === 1 ? 'disabled' : ''}>
                                                ${prevIcon}
                                             </button>`;

                // Generate Page Numbers
                let range = [];
                if (pCount <= 7) {
                    for (let i = 1; i <= pCount; i++) range.push(i);
                } else {
                    if (page <= 4) {
                        range = [1, 2, 3, 4, 5, '...', pCount];
                    } else if (page >= pCount - 3) {
                        range = [1, '...', pCount - 4, pCount - 3, pCount - 2, pCount - 1, pCount];
                    } else {
                        range = [1, '...', page - 1, page, page + 1, '...', pCount];
                    }
                }

                range.forEach(item => {
                    if (item === '...') {
                        html += `<span class="w-8 h-8 flex items-center justify-center text-gray-500">...</span>`;
                    } else {
                        const isActive = item === page;
                        const classes = isActive
                            ? 'bg-blue-600 text-white font-bold'
                            : 'text-black hover:bg-gray-100';
                        html += `<button onclick="renderPagination('${tId}', ${item}, ${pCount}, ${rPP})" 
                                                        class="w-8 h-8 flex items-center justify-center rounded mx-1 ${classes}">
                                                        ${item}
                                                     </button>`;
                    }
                });

                // Next Button
                html += `<button onclick="renderPagination('${tId}', ${page + 1}, ${pCount}, ${rPP})" 
                                                class="w-8 h-8 flex items-center justify-center ml-2 rounded hover:bg-gray-100 ${page === pCount ? 'text-gray-300 cursor-not-allowed' : 'text-black'}" 
                                                ${page === pCount ? 'disabled' : ''}>
                                                ${nextIcon}
                                             </button>`;

                // Add flex styling
                html = '<div class="flex justify-end items-center space-x-2 w-full">' + html + '</div>';
                pDiv.innerHTML = html;
            };

            // Initial render
            window.renderPagination(tableId, 1, pageCount, rowsPerPage);
        }
    </script>
@endsection