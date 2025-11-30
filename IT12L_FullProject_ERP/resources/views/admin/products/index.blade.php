@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box"></i> Manage Products</h2>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Product
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Branch</th>
                            <th>Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" 
                                         alt="{{ $product->name }}" 
                                         style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->category_name }}</td>
                            <td>{{ $product->branch_name }}</td>
                            <td>₱{{ number_format($product->price, 2) }}</td>
                            <td>
                                <span class="badge bg-{{ $product->is_available ? 'success' : 'danger' }}">
                                    {{ $product->is_available ? 'Available' : 'Unavailable' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this product?');"
                                          class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No products found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection