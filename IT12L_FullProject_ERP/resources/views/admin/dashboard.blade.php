@extends('layouts.app')

@section('content')
    <!-- Header Section -->
    <div class="page-header mb-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <h1 class="h2 mb-0 text-white"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>
            <div>
                <span class="text-white opacity-75">Welcome, {{ auth()->user()->name }}</span>
            </div>
        </div>
    </div>
    

    <!-- ============================================================================ -->
    <!-- 1. LIVE ANALYTICS & INSIGHTS (MOVED TO TOP) -->
    <!-- ============================================================================ -->
<div class="mb-4">
    <div class="section-header mb-4 d-flex justify-content-between align-items-center">
        <h2 class="h5 mb-0">
            <i class="fas fa-chart-pie"></i> Live Analytics & Insights
        </h2>
        
        <!-- Status Filters - Matching Sales Performance -->
        <div class="status-filters-wrapper">
            <div class="d-flex gap-2 align-items-center flex-wrap">
                <!-- Quick Filter Buttons -->
                <div class="btn-group" role="group">
                    <button type="button" class="quick-status-filter-btn active" data-period="today">Today</button>
                    <button type="button" class="quick-status-filter-btn" data-period="yesterday">Yesterday</button>
                    <button type="button" class="quick-status-filter-btn" data-period="7days">7 Days</button>
                    <button type="button" class="quick-status-filter-btn" data-period="30days">30 Days</button>
                </div>
                
                <!-- Branch Filter -->
                <select id="statusBranchFilter" class="form-select form-select-sm status-filter-select">
                    <option value="all">All Branches</option>
                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Orders by Status - Donut Chart -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-pie"></i> Orders by Status
                    </h5>
                    <small class="text-muted" id="statusPeriodLabel">Today Only</small>
                </div>
                <div class="chart-card-body">
                    <div id="ordersStatusChart"></div>
                </div>
            </div>
        </div>

        <!-- Live Order Count -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-sync-alt"></i> Live Order Activity
                    </h5>
                    <small class="text-muted" id="livePeriodLabel">Today Only</small>
                </div>
                <div class="chart-card-body text-center">
                    <div class="live-order-count">
                        <div class="live-indicator">
                            <span class="pulse"></span>
                            <span class="text">LIVE</span>
                        </div>
                        <h2 class="count-value">{{ $recentOrderCount }}</h2>
                        <p class="count-label">Orders</p>
                        <div class="trend-indicator {{ $orderTrend >= 0 ? 'trend-up' : 'trend-down' }}">
                            <i class="fas fa-{{ $orderTrend >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                            {{ number_format(abs($orderTrend), 1) }}%
                            <span class="trend-text">vs previous period</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats Summary -->
        <div class="col-lg-4">
            <div class="chart-card">
                <div class="chart-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-bolt"></i> Quick Insights
                    </h5>
                    <small class="text-muted" id="insightsPeriodLabel">Today Only</small>
                </div>
                <div class="chart-card-body">
                    <div class="quick-stats-grid">
                        <div class="quick-stat-item-compact">
                            <div class="stat-icon-circle-compact pending">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info-compact">
                                <h4>{{ $orderStatusData['pending'] ?? 0 }}</h4>
                                <p>Pending</p>
                            </div>
                        </div>
                        <div class="quick-stat-item-compact">
                            <div class="stat-icon-circle-compact confirmed">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stat-info-compact">
                                <h4>{{ $orderStatusData['confirmed'] ?? 0 }}</h4>
                                <p>Confirmed</p>
                            </div>
                        </div>
                        <div class="quick-stat-item-compact">
                            <div class="stat-icon-circle-compact preparing">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <div class="stat-info-compact">
                                <h4>{{ $orderStatusData['preparing'] ?? 0 }}</h4>
                                <p>Preparing</p>
                            </div>
                        </div>
                        <div class="quick-stat-item-compact">
                            <div class="stat-icon-circle-compact ready">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="stat-info-compact">
                                <h4>{{ $orderStatusData['ready'] ?? 0 }}</h4>
                                <p>Ready</p>
                            </div>
                        </div>
                        <div class="quick-stat-item-compact">
                            <div class="stat-icon-circle-compact pickedup">
                                <i class="fas fa-truck"></i>
                            </div>
                            <div class="stat-info-compact">
                                <h4>{{ $orderStatusData['picked up'] ?? 0 }}</h4>
                                <p>Picked Up</p>
                            </div>
                        </div>
                        <div class="quick-stat-item-compact">
                            <div class="stat-icon-circle-compact cancelled">
                                <i class="fas fa-times-circle"></i>
                            </div>
                            <div class="stat-info-compact">
                                <h4>{{ $orderStatusData['cancelled'] ?? 0 }}</h4>
                                <p>Cancelled</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="col-12">
    <div class="chart-card">
        <div class="chart-card-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> Sales Performance
                    </h5>
                    <small>Monthly Revenue Trends</small>
                </div>
                
                <!-- Sales Filters - Single Row -->
                <div class="sales-filters-wrapper">
                    <div class="d-flex gap-2 align-items-center flex-wrap">
                        <!-- Quick Filter Buttons -->
                        <div class="btn-group" role="group">
                            <button type="button" class="quick-sales-filter-btn" data-days="30">30 Days</button>
                            <button type="button" class="quick-sales-filter-btn active" data-days="180">4 Months</button>
                            <button type="button" class="quick-sales-filter-btn" data-days="365">1 Year</button>
                        </div>
                        
                        <!-- Year Filter -->
                        <select id="salesYearFilter" class="form-select form-select-sm sales-filter-select">
                            <option value="all">All Years</option>
                            @php
                                $currentYear = date('Y');
                                $startYear = 2020;
                            @endphp
                            @for($year = $currentYear; $year >= $startYear; $year--)
                                <option value="{{ $year }}" {{ $year == $currentYear ? 'selected' : '' }}>{{ $year }}</option>
                            @endfor
                        </select>
                        
                        <!-- Branch Filter -->
                        <select id="salesBranchFilter" class="form-select form-select-sm sales-filter-select">
                            <option value="all">All Branches</option>
                            @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="chart-card-body">
            <div id="salesPerformanceChart"></div>
        </div>
    </div>
