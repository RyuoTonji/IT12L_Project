@extends('layouts.admin')

@section('content')
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 text-gray-900">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold">Category: {{ $category->name }}</h1>
                <div>
                    <a href="{{ route('categories.index') }}"
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600 mr-2 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to Categories
                    </a>
                    <form id="deleteCategoryForm" action="{{ route('categories.destroy', $category) }}" method="POST"
                        class="inline-block mr-2">
                        @csrf
                        @method('DELETE')
                        <button type="button"
                            class="px-4 py-2 bg-gray-600 text-white rounded hover:bg-gray-700 flex items-center inline-flex"
                            onclick="confirmArchiveCategory()">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" stroke-width="2"
                                stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                                <path d="M4 10h16v10a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V10Z"></path>
                                <path d="M6 10V7h12v3"></path>
                                <path d="M8 7V5h8v2"></path>
                                <rect x="9" y="14" width="6" height="2" rx="0.5"></rect>
                                <path d="M4 10l2-4h12l2 4"></path>
                            </svg>
                            Archive
                        </button>
                    </form>
                    <a href="{{ route('categories.edit', $category) }}"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 flex items-center inline-flex">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Edit Category
                    </a>
                </div>
            </div>

            <div class="bg-gray-50 p-4 rounded-lg border mb-6">
                <h2 class="text-lg font-medium mb-2">Category Details</h2>
                <p><strong>ID:</strong> {{ $category->id }}</p>
                <p><strong>Name:</strong> {{ $category->name }}</p>
                <p><strong>Description:</strong> {{ $category->description ?: 'No description' }}</p>
                <p><strong>Created:</strong> {{ $category->created_at->format('M d, Y H:i') }}</p>
                <p><strong>Last Updated:</strong> {{ $category->updated_at->format('M d, Y H:i') }}</p>
            </div>

            <h2 class="text-xl font-medium mb-4">Menu Items in this Category</h2>

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
                                class="py-2 px-4 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price</th>
                            <th
                                class="py-2 px-4 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Available</th>
                            <!-- Actions Header Removed -->
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($category->menuItems as $item)
                            <tr onclick="window.location='{{ route('menu.show', ['menu' => $item, 'source' => 'category']) }}'"
                                class="hover:bg-gray-50 cursor-pointer transition-colors duration-200">
                                <td class="py-2 px-4">{{ $item->id }}</td>
                                <td class="py-2 px-4 font-medium text-gray-900">{{ $item->name }}</td>
                                <td class="py-2 px-4 text-right">₱{{ number_format($item->price, 2) }}</td>
                                <td class="py-2 px-4 text-center">
                                    <span
                                        class="inline-flex text-xs leading-5 font-semibold items-center
                                                                    {{ $item->is_available ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $item->is_available ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-4 px-4 text-center text-gray-500">No menu items in this category</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        function confirmArchiveCategory() {
            AlertModal.showConfirm(
                'Are you sure you want to archive this category?',
                function () {
                    document.getElementById('deleteCategoryForm').submit();
                },
                null,
                'Archive Confirmation'
            );
        }
    </script>
@endsection