@extends('admin.layout')

@section('content')
<style>
    .admin-card {
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }
    
    .admin-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
    }
    
    .admin-card .card-header {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 16px 16px 0 0;
        border: none;
        font-weight: 600;
    }
    
    .category-row {
        transition: all 0.2s ease;
    }
    
    .category-row:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }
    
    .category-bar {
        background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .category-bar:hover {
        transform: scaleX(1.02);
    }
    
    .stats-card {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
        border-radius: 12px;
        padding: 20px;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-tags-fill me-2"></i>Category Analysis
                    </h2>
                    <p class="text-muted mb-0">Detailed breakdown of spending by categories</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Sorting
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.categories') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="total" {{ ($sortBy === 'total') ? 'selected' : '' }}>Total Amount</option>
                                    <option value="category" {{ ($sortBy === 'category') ? 'selected' : '' }}>Category Name</option>
                                    <option value="count" {{ ($sortBy === 'count') ? 'selected' : '' }}>Transaction Count</option>
                                    <option value="average" {{ ($sortBy === 'average') ? 'selected' : '' }}>Average Amount</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="desc" {{ ($sortOrder === 'desc') ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ ($sortOrder === 'asc') ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-admin">
                                    <i class="bi bi-funnel me-2"></i>Apply Sorting
                                </button>
                                <a href="{{ route('admin.categories') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stats-card">
                <i class="bi bi-tags fs-2 mb-2"></i>
                <h3>{{ $categoryStats->count() }}</h3>
                <p class="mb-0">Total Categories</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="bi bi-cash-stack fs-2 mb-2"></i>
                <h3>RWF {{ number_format($categoryStats->sum('total'), 0) }}</h3>
                <p class="mb-0">Total Spending</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stats-card">
                <i class="bi bi-receipt-cutoff fs-2 mb-2"></i>
                <h3>{{ $categoryStats->sum('count') }}</h3>
                <p class="mb-0">Total Transactions</p>
            </div>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card admin-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Category Breakdown
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Transactions</th>
                            <th>Total Amount</th>
                            <th>Average Amount</th>
                            <th>Percentage</th>
                            <th>Visual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categoryStats as $index => $category)
                            @php
                                $totalSpending = $categoryStats->sum('total');
                                $percentage = $totalSpending > 0 ? ($category->total / $totalSpending) * 100 : 0;
                            @endphp
                            <tr class="category-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.875rem;">
                                            {{ strtoupper(substr($category->category, 0, 1)) }}
                                        </div>
                                        <div class="fw-semibold">{{ $category->category }}</div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $category->count }}</span>
                                </td>
                                <td class="fw-semibold">RWF {{ number_format($category->total, 0) }}</td>
                                <td>RWF {{ number_format($category->average, 0) }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ round($percentage, 1) }}%</span>
                                </td>
                                <td>
                                    <div class="progress" style="width: 120px; height: 8px;">
                                        <div class="progress-bar category-bar" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <i class="bi bi-tags fs-1 d-block mb-2"></i>
                                    No category data found in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Categories Chart -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-fill me-2"></i>Top 5 Categories by Spending
                    </h5>
                </div>
                <div class="card-body">
                    @if($categoryStats->isNotEmpty())
                        <div class="row">
                            @foreach($categoryStats->take(5) as $index => $category)
                                @php
                                    $totalSpending = $categoryStats->sum('total');
                                    $percentage = $totalSpending > 0 ? ($category->total / $totalSpending) * 100 : 0;
                                @endphp
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="text-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 60px; height: 60px; font-size: 1.5rem;">
                                            {{ $index + 1 }}
                                        </div>
                                        <h6 class="fw-semibold">{{ $category->category }}</h6>
                                        <div class="text-muted">RWF {{ number_format($category->total, 0) }}</div>
                                        <small class="text-muted">{{ round($percentage, 1) }}% of total</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted text-center">No category data available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
