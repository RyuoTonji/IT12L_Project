@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    Inventory Management
                    </h1>
                    <div>
                        <button onclick="showExportConfirmation()"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2zm2-10l4 4m-4 0l4-4m-4 4V7" />
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
                    <div class="flex items-center">
                        @if(request()->hasAny(['supplier', 'category']))
                            <a href="{{ route('inventory.index') }}"
                                class="text-gray-600 hover:text-gray-900 flex items-center inline-flex">
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

            <!-- Category-Grouped Tables -->
            @if(!request('category'))
                @forelse($inventoryByCategory as $categoryName => $items)
                    <div class="mb-8 category-section">
                        <div class="bg-gray-100 px-4 py-3 rounded-t-lg border-l-4 border-green-500">
                            <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-600" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                {{ $categoryName ?? 'Uncategorized' }}
                                <span class="ml-2 text-sm font-normal text-gray-500">({{ $items->count() }} items)</span>
                            </h2>
                        </div>
                        <div class="overflow-x-auto border border-t-0 rounded-b-lg">
                            <table class="min-w-full bg-white">
                                <thead>
                                    <tr>
                                        <th
                                            class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Name</th>
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
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="py-2 px-4">{{ $item->name }}</td>
                                            <td class="py-2 px-4">{{ $item->supplier ?? '-' }}</td>
                                            <td class="py-2 px-4 text-right">{{ $item->quantity }}</td>
                                            <td class="py-2 px-4 text-center">{{ $item->unit }}</td>
                                            <td class="py-2 px-4 text-center">
                                                <span
                                                    class="inline-flex text-xs leading-5 font-semibold {{ $item->quantity <= $item->reorder_level ? 'text-red-600' : 'text-green-600' }} items-center">
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
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                        viewBox="0 0 24 24" stroke="currentColor">
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
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded"
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
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-gray-500">
                        <p class="text-lg font-medium">No inventory items found</p>
                    </div>
                @endforelse
            @else
                <!-- Single category filter - show as standard table -->
                <div class="overflow-x-auto">
                    <table class="min-w-full bg-white">
                        <thead>
                            <tr>
                                <th
                                    class="py-2 px-4 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Name</th>
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
                                    <td class="py-2 px-4">{{ $item->name }}</td>
                                    <td class="py-2 px-4">{{ $item->supplier ?? '-' }}</td>
                                    <td class="py-2 px-4 text-right">{{ $item->quantity }}</td>
                                    <td class="py-2 px-4 text-center">{{ $item->unit }}</td>
                                    <td class="py-2 px-4 text-center">
                                        <span
                                            class="inline-flex text-xs leading-5 font-semibold {{ $item->quantity <= $item->reorder_level ? 'text-red-600' : 'text-green-600' }} items-center">
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
                                                class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:bg-gray-100 rounded"
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
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">No inventory items found</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
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
    </script>
@endsection