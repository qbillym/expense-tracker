@extends('admin.layout')

@section('content')
<style>
    .admin-stats-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        color: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .admin-stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.2);
    }
    
    .admin-stats-card i {
        font-size: 2.5rem;
        color: #60a5fa;
        margin-bottom: 12px;
    }
    
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
    
    .category-bar {
        background: linear-gradient(90deg, #3b82f6 0%, #60a5fa 100%);
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .category-bar:hover {
        transform: scaleX(1.02);
    }
    
    .expense-item {
        transition: all 0.2s ease;
    }
    
    .expense-item:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }
    
    .nav-link.admin-active {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%) !important;
        color: white !important;
    }
</style>

<div class="container-fluid">
    <!-- Admin Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-shield-check me-2"></i>Admin Dashboard
                    </h2>
                    <p class="text-muted mb-0">System Overview & Management</p>
                </div>
                <div>
                    <span class="badge bg-success fs-6">
                        <i class="bi bi-circle-fill me-1"></i>System Online
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="admin-stats-card">
                <i class="bi bi-people-fill"></i>
                <h3 class="mb-1">{{ $totalUsers }}</h3>
                <p class="mb-0 opacity-75">Total Users</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="admin-stats-card">
                <i class="bi bi-receipt-cutoff"></i>
                <h3 class="mb-1">{{ $totalExpenses }}</h3>
                <p class="mb-0 opacity-75">Total Expenses</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="admin-stats-card">
                <i class="bi bi-cash-stack"></i>
                <h3 class="mb-1">RWF {{ number_format($totalAmount, 0) }}</h3>
                <p class="mb-0 opacity-75">Total Amount</p>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="admin-stats-card">
                <i class="bi bi-bullseye"></i>
                <h3 class="mb-1">{{ $activeTargets }}</h3>
                <p class="mb-0 opacity-75">Active Targets</p>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- Left Column -->
        <div class="col-lg-8">
            <!-- Most Spent Categories -->
            <div class="card admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-bar-chart-fill me-2"></i>Most Spent Categories
                    </h5>
                </div>
                <div class="card-body">
                    @if($mostSpentCategories->isNotEmpty())
                        @foreach($mostSpentCategories as $category)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-semibold">{{ $category->category }}</span>
                                    <span class="text-muted">RWF {{ number_format($category->total, 0) }}</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar category-bar" 
                                         style="width: {{ min(100, ($category->total / $mostSpentCategories->first()->total) * 100) }}%">
                                    </div>
                                </div>
                                <small class="text-muted">{{ $category->count }} transactions</small>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No expense data available.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Expenses -->
            <div class="card admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Expenses
                    </h5>
                </div>
                <div class="card-body">
                    @if($recentExpenses->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>User</th>
                                        <th>Description</th>
                                        <th>Category</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExpenses as $expense)
                                        <tr class="expense-item">
                                            <td>{{ $expense->user->name }}</td>
                                            <td>{{ Str::limit($expense->description, 30) }}</td>
                                            <td>
                                                <span class="badge bg-primary">{{ $expense->category }}</span>
                                            </td>
                                            <td class="fw-semibold">RWF {{ number_format($expense->amount, 0) }}</td>
                                            <td>{{ $expense->date->format('M d, Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">No recent expenses found.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-eye-fill me-2"></i>Monitoring Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.users') }}" class="btn btn-outline-primary">
                            <i class="bi bi-people me-2"></i>View Users
                        </a>
                        <a href="{{ route('admin.targets') }}" class="btn btn-outline-success">
                            <i class="bi bi-bullseye me-2"></i>View Targets
                        </a>
                        <a href="{{ route('admin.categories') }}" class="btn btn-outline-info">
                            <i class="bi bi-tags me-2"></i>Category Analysis
                        </a>
                        <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-warning">
                            <i class="bi bi-activity me-2"></i>Activity Logs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Users Overview -->
            <div class="card admin-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-badge me-2"></i>Recent Users
                    </h5>
                </div>
                <div class="card-body">
                    @if($users->isNotEmpty())
                        @foreach($users->take(5) as $user)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <div>
                                    <div class="fw-semibold">{{ $user->name }}</div>
                                    <small class="text-muted">{{ $user->email }}</small>
                                </div>
                                <div class="text-end">
                                    <div class="small">{{ $user->expenses->count() }} expenses</div>
                                    <small class="text-muted">RWF {{ number_format($user->expenses->sum('amount'), 0) }}</small>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">No users found.</p>
                    @endif
                </div>
            </div>

            <!-- System Info -->
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-info-circle me-2"></i>System Info
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">Laravel Version</small>
                        <div class="fw-semibold">{{ app()->version() }}</div>
                    </div>
                    <div class="mb-2">
                        <small class="text-muted">PHP Version</small>
                        <div class="fw-semibold">{{ PHP_VERSION }}</div>
                    </div>
                    <div>
                        <small class="text-muted">Environment</small>
                        <div class="fw-semibold">{{ app()->environment() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
