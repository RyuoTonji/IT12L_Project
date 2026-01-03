@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Edit Order #{{ $order->id }}</h1>
                <a href="{{ route('orders.show', $order) }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center inline-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Order
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('orders.update', $order) }}" method="POST" id="orderForm">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h2 class="text-lg font-medium mb-4">Order Information</h2>

                        <div class="mb-4">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Order Status</label>
                            <select name="status" id="status"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required>
                                <option value="new" {{ old('status', $order->status) == 'new' ? 'selected' : '' }}>New
                                </option>
                                <option value="preparing" {{ old('status', $order->status) == 'preparing' ? 'selected' : '' }}>Preparing
                                </option>
                                <option value="ready" {{ old('status', $order->status) == 'ready' ? 'selected' : '' }}>Ready
                                </option>
                                <option value="served" {{ old('status', $order->status) == 'served' ? 'selected' : '' }}>
                                    Served</option>
                                <option value="completed" {{ old('status', $order->status) == 'completed' ? 'selected' : '' }}>Completed
                                </option>
                                @if(Auth::user()->role == 'admin' || Auth::user()->role == 'manager')
                                    <option value="cancelled" {{ old('status', $order->status) == 'cancelled' ? 'selected' : '' }}>Cancelled
                                    </option>
                                @endif
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">Order Type</label>
                            <select name="order_type" id="order_type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required>
                                <option value="dine-in" {{ $order->order_type == 'dine-in' ? 'selected' : '' }}>Dine-in
                                </option>
                                <option value="takeout" {{ $order->order_type == 'takeout' ? 'selected' : '' }}>Takeout
                                </option>
                            </select>
                        </div>

                        <div class="mb-4" id="table_section"
                            style="{{ $order->order_type == 'takeout' ? 'display: none;' : '' }}">
                            <label for="table_id" class="block text-sm font-medium text-gray-700 mb-1">Table</label>
                            <select name="table_id" id="table_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select a table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" {{ $order->table_id == $table->id ? 'selected' : '' }}>
                                        {{ $table->name }} (Capacity: {{ $table->capacity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="customer_section">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer
                                Name</label>
                            <input type="text" name="customer_name" id="customer_name" value="{{ $order->customer_name }}"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-medium mb-4">Order Summary</h2>

                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <div class="flex justify-between mb-2">
                                <span class="font-medium">Total Items:</span>
                                <span id="total_items">{{ $order->orderItems->sum('quantity') }}</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="font-medium">Subtotal:</span>
                                <span id="subtotal">₱{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span id="total">₱{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-medium mb-4">Menu Items</h2>

                    <div class="mb-4">
                        <div class="flex space-x-2 overflow-x-auto pb-2">
                            <button type="button" class="category-tab px-4 py-2 bg-blue-600 text-white rounded-t-lg"
                                data-category="all">All</button>
                            @foreach($categories as $category)
                                <button type="button" class="category-tab px-4 py-2 bg-gray-200 text-gray-700 rounded-t-lg"
                                    data-category="{{ $category->id }}">{{ $category->name }}</button>
                            @endforeach
                        </div>

                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <!-- All Categories View (Segmented) -->
                            <div id="all-categories-view">
                                @foreach($categories as $category)
                                    @if($category->menuItems->count() > 0)
                                        <div class="category-section mb-6" data-category-section="{{ $category->id }}">
                                            <div class="mb-3">
                                                <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-gray-300 pb-2">
                                                    {{ $category->name }}
                                                </h3>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                @foreach($category->menuItems as $item)
                                                    <div class="menu-item bg-white p-3 rounded shadow-sm border cursor-pointer hover:shadow-md transition-shadow relative overflow-hidden"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}" data-category="{{ $category->id }}"
                                                        data-max-quantity="{{ $item->max_quantity }}">
                                                        <div class="font-medium">{{ $item->name }}</div>
                                                        <div class="text-sm text-gray-600 mb-1">{{ Str::limit($item->description, 50) }}
                                                        </div>
                                                        <div class="text-blue-600 font-bold">₱{{ number_format($item->price, 2) }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">Available:
                                                            {{ $item->max_quantity }}
                                                        </div>
                                                        @if($item->max_quantity < 1)
                                                            <div
                                                                class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center">
                                                                <span
                                                                    class="text-red-600 font-bold rotate-12 border-2 border-red-600 px-2 py-1 rounded">SOLD
                                                                    OUT</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>

                            <!-- Single Category View -->
                            <div id="single-category-view" class="hidden">
                                @foreach($categories as $category)
                                    @if($category->menuItems->count() > 0)
                                        <div class="category-section-single" data-category-section="{{ $category->id }}">
                                            <div class="mb-3">
                                                <h3 class="text-lg font-semibold text-gray-800 border-b-2 border-gray-300 pb-2">
                                                    {{ $category->name }}
                                                </h3>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                                @foreach($category->menuItems as $item)
                                                    <div class="menu-item-single bg-white p-3 rounded shadow-sm border cursor-pointer hover:shadow-md transition-shadow relative overflow-hidden"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}"
                                                        data-price="{{ $item->price }}" data-category="{{ $category->id }}"
                                                        data-max-quantity="{{ $item->max_quantity }}">
                                                        <div class="font-medium">{{ $item->name }}</div>
                                                        <div class="text-sm text-gray-600 mb-1">{{ Str::limit($item->description, 50) }}
                                                        </div>
                                                        <div class="text-blue-600 font-bold">₱{{ number_format($item->price, 2) }}</div>
                                                        <div class="text-xs text-gray-500 mt-1">Available:
                                                            {{ $item->max_quantity }}
                                                        </div>
                                                        @if($item->max_quantity < 1)
                                                            <div
                                                                class="absolute inset-0 bg-gray-200 bg-opacity-75 flex items-center justify-center">
                                                                <span
                                                                    class="text-red-600 font-bold rotate-12 border-2 border-red-600 px-2 py-1 rounded">SOLD
                                                                    OUT</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-medium mb-4">Selected Items</h2>

                    <div class="bg-white rounded-lg border overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Item</th>
                                    <th
                                        class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Price</th>
                                    <th
                                        class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Quantity</th>
                                    <th
                                        class="py-3 px-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Notes</th>
                                    <th
                                        class="py-3 px-4 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Subtotal</th>
                                    <th
                                        class="py-3 px-4 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200" id="selected_items_container">
                                <tr id="no_items_row" style="{{ $order->orderItems->count() > 0 ? 'display: none;' : '' }}">
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">No items selected</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end space-x-4">
                    <button type="button"
                        onclick="showConfirm('Are you sure you want to cancel editing?', function() { window.location.href='{{ route('cashier.dashboard') }}' })"
                        class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center inline-flex"
                        id="submit_order">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Update Order
                    </button>
                </div>
            </form>
        </div>
    </div>



    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const categoryTabs = document.querySelectorAll('.category-tab');
            const menuItems = document.querySelectorAll('.menu-item');
            const selectedItemsContainer = document.getElementById('selected_items_container');
            const noItemsRow = document.getElementById('no_items_row');
            const totalItemsElement = document.getElementById('total_items');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');
            const orderForm = document.getElementById('orderForm');

            // Order Type Logic
            const orderType = document.getElementById('order_type');
            const tableSection = document.getElementById('table_section');
            const tableId = document.getElementById('table_id');
            const customerSection = document.getElementById('customer_section');

            if (orderType) {
                orderType.addEventListener('change', function () {
                    if (this.value === 'dine-in') {
                        tableSection.style.display = 'block';
                        // Keep customer section visible as per previous requirement
                    } else {
                        tableSection.style.display = 'none';
                        tableId.value = '';
                    }
                });
            }

            // Load existing order items
            @foreach($order->orderItems as $item)
                addItemToSelection({
                    id: {{ $item->menu_item_id }},
                    name: @json($item->menuItem->name),
                    price: {{ $item->unit_price }},
                    quantity: {{ $item->quantity }},
                    notes: @json($item->notes ?? ''),
                    itemId: {{ $item->id }}
                                                                                });
            @endforeach

            // Handle category tab clicks
            categoryTabs.forEach(tab => {
                tab.addEventListener('click', function () {
                    categoryTabs.forEach(t => t.classList.remove('bg-blue-600', 'text-white'));
                    categoryTabs.forEach(t => t.classList.add('bg-gray-200', 'text-gray-700'));
                    this.classList.remove('bg-gray-200', 'text-gray-700');
                    this.classList.add('bg-blue-600', 'text-white');

                    const categoryId = this.dataset.category;
                    const allCategoriesView = document.getElementById('all-categories-view');
                    const singleCategoryView = document.getElementById('single-category-view');

                    if (categoryId === 'all') {
                        // Show segmented view with all categories
                        allCategoriesView.classList.remove('hidden');
                        singleCategoryView.classList.add('hidden');
                    } else {
                        // Show single category view
                        allCategoriesView.classList.add('hidden');
                        singleCategoryView.classList.remove('hidden');

                        // Show/hide category sections
                        const categorySections = document.querySelectorAll('.category-section-single');
                        categorySections.forEach(section => {
                            if (section.dataset.categorySection === categoryId) {
                                section.style.display = 'block';
                            } else {
                                section.style.display = 'none';
                            }
                        });
                    }
                });
            });

            // Handle menu item clicks (helper function)
            function setupMenuItemClick(item) {
                item.addEventListener('click', function () {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;
                    const itemPrice = parseFloat(this.dataset.price);

                    // In edit mode we don't strictly enforce max quantity check on click creation 
                    // because we might just be adding to list, validation happens on input change mainly.
                    // But if consistent with Create, we can check maxQuantity if we had it.

                    // Check if item already exists in the selected items
                    const existingItem = document.querySelector(`#selected_items_container tr[data-id="${itemId}"]`);

                    if (existingItem) {
                        // Increment quantity
                        const quantityInput = existingItem.querySelector('.quantity-input');
                        quantityInput.value = parseInt(quantityInput.value) + 1;
                        updateItemSubtotal(existingItem);
                    } else {
                        // Add new item
                        addItemToSelection({
                            id: itemId,
                            name: itemName,
                            price: itemPrice,
                            quantity: 1,
                            notes: ''
                        });
                    }
                });
            }

            // Setup click handlers for both views
            const menuItemsOne = document.querySelectorAll('.menu-item');
            const menuItemsSingle = document.querySelectorAll('.menu-item-single');
            menuItemsOne.forEach(item => setupMenuItemClick(item));
            menuItemsSingle.forEach(item => setupMenuItemClick(item));

            function addItemToSelection(item) {
                const newRow = document.createElement('tr');
                newRow.dataset.id = item.id;
                newRow.dataset.price = item.price;

                const index = document.querySelectorAll('#selected_items_container tr[data-id]').length;
                const itemIdField = item.itemId ? `<input type="hidden" name="items[${index}][id]" value="${item.itemId}">` : '';

                newRow.innerHTML = `
                                                            <td class="py-2 px-4">${item.name}</td>
                                                            <td class="py-2 px-4 text-right">₱${item.price.toFixed(2)}</td>
                                                            <td class="py-2 px-4 text-center">
                                                                <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" class="quantity-input w-16 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                                <input type="hidden" name="items[${index}][menu_item_id]" value="${item.id}">
                                                                ${itemIdField}
                                                            </td>
                                                            <td class="py-2 px-4">
                                                                <input type="text" name="items[${index}][notes]" value="${item.notes}" placeholder="Special instructions" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                            </td>
                                                            <td class="py-2 px-4 text-right item-subtotal">₱${(item.price * item.quantity).toFixed(2)}</td>
                                                            <td class="py-2 px-4 text-center">
                                                                <button type="button" class="text-red-600 hover:text-red-900 remove-item">Remove</button>
                                                            </td>
                                                        `;

                selectedItemsContainer.appendChild(newRow);

                // Add event listeners to the new row
                const quantityInput = newRow.querySelector('.quantity-input');
                quantityInput.addEventListener('change', function () {
                    if (parseInt(this.value) < 1) this.value = 1;
                    updateItemSubtotal(newRow);
                });

                const removeButton = newRow.querySelector('.remove-item');
                removeButton.addEventListener('click', function () {
                    newRow.remove();
                    updateOrderSummary();
                    checkNoItems();
                    reindexItems();
                });

                updateOrderSummary();
                checkNoItems();
            }

            // Update item subtotal
            function updateItemSubtotal(row) {
                const price = parseFloat(row.dataset.price);
                const quantity = parseInt(row.querySelector('.quantity-input').value);
                const subtotal = price * quantity;
                row.querySelector('.item-subtotal').textContent = `₱${subtotal.toFixed(2)}`;
                updateOrderSummary();
            }

            // Update order summary
            function updateOrderSummary() {
                const items = selectedItemsContainer.querySelectorAll('tr[data-id]');
                let totalItems = 0;
                let subtotal = 0;

                items.forEach(item => {
                    const quantity = parseInt(item.querySelector('.quantity-input').value);
                    const price = parseFloat(item.dataset.price);
                    totalItems += quantity;
                    subtotal += price * quantity;
                });

                totalItemsElement.textContent = totalItems;
                subtotalElement.textContent = `₱${subtotal.toFixed(2)}`;
                totalElement.textContent = `₱${subtotal.toFixed(2)}`;
            }

            // Check if no items are selected
            function checkNoItems() {
                const items = selectedItemsContainer.querySelectorAll('tr[data-id]');
                if (items.length === 0) {
                    noItemsRow.style.display = 'table-row';
                } else {
                    noItemsRow.style.display = 'none';
                }
            }

            // Reindex items when one is removed
            function reindexItems() {
                const items = selectedItemsContainer.querySelectorAll('tr[data-id]');
                items.forEach((item, index) => {
                    const quantityInput = item.querySelector('input[name^="items"][name$="[quantity]"]');
                    const menuItemIdInput = item.querySelector('input[name^="items"][name$="[menu_item_id]"]');
                    const notesInput = item.querySelector('input[name^="items"][name$="[notes]"]');
                    const itemIdInput = item.querySelector('input[name^="items"][name$="[id]"]');

                    if (quantityInput) quantityInput.name = `items[${index}][quantity]`;
                    if (menuItemIdInput) menuItemIdInput.name = `items[${index}][menu_item_id]`;
                    if (notesInput) notesInput.name = `items[${index}][notes]`;
                    if (itemIdInput) itemIdInput.name = `items[${index}][id]`;
                });
            }

            // Form submission validation
            orderForm.addEventListener('submit', function (e) {
                e.preventDefault(); // Prevent default submission initially

                const items = selectedItemsContainer.querySelectorAll('tr[data-id]');
                if (items.length === 0) {
                    alert('Please add at least one item to the order');
                    return;
                }

                showConfirm('Are you sure you want to save these changes?', function () {
                    // Convert the form data to the expected format
                    items.forEach((item, index) => {
                        const itemId = item.dataset.id;
                        const quantityInput = item.querySelector('.quantity-input');
                        const notesInput = item.querySelector('input[name^="items"][name$="[notes]"]');
                        const orderItemIdInput = item.querySelector('input[name^="items"][name$="[id]"]');

                        // Create new inputs with the correct array format
                        const menuItemIdInput = document.createElement('input');
                        menuItemIdInput.type = 'hidden';
                        menuItemIdInput.name = `items[${index}][menu_item_id]`;
                        menuItemIdInput.value = itemId;

                        const quantityNewInput = document.createElement('input');
                        quantityNewInput.type = 'hidden';
                        quantityNewInput.name = `items[${index}][quantity]`;
                        quantityNewInput.value = quantityInput.value;

                        const notesNewInput = document.createElement('input');
                        notesNewInput.type = 'hidden';
                        notesNewInput.name = `items[${index}][notes]`;
                        notesNewInput.value = notesInput.value;

                        // Append new inputs to the form
                        orderForm.appendChild(menuItemIdInput);
                        orderForm.appendChild(quantityNewInput);
                        orderForm.appendChild(notesNewInput);

                        // If it's an existing item, add the ID
                        if (orderItemIdInput) {
                            const idInput = document.createElement('input');
                            idInput.type = 'hidden';
                            idInput.name = `items[${index}][id]`;
                            idInput.value = orderItemIdInput.value;
                            orderForm.appendChild(idInput);
                        }
                    });

                    orderForm.submit(); // Submit the form
                });
            });
        });
    </script>
@endsection