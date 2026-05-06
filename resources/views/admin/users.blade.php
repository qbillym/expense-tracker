@extends('admin.layout')

@section('content')
<style>
    .admin-card {
        border-radius: 12px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        transition: all 0.2s ease;
    }
    
    .admin-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    }
    
    .admin-card .card-header {
        background: #f8fafc;
        color: #1f2937;
        font-weight: 600;
        border: none;
        border-bottom: 1px solid #e5e7eb;
        padding: 16px 20px;
    }
    
    .user-row {
        transition: all 0.2s ease;
    }
    
    .user-row:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }
    
    .stats-badge {
        background: #3b82f6;
        color: white;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.875rem;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-people-fill me-2"></i>User Monitoring
                    </h2>
                    <p class="text-muted mb-0">View and monitor all system users</p>
                </div>
                <div class="d-flex gap-2">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-download me-1"></i>Download
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="downloadAdminUsers('excel')">
                                <i class="bi bi-file-earmark-excel me-2"></i>Excel
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="downloadAdminUsers('pdf')">
                                <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="downloadAdminUsers('csv')">
                                <i class="bi bi-file-earmark-text me-2"></i>CSV
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="window.print()">
                                <i class="bi bi-printer me-2"></i>Print
                            </a></li>
                        </ul>
                    </div>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card admin-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filters & Sorting
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.users') }}">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="search" class="form-label">Search Users</label>
                                <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}" placeholder="Search by name or email">
                            </div>
                            <div class="col-md-3">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ ($sortBy === 'created_at') ? 'selected' : '' }}>Registration Date</option>
                                    <option value="name" {{ ($sortBy === 'name') ? 'selected' : '' }}>Name</option>
                                    <option value="email" {{ ($sortBy === 'email') ? 'selected' : '' }}>Email</option>
                                    <option value="expenses_count" {{ ($sortBy === 'expenses_count') ? 'selected' : '' }}>Expense Count</option>
                                    <option value="expenses_sum_amount" {{ ($sortBy === 'expenses_sum_amount') ? 'selected' : '' }}>Total Spent</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="desc" {{ ($sortOrder === 'desc') ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ ($sortOrder === 'asc') ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-funnel me-1"></i>Filter
                                </button>
                            </div>
                        </div>
                        @if($search)
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-info py-2">
                                        <small class="mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Showing {{ $users->count() }} users matching "{{ $search }}"
                                        </small>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card admin-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>User List ({{ $users->count() }} users)
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Member Since</th>
                            <th>Total Expenses</th>
                            <th>Total Spent</th>
                            <th>Average Expense</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="user-row">
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="fw-semibold">{{ $user->name }}</div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="stats-badge">{{ $user->expenses_count }}</span>
                                </td>
                                <td class="fw-semibold">RWF {{ number_format($user->expenses_sum_amount ?? 0, 0) }}</td>
                                <td>
                                    @if($user->expenses_count > 0)
                                        RWF {{ number_format(($user->expenses_sum_amount ?? 0) / $user->expenses_count, 0) }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($user->hasActiveBudget())
                                        <span class="badge bg-success">Active Budget</span>
                                    @else
                                        <span class="badge bg-secondary">No Budget</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">
                                    <i class="bi bi-people fs-1 d-block mb-2"></i>
                                    No users found in the system.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($users->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function downloadAdminUsers(format) {
    const params = new URLSearchParams({
        format: format,
        search: '{{ $search ?? "" }}',
        sort_by: '{{ $sortBy ?? "created_at" }}',
        sort_order: '{{ $sortOrder ?? "desc" }}'
    });
    
    window.location.href = `/admin/users/download?${params.toString()}`;
}
</script>
@endsection
