@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Inventory Management</h1>
                <div>
                    <a href="{{ route('export.inventory') }}"
                        class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 mr-2">Export PDF</a>
                    <a href="{{ route('inventory.create') }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add Item</a>
                </div>
            </div>



            <!-- Filter Form -->
            <div class="mb-6 bg-gray-50 p-4 rounded-lg">
                <form action="{{ route('inventory.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                    <div>
                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="supplier" id="supplier"
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                            class="rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
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
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700">Filter</button>
                        @if(request()->hasAny(['supplier', 'category']))
                            <a href="{{ route('inventory.index') }}" class="ml-2 text-gray-600 hover:text-gray-900">Clear</a>
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
                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $item->quantity <= $item->reorder_level ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                        {{ $item->quantity <= $item->reorder_level ? 'Low Stock' : 'In Stock' }}
                                    </span>
                                </td>
                                <td class="py-2 px-4 text-center">
                                    <a href="{{ route('inventory.show', $item) }}"
                                        class="text-blue-600 hover:text-blue-900 mr-2">View</a>
                                    <a href="{{ route('inventory.edit', $item) }}"
                                        class="text-green-600 hover:text-green-900 mr-2">Edit</a>
                                    <form id="deleteInventoryForm{{ $item->id }}"
                                        action="{{ route('inventory.destroy', $item) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            onclick="showConfirm('Are you sure you want to archive this inventory item?', function() { document.getElementById('deleteInventoryForm{{ $item->id }}').submit(); })"
                                            class="text-red-600 hover:text-red-900">Delete</button>
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
@endsection