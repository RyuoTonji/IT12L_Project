@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <h1 class="text-2xl font-semibold mb-8">Branch Management</h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($branches as $branch)
                    <a href="{{ route('admin.branches.show', $branch->id) }}"
                        class="block bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800">{{ $branch->name }}</h2>
                                <p class="text-sm text-gray-600 mt-1">{{ $branch->code }}</p>
                            </div>
                            <span
                                class="px-3 py-1 text-xs font-semibold rounded-full {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $branch->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="space-y-2 text-sm text-gray-600">
                            <p><strong>Address:</strong> {{ $branch->address ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $branch->phone ?? 'N/A' }}</p>
                        </div>

                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div>
                                    <p class="text-2xl font-bold text-blue-600">{{ $branch->orders_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Total Orders</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-purple-600">{{ $branch->available_menu_items_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Available Menu</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">{{ $branch->inventories_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Inventory Items</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors">
                                View Branch Operations
                            </button>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection