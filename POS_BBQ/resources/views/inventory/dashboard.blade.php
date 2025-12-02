@extends('layouts.inventory')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">Inventory Management</h3>
                <button onclick="document.getElementById('addStockModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add New Ingredient</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ingredient Name
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sold
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unsold
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Spoilage
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock-In
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock-Out
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Remaining
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Unit
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($inventories as $inventory)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                    {{ $inventory->name }}
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
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-semibold {{ $inventory->quantity <= $inventory->reorder_level ? 'text-red-600' : 'text-green-600' }}">
                                    {{ number_format($inventory->quantity, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    {{ $inventory->unit }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <button onclick="openUpdateModal('{{ $inventory->id }}', '{{ $inventory->name }}')" class="text-indigo-600 hover:text-indigo-900 mr-3">Update Stock</button>
                                    <form action="{{ route('inventory.destroy', $inventory->id) }}" method="POST" class="inline-block" onsubmit="return false;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="showConfirm('Are you sure you want to archive this item?', () => this.closest('form').submit())" class="text-red-600 hover:text-red-900">Archive</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    No ingredients found. Click "Add New Ingredient" to get started.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
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
                <div class="mb-4">
                    <label for="reorder_level" class="block text-sm font-medium text-gray-700 mb-1">Reorder Level</label>
                    <input type="number" name="reorder_level" id="reorder_level" step="0.01" required
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('addStockModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Add
                        Ingredient</button>
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
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Update
                        Stock</button>
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
    </script>
@endsection