</div>
        </div>
    </div>

    <!-- ============================================================================ -->
    <!-- 2. OVERALL PERFORMANCE -->
    <!-- ============================================================================ -->
    <div class="mb-4">
        <div class="section-header mb-4">
            <h2 class="h5 mb-0">
                <i class="fas fa-chart-bar"></i> Overall Performance
            </h2>
        </div>
        
        <div class="row g-4 mb-4">
            <!-- Total Sales (All Time) -->
            <div class="col-md-6">
                <div class="stat-card stat-card-large">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <h6 class="stat-subtitle">Total Sales (All Time)</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="stat-icon-large">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <h2 class="stat-value">₱{{ number_format($totalRevenueAllTime ?? 0, 2) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Orders (All Time) -->
            <div class="col-md-6">
                <div class="stat-card stat-card-large">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="w-100">
                                <h6 class="stat-subtitle">Total Orders (All Time)</h6>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="stat-icon-large">
                                        <i class="fas fa-shopping-cart"></i>
                                    </div>
                                    <h2 class="stat-value">{{ number_format($totalOrdersAllTime ?? 0) }}</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Statistics -->
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-today-sales">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Today's Sales</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-peso-sign"></i>
                            </div>
                            <h3 class="stat-value-small">₱{{ number_format($todayRevenue ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-today-orders">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Today's Orders</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h3 class="stat-value-small">{{ number_format($todayOrders ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-pending">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Pending Orders</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-clock"></i>
                            </div>
                            <h3 class="stat-value-small">{{ number_format($pendingOrders ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="stat-card stat-card-small stat-card-products">
                    <div class="card-body">
                        <h6 class="stat-subtitle-small">Total Products</h6>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="stat-icon-small">
                                <i class="fas fa-utensils"></i>
                            </div>
                            <h3 class="stat-value-small">{{ number_format($totalProducts ?? 0) }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- ============================================================================ -->
    <!-- 4. SALES BY BRANCH & TOP SELLING PRODUCTS -->
    <!-- ============================================================================ -->
    <div class="row g-4 mb-4">
        <!-- Sales by Branch -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="data-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-store"></i> Confirmed Sales by Branch
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table table">
                            <thead>
                                <tr>
                                    <th>Branch</th>
                                    <th class="text">Orders</th>
                                    <th class="text">Total Sales</th>
                                </tr>
                            </thead>
                            <tbody data-table="sales-by-branch">
                                @forelse($salesByBranch as $branch)
                                <tr>
                                    <td class="fw-medium">{{ $branch->branch_name }}</td>
                                    <td class="text">{{ number_format($branch->total_orders) }}</td>
                                    <td class="text fw-semibold text-dark">₱{{ number_format($branch->total_sales, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state-small">
                                            <i class="fas fa-inbox"></i>
                                            <p class="mb-0">No sales data available</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Selling Products -->
        <div class="col-lg-6">
            <div class="data-card">
                <div class="data-card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-trophy"></i> Top Selling Products
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="data-table table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text">Quantity</th>
                                    <th class="text">Sales</th>
                                </tr>
                            </thead>
                            <tbody data-table="top-products">
                                @forelse($topProducts as $product)
                                <tr>
                                    <td class="fw-medium">{{ $product->name }}</td>
                                    <td class="text">{{ number_format($product->total_quantity) }}</td>
                                    <td class="text fw-semibold text-dark">₱{{ number_format($product->total_sales, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3">
                                        <div class="empty-state-small">
                                            <i class="fas fa-inbox"></i>
                                            <p class="mb-0">No product data available</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders Section -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="data-card">
                <div class="data-card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-sync"></i> Real-Time Transactions
                        </h5>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-light">
                            View All <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($recentOrders->isEmpty())
                        <div class="empty-state">
                            <i class="fas fa-inbox"></i>
                            <h4>No Recent Orders</h4>
                            <p>There are no recent orders to display</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="data-table table">
                                <thead>
                                    <tr>
                                        <th>Order ID</th>
                                        <th>Customer</th>
                                        <th>Branch</th>
                                        <th class="text-end">Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                        <tr>
                                            <td>
                                                <span class="order-id">#{{ $order->id }}</span>
                                            </td>
                                            <td>{{ $order->user_name }}</td>
                                            <td>
                                                <span class="branch-badge">
                                                    <i class="fas fa-store"></i> {{ $order->branch_name }}
                                                </span>
                                            </td>
                                            <td class="text-end">
                                                <span class="amount-text">₱{{ number_format($order->total_amount, 2) }}</span>
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'pending' => 'warning',
                                                        'confirmed' => 'info',
                                                        'preparing' => 'primary',
                                                        'ready' => 'teal',
                                                        'picked up' => 'success',
                                                        'cancelled' => 'danger'
                                                    ];
                                                    $badgeClass = $statusClass[$order->status] ?? 'secondary';
                                                @endphp
                                                <span class="status-badge status-{{ $badgeClass }}">
                                                    {{ ucfirst($order->status) }}
                                                </span>
                                            </td>
                                            <td class="text-muted">{{ \Carbon\Carbon::parse($order->ordered_at)->diffForHumans() }}</td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.5rem 2rem;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* ============================================================================ */
    /* UNIFIED FILTER STYLES - IDENTICAL FOR BOTH SALES AND STATUS */
    /* ============================================================================ */

    /* Common wrapper styles */
    .sales-filters-wrapper,
    .status-filters-wrapper {
        display: flex;
    }

    .sales-filters-wrapper .d-flex,
    .status-filters-wrapper .d-flex {
        display: flex;
        align-items: center;
        gap: 0.625rem;
        flex-wrap: wrap;
    }

    /* ============================================================================ */
    /* BUTTON GROUP - IDENTICAL */
    /* ============================================================================ */

    .sales-filters-wrapper .btn-group,
    .status-filters-wrapper .btn-group {
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
        background: white;
    }

    .quick-sales-filter-btn,
    .quick-status-filter-btn {
        padding: 0.625rem 1.25rem;
        border: 1px solid #dee2e6 !important;
        background: white;
        color: #495057;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        white-space: nowrap;
        min-width: 100px;
    }

    .quick-sales-filter-btn:hover,
    .quick-status-filter-btn:hover {
        background: #f8f9fa;
        color: #A52A2A;
        border-color: #A52A2A !important;
    }

    .quick-sales-filter-btn.active,
    .quick-status-filter-btn.active {
        background: #A52A2A !important;
        color: white !important;
        border-color: #A52A2A !important;
        box-shadow: 0 2px 8px rgba(165, 42, 42, 0.3);
    }

    /* ============================================================================ */
    /* SELECT DROPDOWNS - IDENTICAL */
    /* ============================================================================ */

    .sales-filter-select,
    .status-filter-select {
        height: 42px;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        font-size: 0.9rem;
        padding: 0.625rem 2.5rem 0.625rem 1rem;
        transition: all 0.3s ease;
        background: white;
        font-weight: 600;
        color: #495057;
        cursor: pointer;
        min-width: 140px;
    }

    /* Specific widths */
    #salesYearFilter {
        width: 140px;
    }

    #salesBranchFilter,
    #statusBranchFilter {
        width: 180px;
    }

    /* Date inputs specific styling */
    .status-filter-select[type="date"] {
        min-width: 160px;
        padding: 0.625rem 1rem;
    }

    /* Hover states */
    .sales-filter-select:hover,
    .status-filter-select:hover {
        border-color: #adb5bd;
        background-color: #f8f9fa;
    }

    /* Focus states */
    .sales-filter-select:focus,
    .status-filter-select:focus {
        border-color: #A52A2A;
        box-shadow: 0 0 0 0.2rem rgba(165, 42, 42, 0.15);
        outline: none;
        background-color: white;
    }

    /* Apply Button for Status Filters */
    .status-apply-btn {
        height: 42px;
        padding: 0.625rem 1.5rem;
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        color: white !important;
        transition: all 0.3s ease;
        white-space: nowrap;
    }

    .status-apply-btn:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
    }

    /* ============================================================================ */
    /* RESPONSIVE DESIGN - UNIFIED */
    /* ============================================================================ */

    @media (max-width: 1400px) {
        .quick-sales-filter-btn,
        .quick-status-filter-btn {
            padding: 0.5rem 1rem;
            font-size: 0.85rem;
            min-width: 90px;
        }
        
        .sales-filter-select,
        .status-filter-select {
            min-width: 130px;
            font-size: 0.85rem;
        }

        .status-filter-select[type="date"] {
            min-width: 140px;
        }
    }

    @media (max-width: 1200px) {
        .sales-filters-wrapper .d-flex,
        .status-filters-wrapper .d-flex {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 992px) {
        .section-header {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .sales-filters-wrapper,
        .status-filters-wrapper {
            width: 100%;
        }
        
        .sales-filters-wrapper .d-flex,
        .status-filters-wrapper .d-flex {
            width: 100%;
            justify-content: center;
        }
        
        .quick-sales-filter-btn,
        .quick-status-filter-btn {
            padding: 0.5rem 0.875rem;
            font-size: 0.8rem;
            min-width: 80px;
        }
        
        .sales-filter-select,
        .status-filter-select {
            min-width: 120px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 768px) {
        .sales-filters-wrapper .d-flex,
        .status-filters-wrapper .d-flex {
            flex-direction: column;
            gap: 0.75rem;
            width: 100%;
        }
        
        .sales-filters-wrapper .btn-group,
        .status-filters-wrapper .btn-group {
            width: 100%;
        }
        
        .quick-sales-filter-btn,
        .quick-status-filter-btn {
            flex: 1;
            font-size: 0.8rem;
            padding: 0.5rem 0.75rem;
            min-width: auto;
        }
        
        .sales-filter-select,
        .status-filter-select {
            width: 100%;
            min-width: 100%;
        }

        .status-apply-btn {
            width: 100%;
        }

        .status-filters-wrapper .btn-group {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .status-filters-wrapper .btn-group {
            grid-template-columns: 1fr;
        }
    }

    /* ============================================================================ */
    /* SECTION HEADER */
    /* ============================================================================ */

    .section-header {
        background: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-left: 4px solid #A52A2A;
    }

    .section-header h2 {
        color: #A52A2A;
        font-weight: 600;
    }

    /* Period Labels */
    .chart-card-header small {
        display: block;
        color: #6c757d;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 0.25rem;
    }

    /* Filters Card */
    .filters-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .filters-card h5 {
        color: #A52A2A;
        font-weight: 600;
        margin-bottom: 0.875rem;
        font-size: 1rem;
    }

    .filter-group {
        display: flex;
        gap: 0.875rem;
        flex-wrap: wrap;
        align-items: end;
    }

    .filter-group .form-group {
        flex: 1;
        min-width: 150px;
    }

    .filter-group label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 0.4rem;
        display: block;
        font-size: 0.875rem;
    }

    .filter-group .form-control {
        border: 2px solid #dee2e6;
        border-radius: 8px;
        padding: 0.5rem 0.875rem;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .filter-group .form-control:focus {
        border-color: #A52A2A;
        box-shadow: 0 0 0 0.2rem rgba(165, 42, 42, 0.15);
    }

    .filter-group .btn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }

    /* Stat Cards */
    .stat-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border-left: 4px solid #A52A2A;
    }

    .stat-card-large {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        border-left: none;
    }

    .stat-card-large .card-body {
        padding: 2rem;
    }

    .stat-card-large .stat-subtitle {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.875rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-card-large .stat-value {
        color: white;
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .stat-card-large .stat-icon-large {
        font-size: 3.5rem;
        color: rgba(255, 255, 255, 0.3);
    }

    /* Small Stat Cards */
    .stat-card-small {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .stat-card-small .card-body {
        padding: 1.5rem;
    }

    .stat-card-small .stat-subtitle-small {
        color: #6c757d;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .stat-card-small .stat-value-small {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-card-small .stat-icon-small {
        font-size: 2.5rem;
    }

    /* Today's Sales - Green */
    .stat-card-today-sales {
        border-left: 4px solid #28A745;
    }

    .stat-card-today-sales .stat-value-small {
        color: #28A745;
    }

    .stat-card-today-sales .stat-icon-small {
        color: rgba(40, 167, 69, 0.2);
    }

    /* Today's Orders - Blue */
    .stat-card-today-orders {
        border-left: 4px solid #007BFF;
    }

    .stat-card-today-orders .stat-value-small {
        color: #007BFF;
    }

    .stat-card-today-orders .stat-icon-small {
        color: rgba(0, 123, 255, 0.2);
    }

    /* Pending Orders - Orange */
    .stat-card-pending {
        border-left: 4px solid #FD7E14;
    }

    .stat-card-pending .stat-value-small {
        color: #FD7E14;
    }

    .stat-card-pending .stat-icon-small {
        color: rgba(253, 126, 20, 0.2);
    }

    /* Total Products - Purple */
    .stat-card-products {
        border-left: 4px solid #6610F2;
    }

    .stat-card-products .stat-value-small {
        color: #6610F2;
    }

    .stat-card-products .stat-icon-small {
        color: rgba(102, 16, 242, 0.2);
    }

    /* ============================================================================ */
    /* IMPROVED CHART STYLES */
    /* ============================================================================ */

    /* Chart Cards */
    .chart-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        height: 100%;
        transition: all 0.3s ease;
    }

    .chart-card:hover {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .chart-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        padding: 1.25rem 1.5rem;
        border-bottom: 2px solid #dee2e6;
    }

    .chart-card-header h5 {
        color: #A52A2A;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }

    .chart-card-body {
        padding: 1.5rem;
        min-height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Sales Performance Chart - Make it larger */
    .chart-card-body #salesPerformanceChart {
        width: 100%;
        min-height: 400px;
    }

    /* Status Chart */
    .chart-card-body #ordersStatusChart {
        width: 100%;
        min-height: 350px;
    }

    /* Live Order Count Styles */
    .live-order-count {
        padding: 2rem 1rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 300px;
    }

    .live-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: rgba(40, 167, 69, 0.1);
        padding: 0.5rem 1rem;
        border-radius: 20px;
        margin-bottom: 1.5rem;
    }

    .live-indicator .pulse {
        width: 10px;
        height: 10px;
        background: #28A745;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
            transform: scale(1);
            box-shadow: 0 0 0 0 rgba(40, 167, 69, 0.7);
        }
        50% {
            opacity: 0.7;
            transform: scale(1.1);
            box-shadow: 0 0 0 10px rgba(40, 167, 69, 0);
        }
    }

    .live-indicator .text {
        color: #28A745;
        font-weight: 700;
        font-size: 0.75rem;
        letter-spacing: 1px;
    }

    .count-value {
        font-size: 4rem;
        font-weight: 700;
        color: #A52A2A;
        margin: 0;
        line-height: 1;
        transition: transform 0.3s ease;
    }

    .count-label {
        color: #6c757d;
        font-size: 1rem;
        margin-top: 0.5rem;
        margin-bottom: 1.5rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .trend-indicator {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.3s ease;
    }

    .trend-up {
        background: rgba(40, 167, 69, 0.1);
        color: #28A745;
    }

    .trend-down {
        background: rgba(220, 53, 69, 0.1);
        color: #dc3545;
    }

    .trend-text {
        font-size: 0.8rem;
        font-weight: 500;
        opacity: 0.8;
        margin-left: 0.5rem;
    }

    /* Quick Stats Grid - 3 columns, 2 rows */
    .quick-stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.75rem;
        padding: 0.5rem 0;
    }

    .quick-stat-item-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        padding: 0.75rem 0.5rem;
        border-radius: 8px;
        background: #f8f9fa;
        transition: all 0.3s ease;
        text-align: center;
    }

    .quick-stat-item-compact:hover {
        background: #e9ecef;
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-icon-circle-compact {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
        margin-bottom: 0.25rem;
    }

    .stat-icon-circle-compact.pending {
        background: rgba(253, 126, 20, 0.15);
        color: #FD7E14;
    }

    .stat-icon-circle-compact.confirmed {
        background: rgba(13, 202, 240, 0.15);
        color: #0dcaf0;
    }

    .stat-icon-circle-compact.preparing {
        background: rgba(255, 193, 7, 0.15);
        color: #ffc107;
    }

    .stat-icon-circle-compact.ready {
        background: rgba(32, 201, 151, 0.15);
        color: #20c997;
    }

    .stat-icon-circle-compact.pickedup {
        background: rgba(25, 135, 84, 0.15);
        color: #198754;
    }

    .stat-icon-circle-compact.cancelled {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }

    .stat-info-compact {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.125rem;
    }

    .stat-info-compact h4 {
        margin: 0;
        font-size: 1.5rem;
        font-weight: 700;
        color: #212529;
        line-height: 1;
    }

    .stat-info-compact p {
        margin: 0;
        font-size: 0.7rem;
        color: #6c757d;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    /* Responsive - Mobile: 2 columns */
    @media (max-width: 768px) {
        .quick-stats-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }
        
        .stat-icon-circle-compact {
            width: 35px;
            height: 35px;
            font-size: 1rem;
        }
        
        .stat-info-compact h4 {
            font-size: 1.25rem;
        }
        
        .stat-info-compact p {
            font-size: 0.65rem;
        }
    }

    /* Data Cards */
    .data-card {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        height: 100%;
    }

    .data-card-header {
        background: linear-gradient(135deg, #A52A2A 0%, #8B0000 100%);
        color: white;
        padding: 1.25rem 1.5rem;
    }

    .data-card-header h5 {
        color: white;
        font-weight: 600;
    }

    .data-card-header .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .data-card-header .btn-light:hover {
        background: white;
        transform: translateY(-2px);
    }

    .data-card .card-body {
        padding: 0;
    }

    /* Data Table */
    .data-table {
        margin-bottom: 0;
    }

    .data-table thead {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table thead th {
        font-weight: 600;
        color: #495057;
        padding: 1rem;
        border: none;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        text-align: center;
    }

    .data-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #dee2e6;
        text-align: center;
    }

    .data-table tbody tr:hover {
        background-color: rgba(165, 42, 42, 0.05);
    }

    .data-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    /* Order ID */
    .order-id {
        font-weight: 700;
        color: #A52A2A;
        font-size: 0.95rem;
    }

    /* Branch Badge */
    .branch-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 500;
        color: #d9480f;
    }

    /* Amount Text */
    .amount-text {
        font-weight: 700;
        color: #000000ff;
        font-size: 1rem;
    }

    /* Status Badges */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
        text-transform: capitalize;
    }

    .status-warning {
        color: #ffc107;
    }

    .status-info {
        color: #0dcaf0;
    }

    .status-primary {
        color: #0d6efd;
    }

    .status-teal {
        color: #20c997;
    }

    .status-success {
        color: #198754;
    }

    .status-danger {
        color: #dc3545;
    }

    .status-secondary {
        color: #6c757d;
    }

    /* Action Buttons */
    .data-table .btn-primary,
    .btn-primary.btn-sm {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        font-weight: 600;
        transition: all 0.3s ease;
        color: white !important;
    }

    .data-table .btn-primary:hover,
    .btn-primary.btn-sm:hover {
        background: linear-gradient(135deg, #0a58ca 0%, #084298 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
        color: white !important;
    }

    /* Empty States */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #dee2e6;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #6c757d;
        margin-bottom: 0;
    }

    .empty-state-small {
        text-align: center;
        padding: 2rem 1rem;
    }

    .empty-state-small i {
        font-size: 2rem;
        color: #dee2e6;
        margin-bottom: 0.5rem;
        display: block;
    }

    .empty-state-small p {
        color: #6c757d;
        font-size: 0.875rem;
    }

    /* ApexCharts customization */
    .apexcharts-legend {
        padding: 10px 0 !important;
    }

    .apexcharts-tooltip {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
        border-radius: 8px !important;
    }

    /* Ensure charts take full width */
    .apexcharts-canvas {
        margin: 0 auto;
    }

    /* Loading Spinner */
    .spinner-border {
        width: 3rem;
        height: 3rem;
        border: 0.25em solid currentColor;
        border-right-color: transparent;
        border-radius: 50%;
        animation: spinner-border 0.75s linear infinite;
    }

    @keyframes spinner-border {
        to { transform: rotate(360deg); }
    }

    .visually-hidden {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Responsive Design */
    @media (max-width: 1200px) {
        .chart-card-body #salesPerformanceChart {
            min-height: 350px;
        }
        
        .count-value {
            font-size: 3rem;
        }
        
        .stat-card-large .stat-value {
            font-size: 2rem;
        }

        .stat-icon-large {
            font-size: 2.5rem !important;
        }
    }

    @media (max-width: 992px) {
        .page-header {
            padding: 1.5rem;
        }

        .chart-card-body {
            min-height: 250px;
            padding: 1rem;
        }
        
        .chart-card-body #ordersStatusChart {
            min-height: 300px;
        }
        
        .live-order-count {
            padding: 1.5rem 1rem;
            min-height: 250px;
        }
        
        .count-value {
            font-size: 2.5rem;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1rem;
        }

        .filter-group {
            flex-direction: column;
        }

        .filter-group .form-group {
            width: 100%;
        }

        .data-card-header .d-flex {
            flex-direction: column;
            gap: 1rem;
        }

        .data-card-header .btn {
            width: 100%;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .data-table {
            min-width: 800px;
        }
        
        .chart-card-header {
            padding: 1rem;

/* ============================================================================ */
/* STATUS & INSIGHTS FILTERS - MATCHING SALES PERFORMANCE UI */
/* ============================================================================ */

.status-filters-wrapper {
    display: flex;
}

.status-filters-wrapper .d-flex {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    flex-wrap: wrap;
}

/* Button Group */
.status-filters-wrapper .btn-group {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
    background: white;
}

.quick-status-filter-btn {
    padding: 0.625rem 1.25rem;
    border: 1px solid #dee2e6 !important;
    background: white;
    color: #495057;
    font-weight: 600;
    font-size: 0.9rem;
    transition: all 0.3s ease;
    white-space: nowrap;
    min-width: 100px;
}

.quick-status-filter-btn:hover {
    background: #f8f9fa;
    color: #A52A2A;
    border-color: #A52A2A !important;
}

.quick-status-filter-btn.active {
    background: #A52A2A !important;
    color: white !important;
    border-color: #A52A2A !important;
    box-shadow: 0 2px 8px rgba(165, 42, 42, 0.3);
}

/* Date Inputs */
.status-filter-select[type="date"] {
    height: 42px;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    font-size: 0.9rem;
    padding: 0.625rem 1rem;
    transition: all 0.3s ease;
    background: white;
    font-weight: 600;
    color: #495057;
    cursor: pointer;
    min-width: 160px;
}

.status-filter-select[type="date"]:hover {
    border-color: #adb5bd;
    background-color: #f8f9fa;
}

.status-filter-select[type="date"]:focus {
    border-color: #A52A2A;
    box-shadow: 0 0 0 0.2rem rgba(165, 42, 42, 0.15);
    outline: none;
    background-color: white;
}

/* Apply Button */
.status-apply-btn {
    height: 42px;
    padding: 0.625rem 1.5rem;
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
    border: none !important;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.9rem;
    color: white !important;
    transition: all 0.3s ease;
    white-space: nowrap;
}

.status-apply-btn:hover {
    background: linear-gradient(135deg, #0a58ca 0%, #084298 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.4);
}

/* Period Labels in Chart Headers */
.chart-card-header small {
    display: block;
    color: #6c757d;
    font-size: 0.75rem;
    font-weight: 500;
    margin-top: 0.25rem;
}

/* Section Header with Filters */
.section-header {
    background: white;
    padding: 1rem 1.5rem;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border-left: 4px solid #A52A2A;
}

.section-header h2 {
    color: #A52A2A;
    font-weight: 600;
}

/* Responsive Design for Status Filters */
@media (max-width: 1400px) {
    .quick-status-filter-btn {
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        min-width: 90px;
    }
    
    .status-filter-select[type="date"] {
        min-width: 140px;
        font-size: 0.85rem;
    }
}

@media (max-width: 1200px) {
    .status-filters-wrapper .d-flex {
        width: 100%;
        justify-content: flex-end;
    }
}

@media (max-width: 992px) {
    .section-header {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
    }
    
    .status-filters-wrapper {
        width: 100%;
    }
    
    .status-filters-wrapper .d-flex {
        width: 100%;
        justify-content: center;
    }
    
    .quick-status-filter-btn {
        padding: 0.5rem 0.875rem;
        font-size: 0.8rem;
        min-width: 80px;
    }
}

@media (max-width: 768px) {
    .status-filters-wrapper .d-flex {
        flex-direction: column;
        gap: 0.75rem;
        width: 100%;
    }
    
    .status-filters-wrapper .btn-group {
        width: 100%;
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }
    
    .quick-status-filter-btn {
        flex: 1;
        font-size: 0.75rem;
        padding: 0.5rem 0.75rem;
        min-width: auto;
    }
    
    .status-filter-select[type="date"] {
        width: 100%;
        min-width: 100%;
    }
    
    .status-apply-btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .status-filters-wrapper .btn-group {
        grid-template-columns: 1fr;
    }
    
    .section-header h2 {
        font-size: 1rem;
    }
}

/* Ensure consistent styling across both filter sections */
.sales-filters-wrapper,
.status-filters-wrapper {
    display: flex;
}

.sales-filters-wrapper .d-flex,
.status-filters-wrapper .d-flex {
    display: flex;
    align-items: center;
    gap: 0.625rem;
    flex-wrap: wrap;
}
    }
</style>
@endpush

@push('scripts')
<!-- Load ApexCharts from CDN -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.1/dist/apexcharts.min.js"></script>

<script>
// ========================================================================
// DATA & CHART REFERENCES
// ========================================================================
const orderStatusData = {!! json_encode($orderStatusData) !!};
const salesPerformance = {!! json_encode($salesPerformance) !!};
const branches = {!! json_encode($branches) !!};

let salesChart = null;
let statusChart = null;

// Separate filter states
const salesFilterState = {
    startDate: "{{ $salesStartDate ?? '' }}",
    endDate: "{{ $salesEndDate ?? '' }}",
    branchId: 'all'
};

const statusFilterState = {
    startDate: "{{ $statusStartDate ?? Carbon\Carbon::today()->format('Y-m-d') }}",
    endDate: "{{ $statusEndDate ?? Carbon\Carbon::today()->format('Y-m-d') }}",
    currentPeriod: 'today',
    branchId: 'all'  // Added branch filter
};

// ========================================================================
// SALES PERFORMANCE FILTERS
// ========================================================================
function initSalesFilters() {
    // Quick filter buttons
    document.querySelectorAll('.quick-sales-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const days = parseInt(this.getAttribute('data-days'));
            
            document.querySelectorAll('.quick-sales-filter-btn').forEach(b => 
                b.classList.remove('active'));
            this.classList.add('active');
            
            const end = new Date();
            const start = new Date();
            start.setDate(start.getDate() - days);
            
            salesFilterState.startDate = start.toISOString().split('T')[0];
            salesFilterState.endDate = end.toISOString().split('T')[0];
            
            fetchSalesData();
        });
    });
    
    // Year filter
    document.getElementById('salesYearFilter')?.addEventListener('change', function() {
        const year = this.value;
        if (year !== 'all') {
            salesFilterState.startDate = `${year}-01-01`;
            salesFilterState.endDate = `${year}-12-31`;
        } else {
            salesFilterState.startDate = '';
            salesFilterState.endDate = '';
        }
        document.querySelectorAll('.quick-sales-filter-btn').forEach(b => 
            b.classList.remove('active'));
        fetchSalesData();
    });
    
    // Branch filter
    document.getElementById('salesBranchFilter')?.addEventListener('change', function() {
        salesFilterState.branchId = this.value;
        fetchSalesData();
    });
}

function fetchSalesData() {
    const params = new URLSearchParams({
        chart_type: 'sales_performance',
        start_date: salesFilterState.startDate,
        end_date: salesFilterState.endDate,
        branch_id: salesFilterState.branchId
    });
    
    fetch(window.location.pathname + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        updateSalesChart(data.salesPerformance);
        console.log('✅ Sales data updated');
    })
    .catch(error => console.error('Sales fetch error:', error));
}

// ========================================================================
// STATUS & INSIGHTS FILTERS (WITH BRANCH FILTER)
// ========================================================================
function initStatusFilters() {
    // Quick filter buttons - Auto filter on click
    document.querySelectorAll('.quick-status-filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const period = this.getAttribute('data-period');
            
            document.querySelectorAll('.quick-status-filter-btn').forEach(b => 
                b.classList.remove('active'));
            this.classList.add('active');
            
            const end = new Date();
            const start = new Date();
            
            let periodLabel = '';
            
            switch(period) {
                case 'today':
                    start.setHours(0, 0, 0, 0);
                    end.setHours(23, 59, 59, 999);
                    periodLabel = 'Today Only';
                    break;
                case 'yesterday':
                    start.setDate(start.getDate() - 1);
                    start.setHours(0, 0, 0, 0);
                    end.setDate(end.getDate() - 1);
                    end.setHours(23, 59, 59, 999);
                    periodLabel = 'Yesterday';
                    break;
                case '7days':
                    start.setDate(start.getDate() - 7);
                    periodLabel = 'Last 7 Days';
                    break;
                case '30days':
                    start.setDate(start.getDate() - 30);
                    periodLabel = 'Last 30 Days';
                    break;
            }
            
            statusFilterState.startDate = start.toISOString().split('T')[0];
            statusFilterState.endDate = end.toISOString().split('T')[0];
            statusFilterState.currentPeriod = period;
            
            updatePeriodLabels(periodLabel);
            fetchStatusData();
        });
    });
    
    // Branch filter - Auto filter on change
    document.getElementById('statusBranchFilter')?.addEventListener('change', function() {
        statusFilterState.branchId = this.value;
        fetchStatusData();
    });
}

function updatePeriodLabels(label) {
    document.getElementById('statusPeriodLabel').textContent = label;
    document.getElementById('livePeriodLabel').textContent = label;
    document.getElementById('insightsPeriodLabel').textContent = label;
}

function fetchStatusData() {
    const params = new URLSearchParams({
        chart_type: 'order_status',
        start_date: statusFilterState.startDate,
        end_date: statusFilterState.endDate,
        branch_id: statusFilterState.branchId
    });
    
    fetch(window.location.pathname + '?' + params.toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(response => response.json())
    .then(data => {
        updateStatusChart(data.orderStatusData);
        updateQuickInsights(data.orderStatusData);
        updateLiveMetrics(data);
        updateTodayMetrics(data);
        console.log('✅ Status data updated');
    })
    .catch(error => console.error('Status fetch error:', error));
}

// ========================================================================
// CHART UPDATE FUNCTIONS
// ========================================================================
function updateSalesChart(salesData) {
    if (!salesChart || !salesData) return;
    
    const months = salesData.map(item => {
        const date = new Date(item.month + '-01');
        return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
    });
    
    const salesValues = salesData.map(item => parseFloat(item.total_sales || 0));
    const orderCounts = salesData.map(item => parseInt(item.order_count || 0));
    
    salesChart.updateOptions({ labels: months });
    salesChart.updateSeries([
        { name: 'Revenue', data: salesValues },
        { name: 'Orders', data: orderCounts }
    ]);
}

function updateStatusChart(statusData) {
    if (!statusChart) return;
    
    const series = [
        statusData.pending || 0,
        statusData.confirmed || 0,
        statusData.preparing || 0,
        statusData.ready || 0,
        statusData['picked up'] || 0,
        statusData.cancelled || 0
    ];
    
    statusChart.updateSeries(series);
}

function updateQuickInsights(statusData) {
    document.querySelector('.quick-stat-item-compact:nth-child(1) h4').textContent = statusData.pending || 0;
    document.querySelector('.quick-stat-item-compact:nth-child(2) h4').textContent = statusData.confirmed || 0;
    document.querySelector('.quick-stat-item-compact:nth-child(3) h4').textContent = statusData.preparing || 0;
    document.querySelector('.quick-stat-item-compact:nth-child(4) h4').textContent = statusData.ready || 0;
    document.querySelector('.quick-stat-item-compact:nth-child(5) h4').textContent = statusData['picked up'] || 0;
    document.querySelector('.quick-stat-item-compact:nth-child(6) h4').textContent = statusData.cancelled || 0;
}

function updateLiveMetrics(data) {
    const countElement = document.querySelector('.count-value');
    if (countElement) countElement.textContent = data.recentOrderCount;
    
    const trendElement = document.querySelector('.trend-indicator');
    if (trendElement) {
        const trend = data.orderTrend;
        trendElement.className = `trend-indicator ${trend >= 0 ? 'trend-up' : 'trend-down'}`;
        trendElement.innerHTML = `
            <i class="fas fa-${trend >= 0 ? 'arrow-up' : 'arrow-down'}"></i>
            ${Math.abs(trend).toFixed(1)}%
            <span class="trend-text">vs previous period</span>
        `;
    }
}

function updateTodayMetrics(data) {
    const todayOrdersElement = document.querySelector('.stat-card-today-orders .stat-value-small');
    if (todayOrdersElement) todayOrdersElement.textContent = data.todayOrders;
    
    const todayRevenueElement = document.querySelector('.stat-card-today-sales .stat-value-small');
    if (todayRevenueElement) {
        todayRevenueElement.textContent = '₱' + parseFloat(data.todayRevenue).toLocaleString('en-PH', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }
}

// ========================================================================
// LIVE REFRESH (Every 30 seconds)
// ========================================================================
function startLiveRefresh() {
    setInterval(function() {
        const params = new URLSearchParams({
            live_count: '1',
            status_start_date: statusFilterState.startDate,
            status_end_date: statusFilterState.endDate
        });
        
        fetch(window.location.pathname + '?' + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(data => {
            updateLiveMetrics(data);
            updateTodayMetrics(data);
        })
        .catch(error => console.error('Live refresh error:', error));
    }, 30000);
}

// ========================================================================
// INITIALIZE CHARTS
// ========================================================================
async function initDashboard() {
    while (typeof ApexCharts === 'undefined') {
        await new Promise(resolve => setTimeout(resolve, 100));
    }
    
    // Status Chart
    const statusChartElement = document.querySelector("#ordersStatusChart");
    if (statusChartElement) {
        statusChart = new ApexCharts(statusChartElement, {
            series: [
                orderStatusData.pending || 0,
                orderStatusData.confirmed || 0,
                orderStatusData.preparing || 0,
                orderStatusData.ready || 0,
                orderStatusData['picked up'] || 0,
                orderStatusData.cancelled || 0
            ],
            chart: { type: 'donut', height: 350 },
            labels: ['Pending', 'Confirmed', 'Preparing', 'Ready', 'Picked Up', 'Cancelled'],
            colors: ['#FD7E14', '#0dcaf0', '#ffc107', '#20c997', '#198754', '#dc3545'],
            legend: { position: 'bottom' },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: { show: true, label: 'Total Orders' }
                        }
                    }
                }
            }
        });
        statusChart.render();
    }
    
    // Sales Chart
    const salesChartElement = document.querySelector("#salesPerformanceChart");
    if (salesChartElement && salesPerformance) {
        const months = salesPerformance.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        
        salesChart = new ApexCharts(salesChartElement, {
            series: [
                {
                    name: 'Revenue',
                    type: 'area',
                    data: salesPerformance.map(item => parseFloat(item.total_sales || 0))
                },
                {
                    name: 'Orders',
                    type: 'line',
                    data: salesPerformance.map(item => parseInt(item.order_count || 0))
                }
            ],
            chart: { height: 450, type: 'line' },
            colors: ['#A52A2A', '#0d6efd'],
            stroke: { curve: 'smooth', width: [3, 3] },
            labels: months,
            xaxis: { title: { text: 'Month' } },
            yaxis: [
                {
                    title: { text: 'Revenue (₱)' },
                    labels: { formatter: val => '₱' + val.toLocaleString('en-PH') }
                },
                {
                    opposite: true,
                    title: { text: 'Orders' }
                }
            ],
            legend: { position: 'top' }
        });
        salesChart.render();
    }
    
    initSalesFilters();
    initStatusFilters();
    startLiveRefresh();
    
    console.log('✅ Dashboard initialized');
}

// Start
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initDashboard);
} else {
    initDashboard();
}
</script>
@endpush