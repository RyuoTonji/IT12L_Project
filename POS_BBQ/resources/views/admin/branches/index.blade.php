@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-8 text-gray-900">
            <h1 class="text-2xl font-semibold mb-8 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 mr-3 text-gray-800" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Branch Management
            </h1>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach($branches as $branch)
                    <a href="{{ route('admin.branches.show', $branch->id) }}"
                        class="block bg-white p-6 rounded-lg shadow-md border border-gray-200 hover:shadow-lg transition-shadow">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 text-blue-600" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    {{ $branch->name }}
                                </h2>
                                <p class="text-sm text-gray-600 mt-1">{{ $branch->code }}</p>
                            </div>
                            <span
                                class="inline-flex text-sm leading-5 font-semibold items-center {{ $branch->is_active ? 'text-green-600' : 'text-red-600' }}">
                                @if($branch->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Active
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Inactive
                                @endif
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
                                    <p class="text-2xl font-bold text-purple-600">{{ $branch->available_menu_items_count ?? 0 }}
                                    </p>
                                    <p class="text-xs text-gray-600">Available Menu</p>
                                </div>
                                <div>
                                    <p class="text-2xl font-bold text-green-600">{{ $branch->inventories_count ?? 0 }}</p>
                                    <p class="text-xs text-gray-600">Inventory Items</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button
                                class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition-colors flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                    <polyline points="14 2 14 8 20 8"></polyline>
                                    <circle cx="11.5" cy="15.5" r="2.5"></circle>
                                    <path d="M16 20l-2-2"></path>
                                </svg>
                                View Branch Operations
                            </button>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
@endsection