@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-store"></i> Manage Branches</h2>
        <a href="{{ route('admin.branches.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Branch
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
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
                            <th>Address</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                        <tr>
                            <td>{{ $branch->id }}</td>
                            <td>{{ $branch->name }}</td>
                            <td>{{ $branch->address }}</td>
                            <td>{{ $branch->phone ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-{{ $branch->is_active ? 'success' : 'danger' }}">
                                    {{ $branch->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.branches.edit', $branch->id) }}" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('admin.branches.destroy', $branch->id) }}" 
                                          method="POST" 
                                          onsubmit="return confirm('Are you sure you want to delete this branch?');"
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
                            <td colspan="6" class="text-center">No branches found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection