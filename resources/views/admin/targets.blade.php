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
    
    .target-row {
        transition: all 0.2s ease;
    }
    
    .target-row:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }
    
    .progress {
        border-radius: 8px;
        overflow: hidden;
    }
    
    .progress-bar {
        border-radius: 8px;
    }
    
    .status-active {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }
    
    .status-locked {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }
    
    .status-completed {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-bullseye-fill me-2"></i>Budget Targets
                    </h2>
                    <p class="text-muted mb-0">Monitor all user budget targets and progress</p>
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
                    <form method="GET" action="{{ route('admin.targets') }}">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ ($sortBy === 'created_at') ? 'selected' : '' }}>Creation Date</option>
                                    <option value="target_amount" {{ ($sortBy === 'target_amount') ? 'selected' : '' }}>Target Amount</option>
                                    <option value="start_date" {{ ($sortBy === 'start_date') ? 'selected' : '' }}>Start Date</option>
                                    <option value="end_date" {{ ($sortBy === 'end_date') ? 'selected' : '' }}>End Date</option>
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
                                <a href="{{ route('admin.targets') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Targets Table -->
    <div class="card admin-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>All Budget Targets
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Target Amount</th>
                            <th>Duration</th>
                            <th>Spent</th>
                            <th>Progress</th>
                            <th>Status</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($targets as $target)
                            @php
                                $spent = $target->user->expenses()
                                    ->whereBetween('date', [$target->start_date->toDateString(), $target->end_date->toDateString()])
                                    ->sum('amount');
                                $progress = $target->target_amount > 0 ? min(100, ($spent / $target->target_amount) * 100) : 0;
                                $isLocked = $target->locked_at !== null;
                                $isOverBudget = $spent > $target->target_amount;
                            @endphp
                            <tr class="target-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($target->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $target->user->name }}</div>
                                            <small class="text-muted">{{ $target->user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="fw-semibold">RWF {{ number_format($target->target_amount, 0) }}</td>
                                <td>
                                    <div>{{ $target->start_date->format('M d') }} - {{ $target->end_date->format('M d, Y') }}</div>
                                    <small class="text-muted">{{ $target->start_date->diffInDays($target->end_date) + 1 }} days</small>
                                </td>
                                <td class="fw-semibold {{ $isOverBudget ? 'text-danger' : 'text-success' }}">
                                    RWF {{ number_format($spent, 0) }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress me-2" style="width: 100px; height: 20px;">
                                            <div class="progress-bar {{ $isOverBudget ? 'bg-danger' : ($progress >= 85 ? 'bg-warning' : 'bg-success') }}" 
                                                 style="width: {{ $progress }}%">
                                            </div>
                                        </div>
                                        <small class="fw-semibold">{{ round($progress) }}%</small>
                                    </div>
                                </td>
                                <td>
                                    @if($isLocked)
                                        @if($isOverBudget)
                                            <span class="badge status-locked">Over Budget</span>
                                        @else
                                            <span class="badge status-completed">Completed</span>
                                        @endif
                                    @else
                                        <span class="badge status-active">Active</span>
                                    @endif
                                </td>
                                <td>{{ $target->created_at->format('M d, Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="bi bi-bullseye fs-1 d-block mb-2"></i>
                                    No budget targets found in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($targets->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $targets->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
