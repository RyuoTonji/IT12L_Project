@extends('layouts.admin')

@section('content')
    <!-- Alpine.js is already included in layout via Vite -->

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
                <div>
                    <a href="{{ route('admin.inventory.report') }}"
                        class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700 mr-2 flex items-center inline-flex transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Daily Report
                    </a>
                    <button onclick="showExportConfirmation()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2 flex items-center inline-flex transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zm2-10l4 4m-4 0l4-4m-4 4V7" />
                        </svg>
                        Export PDF
                    </button>
                    <a href="{{ route('inventory.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex transition-colors duration-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </a>
                </div>
            </div>

            <!-- Filter Form (Date Only) -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form id="filterForm" action="{{ route('inventory.index') }}" method="GET"
                    class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">Date Added</label>
                        <input type="date" name="date" id="date" value="{{ request('date') }}"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="document.getElementById('filterForm').submit()">
                    </div>
                    <div class="flex items-center pb-1">
                        @if(request()->filled('date'))
                            <a href="{{ route('inventory.index') }}"
                                class="text-gray-600 hover:text-gray-900 flex items-center inline-flex ml-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear Filter
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- Category Tabs & Tables -->
            @if(!request('date'))
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="-mb-px flex space-x-8 overflow-x-auto custom-scrollbar" aria-label="Tabs">
                        @foreach($inventoryByCategory as $categoryName => $items)
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

                <!-- Tab Contents (Tables) -->
                @forelse($inventoryByCategory as $categoryName => $items)
                    <div x-show="activeTab === {{ $loop->index }}" class="category-section" style="display: none;">
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full bg-white divide-y divide-gray-200" id="table-cat-{{ $loop->index }}">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Supplier</th>
                                        <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Quantity</th>
                                        <th
                                            class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Unit</th>
                                        <th
                                            class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                        <th
                                            class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        <tr class="hover:bg-blue-50 transition-colors cursor-pointer"
                                            onclick="window.location='{{ route('inventory.show', $item) }}'">
                                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                            <td class="py-4 px-6 text-sm text-gray-500">{{ $item->supplier ?: '-' }}</td>
                                            <td class="py-4 px-6 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                                            <td class="py-4 px-6 text-sm text-center text-gray-500">{{ $item->unit }}</td>
                                            <td class="py-4 px-6 text-center">
                                                <span
                                                    class="inline-flex items-center text-xs font-medium {{ $item->quantity <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                                    @if($item->quantity <= 0)
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        Out of Stock
                                                    @else
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        In Stock
                                                    @endif
                                                </span>
                                            </td>
                                            <td class="py-4 px-6 text-center text-sm" onclick="event.stopPropagation();">
                                                <div class="flex justify-center space-x-2">
                                                    <a href="{{ route('inventory.show', $item) }}"
                                                        class="text-blue-600 hover:text-blue-900 inline-flex items-center transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                        </svg>
                                                        View
                                                    </a>
                                                    <button type="button" onclick="confirmArchive({{ $item->id }})"
                                                        class="text-gray-500 hover:text-red-600 inline-flex items-center transition-colors ml-2">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                            </path>
                                                        </svg>
                                                        Archive
                                                    </button>
                                                    <form id="deleteInventoryForm{{ $item->id }}"
                                                        action="{{ route('inventory.destroy', $item) }}" method="POST" class="hidden">
                                                        @csrf @method('DELETE')
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div id="pagination-cat-{{ $loop->index }}" class="p-4 bg-gray-50 border-t border-gray-200"></div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                            </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No Inventory Items</h3>
                        <p class="mt-1 text-sm text-gray-500">Get started by creating a new inventory item.</p>
                        <div class="mt-6">
                            <a href="{{ route('inventory.create') }}"
                                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                    fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Item
                            </a>
                        </div>
                    </div>
                @endforelse

            @else
                <!-- Single List (Filtered) -->
                <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                    <table class="min-w-full bg-white divide-y divide-gray-200" id="table-filtered-result">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name
                                </th>
                                <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Supplier</th>
                                <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Quantity</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Unit</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($inventoryItems as $item)
                                <tr class="hover:bg-blue-50 transition-colors cursor-pointer"
                                    onclick="window.location='{{ route('inventory.show', $item) }}'">
                                    <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                    <td class="py-4 px-6 text-sm text-gray-500">{{ $item->supplier ?: '-' }}</td>
                                    <td class="py-4 px-6 text-sm text-right text-gray-900">{{ $item->quantity }}</td>
                                    <td class="py-4 px-6 text-sm text-center text-gray-500">{{ $item->unit }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span
                                            class="inline-flex items-center text-xs font-medium {{ $item->quantity <= 0 ? 'text-red-600' : 'text-green-600' }}">
                                            @if($item->quantity <= 0)
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Out of Stock
                                            @else
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                In Stock
                                            @endif
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center text-sm" onclick="event.stopPropagation();">
                                        <div class="flex justify-center space-x-2">
                                            <a href="{{ route('inventory.show', $item) }}"
                                                class="text-blue-600 hover:text-blue-900 inline-flex items-center transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                View
                                            </a>
                                            <button type="button" onclick="confirmArchive({{ $item->id }})"
                                                class="text-gray-500 hover:text-red-600 inline-flex items-center transition-colors ml-2">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                    </path>
                                                </svg>
                                                Archive
                                            </button>
                                            <form id="deleteInventoryForm{{ $item->id }}"
                                                action="{{ route('inventory.destroy', $item) }}" method="POST" class="hidden">
                                                @csrf @method('DELETE')
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-8 text-center text-gray-500">No inventory items found matching your
                                        filter.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div id="pagination-filtered-result" class="p-4 bg-gray-50 border-t border-gray-200"></div>
                </div>
            @endif
        </div>
    </div>

    <script>
        function showExportConfirmation() {
            AlertModal.showConfirm(
                'Are you sure you want to export the inventory report as PDF?',
                function () {
                    window.location.href = '{{ route('export.inventory') }}';
                },
                null,
                'Export Confirmation'
            );
        }

        function confirmArchive(itemId) {
            AlertModal.showConfirm(
                'Are you sure you want to archive this item?',
                function () {
                    document.getElementById('deleteInventoryForm' + itemId).submit();
                },
                null,
                'Archive Confirmation'
            );
        }

        // Pagination Logic
        document.addEventListener('DOMContentLoaded', function () {
            @if(!request('date'))
                @foreach($inventoryByCategory as $categoryName => $items)
                    paginateTable('table-cat-{{ $loop->index }}', 10);
                @endforeach
            @else
                paginateTable('table-filtered-result', 10);
            @endif
                    });

        function paginateTable(tableId, rowsPerPage) {
            const table = document.getElementById(tableId);
            if (!table) return;
            const tbody = table.querySelector('tbody');
            if (!tbody) return;
            const rows = Array.from(tbody.querySelectorAll('tr'));
            const rowCount = rows.length;
            const pageCount = Math.ceil(rowCount / rowsPerPage);

            let paginationDivId;
            if (tableId === 'table-filtered-result') {
                paginationDivId = 'pagination-filtered-result';
            } else {
                paginationDivId = 'pagination-' + tableId.replace('table-', '');
            }

            let paginationDiv = document.getElementById(paginationDivId);
            if (!paginationDiv) {
                paginationDiv = document.createElement('div');
                paginationDiv.id = paginationDivId;
                paginationDiv.className = 'mt-4 flex justify-end items-center space-x-2';
                table.parentElement.appendChild(paginationDiv);
            }

            if (rowCount <= rowsPerPage) {
                paginationDiv.classList.add('hidden');
                rows.forEach(row => row.style.display = '');
                return;
            }

            paginationDiv.classList.remove('hidden');
            paginationDiv.className = 'p-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center space-x-2';

            window.renderPagination = function (tId, page, pCount, rPP) {
                const t = document.getElementById(tId);
                const tb = t.querySelector('tbody');
                const rs = Array.from(tb.querySelectorAll('tr'));

                // Show/Hide rows
                rs.forEach((row, i) => row.style.display = (i >= (page - 1) * rPP && i < page * rPP) ? '' : 'none');
                t.dataset.currentPage = page;

                // Find Pagination Div
                let pDivId = 'pagination-' + tId.replace('table-', '');
                if (tId === 'table-filtered-result') pDivId = 'pagination-filtered-result';

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

                pDiv.innerHTML = html;
            };

            // Initial render
            window.renderPagination(tableId, 1, pageCount, rowsPerPage);
        }
    </script>
@endsection