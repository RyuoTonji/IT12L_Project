@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Add Inventory Item</h1>
                <a href="{{ route('inventory.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Inventory
                </a>
            </div>

            @if($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('inventory.store') }}" method="POST">
                @csrf

                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                        <select name="category" id="category"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <option value="">Select Category</option>
                            <option value="Ingredient" {{ old('category') == 'Ingredient' ? 'selected' : '' }}>Ingredient
                            </option>
                            <option value="Product" {{ old('category') == 'Product' ? 'selected' : '' }}>Product</option>
                            <option value="Other" {{ old('category') == 'Other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>

                    <div>
                        <label for="supplier" class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <input type="text" name="supplier" id="supplier" value="{{ old('supplier') }}"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700 mb-1">Quantity</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity', 0) }}" step="0.01"
                            min="0"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            required>
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700 mb-1">Unit</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit') }}"
                            placeholder="e.g., kg, liters, pieces"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                    <input type="number" name="reorder_level" id="reorder_level" value="{{ old('reorder_level', 10) }}"
                        step="0.01" min="0"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                        required>
                    <p class="text-sm text-gray-500 mt-1">The minimum quantity at which you should reorder this item</p>
                </div>

                <div class="flex justify-end">
                    <button type="button"
                        onclick="showConfirm('Are you sure you want to create this inventory item?', function() { this.form.submit(); }.bind(this))"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Add Inventory Item
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection