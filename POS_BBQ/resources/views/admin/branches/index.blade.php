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
                                class="px-3 py-1 text-xs font-semibold rounded-full {{ $branch->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} flex items-center">
                                @if($branch->is_active)
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Active
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24"
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
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