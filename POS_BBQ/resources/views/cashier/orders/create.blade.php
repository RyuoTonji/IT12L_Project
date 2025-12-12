@extends('layouts.cashier')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Create New Order</h1>
                <a href="{{ route('orders.index') }}"
                    class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Orders
                </a>
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            <form action="{{ route('orders.store') }}" method="POST" id="orderForm">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h2 class="text-lg font-medium mb-4">Order Information</h2>

                        <div class="mb-4">
                            <label for="order_type" class="block text-sm font-medium text-gray-700 mb-1">Order Type</label>
                            <select name="order_type" id="order_type"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                                required>
                                <option value="dine-in" {{ $selectedTable ? 'selected' : '' }}>Dine-in</option>
                                <option value="takeout" {{ !$selectedTable ? 'selected' : '' }}>Takeout</option>
                            </select>
                        </div>

                        <div class="mb-4" id="table_section" style="{{ !$selectedTable ? 'display: none;' : '' }}">
                            <label for="table_id" class="block text-sm font-medium text-gray-700 mb-1">Table</label>
                            <select name="table_id" id="table_id"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                <option value="">Select a table</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table->id }}" {{ $selectedTable && $selectedTable->id == $table->id ? 'selected' : '' }}>
                                        {{ $table->name }} (Capacity: {{ $table->capacity }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-4" id="customer_section" style="{{ $selectedTable ? 'display: none;' : '' }}">
                            <label for="customer_name" class="block text-sm font-medium text-gray-700 mb-1">Customer
                                Name</label>
                            <input type="text" name="customer_name" id="customer_name"
                                class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        </div>
                    </div>

                    <div>
                        <h2 class="text-lg font-medium mb-4">Order Summary</h2>

                        <div class="bg-gray-50 p-4 rounded-lg border">
                            <div class="flex justify-between mb-2">
                                <span class="font-medium">Total Items:</span>
                                <span id="total_items">0</span>
                            </div>
                            <div class="flex justify-between mb-2">
                                <span class="font-medium">Subtotal:</span>
                                <span id="subtotal">₱0.00</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold">
                                <span>Total:</span>
                                <span id="total">₱0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-6">
                    <h2 class="text-lg font-medium mb-4">Menu Items</h2>

                    <div class="mb-4">
                        <div class="flex space-x-2 overflow-x-auto pb-2">
                            <button type="button"
                                class="category-tab px-4 py-2 bg-blue-600 text-white rounded-t-lg flex items-center"
                                data-category="all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                                </svg>
                                All
                            </button>
                            @foreach($categories as $category)
                                <button type="button" class="category-tab px-4 py-2 bg-gray-200 text-gray-700 rounded-t-lg"
                                    data-category="{{ $category->id }}">
                                    {{ $category->name }}
                                </button>
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
                                                    <div class="menu-item bg-white p-3 rounded shadow-sm border cursor-pointer hover:shadow-md transition-shadow"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                                        data-category="{{ $category->id }}">
                                                        <div class="font-medium">{{ $item->name }}</div>
                                                        <div class="text-sm text-gray-600 mb-1">{{ Str::limit($item->description, 50) }}</div>
                                                        <div class="text-blue-600 font-bold">₱{{ number_format($item->price, 2) }}</div>
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
                                                    <div class="menu-item-single bg-white p-3 rounded shadow-sm border cursor-pointer hover:shadow-md transition-shadow"
                                                        data-id="{{ $item->id }}" data-name="{{ $item->name }}" data-price="{{ $item->price }}"
                                                        data-category="{{ $category->id }}">
                                                        <div class="font-medium">{{ $item->name }}</div>
                                                        <div class="text-sm text-gray-600 mb-1">{{ Str::limit($item->description, 50) }}</div>
                                                        <div class="text-blue-600 font-bold">₱{{ number_format($item->price, 2) }}</div>
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
                                <tr id="no_items_row">
                                    <td colspan="6" class="py-4 px-4 text-center text-gray-500">No items selected</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="button" onclick="confirmCreateOrder()"
                        class="px-6 py-2 bg-green-600 text-white rounded hover:bg-green-700 flex items-center" id="submit_order">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Create Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // by id
            const orderType = document.getElementById('order_type');
            const tableSection = document.getElementById('table_section');
            const customerSection = document.getElementById('customer_section');
            const tableId = document.getElementById('table_id');

            // by class - querySelectorAll
            const categoryTabs = document.querySelectorAll('.category-tab');
            const menuItems = document.querySelectorAll('.menu-item');
            const menuItemsSingle = document.querySelectorAll('.menu-item-single');

            // by id
            const selectedItemsContainer = document.getElementById('selected_items_container');
            const noItemsRow = document.getElementById('no_items_row');
            const totalItemsElement = document.getElementById('total_items');
            const subtotalElement = document.getElementById('subtotal');
            const totalElement = document.getElementById('total');
            const orderForm = document.getElementById('orderForm');

            // Handle order type change
            orderType.addEventListener('change', function () {
                if (this.value === 'dine-in') {
                    tableSection.style.display = 'block';
                    customerSection.style.display = 'none';
                } else {
                    tableSection.style.display = 'none';
                    customerSection.style.display = 'block';
                    tableId.value = '';
                }
            });

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

            // Handle menu item clicks (for both views)
            function setupMenuItemClick(item) {
                item.addEventListener('click', function () {
                    const itemId = this.dataset.id;
                    const itemName = this.dataset.name;
                    const itemPrice = parseFloat(this.dataset.price);

                    // Check if item already exists in the selected items
                    const existingItem = document.querySelector(`#selected_items_container tr[data-id="${itemId}"]`);

                    if (existingItem) {
                        // Increment quantity
                        const quantityInput = existingItem.querySelector('.quantity-input');
                        quantityInput.value = parseInt(quantityInput.value) + 1;
                        updateItemSubtotal(existingItem);
                    } else {
                        // Add new item
                        const newRow = document.createElement('tr');
                        newRow.dataset.id = itemId;
                        newRow.dataset.price = itemPrice;

                        newRow.innerHTML = `
                                <td class="py-2 px-4">${itemName}</td>
                                <td class="py-2 px-4 text-right">₱${itemPrice.toFixed(2)}</td>
                                <td class="py-2 px-4 text-center">
                                    <input type="number" name="items[${itemId}][quantity]" value="1" min="1" class="quantity-input w-16 text-center rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <input type="hidden" name="items[${itemId}][menu_item_id]" value="${itemId}">
                                </td>
                                <td class="py-2 px-4">
                                    <input type="text" name="items[${itemId}][notes]" placeholder="Special instructions" class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                </td>
                                <td class="py-2 px-4 text-right item-subtotal">₱${itemPrice.toFixed(2)}</td>
                                <td class="py-2 px-4 text-center">
                                    <button type="button" class="text-red-600 hover:text-red-900 remove-item flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Remove
                                    </button>
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
                        });
                    }

                    updateOrderSummary();
                    checkNoItems();
                });
            }

            // Setup click handlers for both views
            menuItems.forEach(item => setupMenuItemClick(item));
            menuItemsSingle.forEach(item => setupMenuItemClick(item));

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

            // Form submission validation
            orderForm.addEventListener('submit', function (e) {
                const items = selectedItemsContainer.querySelectorAll('tr[data-id]');
                if (items.length === 0) {
                    e.preventDefault();
                    alert('Please add at least one item to the order');
                    return;
                }

                if (orderType.value === 'dine-in' && !tableId.value) {
                    e.preventDefault();
                    alert('Please select a table for dine-in orders');
                    return;
                }

                // Convert the form data to the expected format
                items.forEach((item, index) => {
                    const itemId = item.dataset.id;
                    const quantityInput = item.querySelector('.quantity-input');
                    const notesInput = item.querySelector('input[name^="items"][name$="[notes]"]');

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
                });
            });
        });

        function confirmCreateOrder() {
            const orderForm = document.getElementById('orderForm');
            const selectedItemsContainer = document.getElementById('selected_items_container');
            const orderType = document.getElementById('order_type');
            const tableId = document.getElementById('table_id');
            
            const items = selectedItemsContainer.querySelectorAll('tr[data-id]');
            if (items.length === 0) {
                AlertModal.show('Please add at least one item to the order', 'warning');
                return;
            }

            if (orderType.value === 'dine-in' && !tableId.value) {
                AlertModal.show('Please select a table for dine-in orders', 'warning');
                return;
            }

            AlertModal.showConfirm(
                'Are you sure you want to create this order?',
                function() {
                    // Convert the form data to the expected format
                    items.forEach((item, index) => {
                        const itemId = item.dataset.id;
                        const quantityInput = item.querySelector('.quantity-input');
                        const notesInput = item.querySelector('input[name^="items"][name$="[notes]"]');

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
                    });

                    orderForm.submit();
                },
                null,
                'Create Order Confirmation'
            );
        }
    </script>
@endsection