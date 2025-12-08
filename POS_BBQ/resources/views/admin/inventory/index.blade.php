@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Inventory Management</h1>
                <div>
                    <button onclick="showExportConfirmation()"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Export PDF
                    </button>
                    <a href="{{ route('inventory.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Item
                    </a>
                </div>
            </div>



            <!-- Filter Form -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form id="filterForm" action="{{ route('inventory.index') }}" method="GET"
                    class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier" id="supplier"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Suppliers</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier }}" {{ request('supplier') == $supplier ? 'selected' : '' }}>
                                    {{ $supplier }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" id="category"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            onchange="document.getElementById('filterForm').submit()">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <button type="submit"
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter
                        </button>
                        @if(request()->hasAny(['supplier', 'category']))
                            <a href="{{ route('inventory.index') }}"
                                class="ml-2 text-gray-600 hover:text-gray-900 flex items-center inline-flex">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                                Clear
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full bg-white">
                    <thead>
                        <tr>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                ID</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Name</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Supplier</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Quantity</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($inventoryItems as $item)
                            <tr>
                                <td class="py-2 px-4">{{ $item->id }}</td>
                                <td class="py-2 px-4">{{ $item->name }}</td>
                                <td class="py-2 px-4">{{ $item->category ?? '-' }}</td>
                                <td class="py-2 px-4">{{ $item->supplier ?? '-' }}</td>
                                <td class="py-2 px-4 text-right">{{ $item->quantity }}</td>
                                <td class="py-2 px-4 text-center">{{ $item->unit }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded {{ $item->quantity <= $item->reorder_level ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }} items-center">
                                        @if($item->quantity <= $item->reorder_level)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                            </svg>
                                            Low Stock
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                            In Stock
                                        @endif
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <a href="{{ route('inventory.show', $item) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-blue-600 hover:bg-blue-100 rounded"
                                        title="View">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>View</span>
                                    </a>
                                    <form id="deleteInventoryForm{{ $item->id }}"
                                        action="{{ route('inventory.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-red-600 hover:bg-red-100 rounded"
                                            onclick="confirmArchive({{ $item->id }})" title="Archive">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                                            </svg>
                                            <span>Archive</span>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 px-4 text-center text-gray-500">No inventory items found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
    </script>
@endsection