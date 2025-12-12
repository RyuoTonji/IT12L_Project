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

            <!-- Category-Grouped Tables -->
            @forelse($menuItemsByCategory as $categoryName => $items)
                <div class="mb-8 category-section" data-category="{{ $categoryName }}">
                    <div class="bg-gray-100 px-4 py-3 rounded-t-lg border-l-4 border-blue-500">
                        <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            {{ $categoryName }}
                            <span class="ml-2 text-sm font-normal text-gray-500">({{ $items->count() }} items)</span>
                        </h2>
                    </div>
                    <div class="overflow-x-auto border border-t-0 rounded-b-lg">
                        <table class="min-w-full bg-white">
                            <thead>
                                <tr>
                                    <th class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                                    <th class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Availability</th>
                                    <th class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Branch</th>
                                    <th class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach($items as $item)
                                    @php
                                        $branch1Available = $item->branches->firstWhere('id', 1)?->pivot->is_available ?? false;
                                        $branch2Available = $item->branches->firstWhere('id', 2)?->pivot->is_available ?? false;

                                        if ($branch1Available && $branch2Available) {
                                            $branchStatus = 'both';
                                        } elseif ($branch1Available) {
                                            $branchStatus = 'branch1';
                                        } elseif ($branch2Available) {
                                            $branchStatus = 'branch2';
                                        } else {
                                            $branchStatus = 'both';
                                        }
                                    @endphp
                                    <tr class="menu-item-row" data-name="{{ strtolower($item->name) }}">
                                        <td class="py-2 px-4">{{ $item->name }}</td>
                                        <td class="py-2 px-4 text-right">₱{{ number_format($item->price, 2) }}</td>
                                        <td class="py-2 px-4 text-center">
                                            <div class="inline-flex items-center gap-2">
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
                                                <button onclick="showAvailabilityEdit({{ $item->id }}, {{ $item->is_available ? 1 : 0 }})" class="text-gray-500 hover:text-blue-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div id="availability-edit-{{ $item->id }}" class="hidden inline-flex items-center gap-2">
                                                <select id="availability-select-{{ $item->id }}" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm">
                                                    <option value="1" {{ $item->is_available ? 'selected' : '' }}>Available</option>
                                                    <option value="0" {{ !$item->is_available ? 'selected' : '' }}>Not Available</option>
                                                </select>
                                                <button onclick="saveAvailability({{ $item->id }})" class="text-green-600 hover:text-green-800" title="Save">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button onclick="cancelAvailabilityEdit({{ $item->id }})" class="text-red-600 hover:text-red-800" title="Cancel">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <div class="inline-flex items-center gap-2">
                                                <span id="branch-label-{{ $item->id }}">
                                                    @if($branchStatus === 'both')
                                                        Both Branches
                                                    @elseif($branchStatus === 'branch1')
                                                        Branch 1
                                                    @else
                                                        Branch 2
                                                    @endif
                                                </span>
                                                <button onclick="showBranchEdit({{ $item->id }}, '{{ $branchStatus }}')" class="text-gray-500 hover:text-blue-600">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div id="branch-edit-{{ $item->id }}" class="hidden inline-flex items-center gap-2">
                                                <select id="branch-select-{{ $item->id }}" class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 text-sm">
                                                    <option value="branch1" {{ $branchStatus === 'branch1' ? 'selected' : '' }}>Branch 1</option>
                                                    <option value="branch2" {{ $branchStatus === 'branch2' ? 'selected' : '' }}>Branch 2</option>
                                                    <option value="both" {{ $branchStatus === 'both' ? 'selected' : '' }}>Both Branches</option>
                                                </select>
                                                <button onclick="saveBranch({{ $item->id }})" class="text-green-600 hover:text-green-800" title="Save">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                                <button onclick="cancelBranchEdit({{ $item->id }})" class="text-red-600 hover:text-red-800" title="Cancel">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="py-2 px-4 text-center">
                                            <a href="{{ route('menu.show', $item) }}" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-100 rounded">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                                <span>View</span>
                                            </a>
                                            <form id="deleteMenuForm{{ $item->id }}" action="{{ route('menu.destroy', $item) }}" method="POST" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded" onclick="confirmArchiveMenu({{ $item->id }})">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                                    </svg>
                                                    <span>Archive</span>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
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

    <script>
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
        function showAvailabilityEdit(menuId, currentValue) {
            document.getElementById(`availability-label-${menuId}`).parentElement.classList.add('hidden');
            document.getElementById(`availability-edit-${menuId}`).classList.remove('hidden');
        }

        function cancelAvailabilityEdit(menuId) {
            document.getElementById(`availability-label-${menuId}`).parentElement.classList.remove('hidden');
            document.getElementById(`availability-edit-${menuId}`).classList.add('hidden');
        }

        function saveAvailability(menuId) {
            const isAvailable = document.getElementById(`availability-select-${menuId}`).value;
            
            // Create and submit form
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
        function showBranchEdit(menuId, currentValue) {
            document.getElementById(`branch-label-${menuId}`).parentElement.classList.add('hidden');
            document.getElementById(`branch-edit-${menuId}`).classList.remove('hidden');
        }

        function cancelBranchEdit(menuId) {
            document.getElementById(`branch-label-${menuId}`).parentElement.classList.remove('hidden');
            document.getElementById(`branch-edit-${menuId}`).classList.add('hidden');
        }

        function saveBranch(menuId) {
            const branch = document.getElementById(`branch-select-${menuId}`).value;
            
            // Create and submit form
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