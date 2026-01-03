@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Inventory Item Details</h1>
                <div>
                    <a href="{{ route('admin.inventory.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Inventory
                    </a>
                    <a href="{{ route('admin.inventory.edit', $inventory) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Item
                    </a>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Name</p>
                        <p class="font-medium">{{ $inventory->name }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <p>
                            <span
                                class="px-2 py-1 rounded text-xs font-semibold
                                    {{ $inventory->quantity <= $inventory->reorder_level ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                                {{ $inventory->quantity <= $inventory->reorder_level ? 'Low Stock' : 'In Stock' }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Category</p>
                        <p class="font-medium">{{ $inventory->category ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Supplier</p>
                        <p class="font-medium">{{ $inventory->supplier ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Quantity</p>
                        <p class="font-medium">{{ $inventory->quantity }} {{ $inventory->unit }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Reorder Level</p>
                        <p class="font-medium">{{ $inventory->reorder_level }} {{ $inventory->unit }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Created At</p>
                        <p class="font-medium">{{ $inventory->created_at->format('M d, Y H:i') }}</p>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">Last Updated</p>
                        <p class="font-medium">{{ $inventory->updated_at->format('M d, Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center">
                <h2 class="text-xl font-medium">Stock Management</h2>
            </div>

            <div class="bg-white p-4 rounded-lg shadow border mt-4">
                <h3 class="font-medium mb-4">Update Stock</h3>

                <form action="{{ route('admin.inventory.update', $inventory) }}" method="POST"
                    class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="name" value="{{ $inventory->name }}">
                    <input type="hidden" name="unit" value="{{ $inventory->unit }}">
                    <input type="hidden" name="reorder_level" value="{{ $inventory->reorder_level }}">

                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Current Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="{{ $inventory->quantity }}" step="0.01"
                            min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            required>
                    </div>

                    <div class="md:col-span-2 flex items-end">
                        <button type="button"
                            onclick="showConfirm('Are you sure you want to update the stock for this item?', function() { this.form.submit(); }.bind(this))"
                            class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center inline-flex">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Update Stock
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection