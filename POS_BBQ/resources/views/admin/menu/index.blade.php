@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                    </svg>
                    Menu Items
                </h1>
                <div class="flex gap-4">
                    <a href="{{ route('categories.index') }}"
                        class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        Manage Categories
                    </a>
                    <a href="{{ route('menu.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add New Item
                    </a>
                </div>
            </div>



            <!-- Search Bar -->
            <div class="mb-6">
                <div class="relative max-w-md">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" id="menuSearch" placeholder="Search menu items by name..."
                        class="pl-10 pr-4 py-2 w-full border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        onkeyup="searchMenuItems()">
                </div>
            </div>

            <!-- Tabbed Interface -->
            <div x-data="{ activeTab: 'cat-0' }">
                <!-- Tabs Navigation -->
                <div class="border-b border-gray-200 mb-6 overflow-x-auto">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        @foreach($menuItemsByCategory as $categoryName => $items)
                            <button @click="activeTab = 'cat-{{ $loop->index }}'"
                                :class="{ 'border-blue-500 text-blue-600': activeTab === 'cat-{{ $loop->index }}', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'cat-{{ $loop->index }}' }"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                                {{ $categoryName }}
                                <span class="bg-gray-100 text-gray-900 ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium md:inline-block">
                                    {{ $items->count() }}
                                </span>
                            </button>
                        @endforeach
                    </nav>
                </div>

                <!-- Tab Contents -->
                @forelse($menuItemsByCategory as $categoryName => $items)
                    <div x-show="activeTab === 'cat-{{ $loop->index }}'" class="mb-8 category-section" data-category="{{ $categoryName }}">
                        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
                            <table class="min-w-full bg-white divide-y divide-gray-200" id="table-cat-{{ $loop->index }}">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="py-3 px-6 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="py-3 px-6 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                        <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
                                        <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                        <th class="py-3 px-6 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($items as $item)
                                        @php
                                            // Check if branch pivot exists AND is available
                                            $branch1Pivot = $item->branches->firstWhere('id', 1);
                                            $branch2Pivot = $item->branches->firstWhere('id', 2);
                                            
                                            $branch1Available = $branch1Pivot ? $branch1Pivot->pivot->is_available : false;
                                            $branch2Available = $branch2Pivot ? $branch2Pivot->pivot->is_available : false;

                                            // Determine status
                                            if ($branch1Available && $branch2Available) {
                                                $branchStatus = 'both';
                                            } elseif ($branch1Available) {
                                                $branchStatus = 'branch1';
                                            } elseif ($branch2Available) {
                                                $branchStatus = 'branch2';
                                            } else {
                                                $branchStatus = 'none';
                                            }
                                        @endphp
                                        <tr class="menu-item-row hover:bg-gray-50 transition-colors" data-name="{{ strtolower($item->name) }}">
                                            <td class="py-4 px-6 text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                            <td class="py-4 px-6 text-sm text-right text-gray-600">₱{{ number_format($item->price, 2) }}</td>
                                            <td class="py-4 px-6 text-center">
                                                <div class="inline-flex items-center gap-2 justify-center w-full">
                                                    <span id="availability-label-{{ $item->id }}" class="flex items-center">
                                                        @if($item->is_available)
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                            </svg>
                                                            Available
                                                        @else
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                            Not Available
                                                        @endif
                                                    </span>
                                                    <button onclick="showAvailabilityEdit({{ $item->id }})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div id="availability-edit-{{ $item->id }}" class="hidden inline-flex items-center gap-2 justify-center w-full">
                                                    <select id="availability-select-{{ $item->id }}" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm py-1">
                                                        <option value="1" {{ $item->is_available ? 'selected' : '' }}>Available</option>
                                                        <option value="0" {{ !$item->is_available ? 'selected' : '' }}>Not Available</option>
                                                    </select>
                                                    <button onclick="saveAvailability({{ $item->id }})" class="text-green-600 hover:text-green-800">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                    <button onclick="cancelAvailabilityEdit({{ $item->id }})" class="text-red-600 hover:text-red-800">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <div class="inline-flex items-center gap-2 justify-center w-full">
                                                    <span id="branch-label-{{ $item->id }}" class="text-sm text-gray-600">
                                                        @if($branchStatus === 'both')
                                                            Both Branches
                                                        @elseif($branchStatus === 'branch1')
                                                            Branch 1
                                                        @elseif($branchStatus === 'branch2')
                                                            Branch 2
                                                        @else
                                                            <span class="text-red-500 italic">No Branch Assigned</span>
                                                        @endif
                                                    </span>
                                                    <button onclick="showBranchEdit({{ $item->id }})" class="text-gray-400 hover:text-blue-600 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div id="branch-edit-{{ $item->id }}" class="hidden inline-flex items-center gap-2 justify-center w-full">
                                                    <select id="branch-select-{{ $item->id }}" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm py-1">
                                                        <option value="branch1" {{ $branchStatus === 'branch1' ? 'selected' : '' }}>Branch 1</option>
                                                        <option value="branch2" {{ $branchStatus === 'branch2' ? 'selected' : '' }}>Branch 2</option>
                                                        <option value="both" {{ $branchStatus === 'both' ? 'selected' : '' }}>Both Branches</option>
                                                    </select>
                                                    <button onclick="saveBranch({{ $item->id }})" class="text-green-600 hover:text-green-800">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                    <button onclick="cancelBranchEdit({{ $item->id }})" class="text-red-600 hover:text-red-800">
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-4 px-6 text-center">
                                                <div class="flex justify-center items-center space-x-2">
                                                    <a href="{{ route('menu.show', ['menu' => $item, 'source' => 'menu']) }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center transition-colors">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                                            <polyline points="14 2 14 8 20 8"></polyline>
                                                            <circle cx="11.5" cy="15.5" r="2.5"></circle>
                                                            <path d="M16 20l-2-2"></path>
                                                        </svg>
                                                        View
                                                    </a>
                                                    <form id="deleteMenuForm{{ $item->id }}" action="{{ route('menu.destroy', $item) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="text-gray-500 hover:text-red-600 inline-flex items-center transition-colors ml-2" onclick="confirmArchiveMenu({{ $item->id }})">
                                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                                                <path d="M4 10h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V10Z"></path>
                                                                <path d="M6 10V7h12v3"></path>
                                                                <path d="M8 7V5h8v2"></path>
                                                                <rect x="9" y="14" width="6" height="2" rx="0.5"></rect>
                                                                <path d="M4 10l2-4h12l2 4"></path>
                                                            </svg>
                                                            Archive
                                                        </button>
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
                    <div class="text-center py-8 text-gray-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto mb-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        <p class="text-lg font-medium">No menu items found</p>
                        <p class="text-sm">Add your first menu item to get started.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        // Pagination Logic
        document.addEventListener('DOMContentLoaded', function() {
            @foreach($menuItemsByCategory as $categoryName => $items)
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

            const paginationDivId = 'pagination-' + tableId.replace('table-', '');
            let paginationDiv = document.getElementById(paginationDivId);
            if (!paginationDiv) return;

            if (rowCount <= rowsPerPage) {
                paginationDiv.classList.add('hidden');
                rows.forEach(row => row.style.display = '');
                return;
            }

            paginationDiv.classList.remove('hidden');
            paginationDiv.className = 'p-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center space-x-2';

            window.renderPagination = function(tId, page, pCount, rPP) {
                const t = document.getElementById(tId);
                const tb = t.querySelector('tbody');
                const rs = Array.from(tb.querySelectorAll('tr'));

                // Show/Hide rows
                rs.forEach((row, i) => row.style.display = (i >= (page - 1) * rPP && i < page * rPP) ? '' : 'none');
                t.dataset.currentPage = page;

                // Find Pagination Div
                let pDiv = document.getElementById('pagination-' + tId.replace('table-', ''));
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
                            ? 'bg-gray-800 text-white font-bold' 
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

        // Search functionality
        function searchMenuItems() {
            const searchValue = document.getElementById('menuSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.menu-item-row');

            rows.forEach(row => {
                const nameText = row.dataset.name;
                if (nameText.indexOf(searchValue) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Availability inline editing
        function showAvailabilityEdit(menuId) {
            document.getElementById(`availability-label-${menuId}`).parentElement.classList.add('hidden');
            document.getElementById(`availability-edit-${menuId}`).classList.remove('hidden');
        }

        function cancelAvailabilityEdit(menuId) {
            document.getElementById(`availability-label-${menuId}`).parentElement.classList.remove('hidden');
            document.getElementById(`availability-edit-${menuId}`).classList.add('hidden');
        }

        function saveAvailability(menuId) {
            const isAvailable = document.getElementById(`availability-select-${menuId}`).value;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/menu/${menuId}/update-availability`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="is_available" value="${isAvailable}">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }

        // Branch inline editing
        function showBranchEdit(menuId) {
            document.getElementById(`branch-label-${menuId}`).parentElement.classList.add('hidden');
            document.getElementById(`branch-edit-${menuId}`).classList.remove('hidden');
        }

        function cancelBranchEdit(menuId) {
            document.getElementById(`branch-label-${menuId}`).parentElement.classList.remove('hidden');
            document.getElementById(`branch-edit-${menuId}`).classList.add('hidden');
        }

        function saveBranch(menuId) {
            const branch = document.getElementById(`branch-select-${menuId}`).value;
            
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/menu/${menuId}/update-branch-availability`;
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            form.innerHTML = `
                <input type="hidden" name="_token" value="${csrfToken}">
                <input type="hidden" name="branch" value="${branch}">
            `;
            
            document.body.appendChild(form);
            form.submit();
        }

        function confirmArchiveMenu(itemId) {
            AlertModal.showConfirm(
                'Are you sure you want to archive this item?',
                function() {
                    document.getElementById('deleteMenuForm' + itemId).submit();
                },
                null,
                'Archive Confirmation'
            );
        }
    </script>
@endsection