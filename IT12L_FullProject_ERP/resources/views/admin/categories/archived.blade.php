@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-archive"></i> Archived Categories</h2>
        <a href="{{ route('admin.categories.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Active Categories
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
                            <th>Name</th>
                            <th>Description</th>
                            <th>Created Date</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                        <tr>
                            <td>#{{ $category->id }}</td>
                            <td><strong>{{ $category->name }}</strong></td>
                            <td>
                                <small class="text-muted">
                                    {{ $category->description ? Str::limit($category->description, 60) : 'N/A' }}
                                </small>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($category->created_at)->format('M j, Y g:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($category->deleted_at)->format('M j, Y g:i A') }}</td>
                            <td>
                                <form action="{{ route('admin.categories.restore', $category->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to restore this category?');"
                                      class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fas fa-undo"></i> Restore
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center">No archived categories found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection