@extends('layouts.app')

@section('content')
<div class="container-fluid my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-archive"></i> Archived Orders</h2>
        <a href="{{ route('admin.orders.index') }}" class="btn btn-primary">
            <i class="fas fa-arrow-left"></i> Back to Active Orders
        </a>
    </div>

    <!-- @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif -->

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('admin.orders.archived') }}" class="row g-3">
                <div class="col-md-3">
                    <select name="branch_id" class="form-select">
                        <option value="">All Branches</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-light">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Branch</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Order Date</th>
                            <th>Archived Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td>
                                <div>{{ $order->user_name }}</div>
                                <small class="text-muted">{{ $order->user_email }}</small>
                            </td>
                            <td>{{ $order->branch_name }}</td>
                            <td>₱{{ number_format($order->total_amount, 2) }}</td>
                                                        <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'confirmed' => 'info',
                                        'picked up' => 'success',
                                        'cancelled' => 'danger'
                                    ];
                                    $textClass = $statusClass[$order->status] ?? 'secondary';
                                @endphp
                                <span class="text-{{ $textClass }} fw-semibold">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($order->created_at)->format('M j, Y g:i A') }}</td>
                            <td>{{ \Carbon\Carbon::parse($order->deleted_at)->format('M j, Y g:i A') }}</td>
                            <td>
                                <form action="{{ route('admin.orders.restore', $order->id) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('Are you sure you want to restore this order?');"
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
                            <td colspan="8" class="text-center">No archived orders found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
</div>
@endsection