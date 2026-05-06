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
    
    .log-row {
        transition: all 0.2s ease;
    }
    
    .log-row:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }
    
    .action-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .action-login { background: #dcfce7; color: #166534; }
    .action-logout { background: #fee2e2; color: #991b1b; }
    .action-expense_created { background: #dbeafe; color: #1e40af; }
    .action-expense_updated { background: #fef3c7; color: #92400e; }
    .action-expense_deleted { background: #f3e8ff; color: #6b21a8; }
    .action-budget_created { background: #ecfdf5; color: #065f46; }
    .action-user_registered { background: #f0fdf4; color: #14532d; }
    .action-admin_login { background: #fef2f2; color: #7f1d1d; }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-activity me-2"></i>Activity Logs
                    </h2>
                    <p class="text-muted mb-0">Monitor all user activities and system events</p>
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
                        <i class="bi bi-funnel me-2"></i>Filters & Sorting
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.activity-logs') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="action" class="form-label">Action Type</label>
                                <select class="form-select" id="action" name="action">
                                    <option value="all" {{ $action === 'all' ? 'selected' : '' }}>All Actions</option>
                                    @foreach($actions as $act)
                                        <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ str_replace('_', ' ', ucfirst($act)) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="user_id" class="form-label">User</label>
                                <select class="form-select" id="user_id" name="user_id">
                                    <option value="all" {{ $userId === 'all' ? 'selected' : '' }}>All Users</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ $userId == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ $sortBy === 'created_at' ? 'selected' : '' }}>Date</option>
                                    <option value="action" {{ $sortBy === 'action' ? 'selected' : '' }}>Action</option>
                                    <option value="user_id" {{ $sortBy === 'user_id' ? 'selected' : '' }}>User</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="desc" {{ $sortOrder === 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ $sortOrder === 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-funnel me-2"></i>Apply Filters
                                </button>
                                <a href="{{ route('admin.activity-logs') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Clear
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Logs Table -->
    <div class="card admin-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="bi bi-list-ul me-2"></i>Activity Log Entries
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>User</th>
                            <th>Action</th>
                            <th>Description</th>
                            <th>IP Address</th>
                            <th>Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="log-row">
                                <td>
                                    <div class="fw-semibold">{{ $log->created_at->format('M d, Y H:i:s') }}</div>
                                    <small class="text-muted">{{ $log->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    @if($log->user)
                                        <div class="fw-semibold">{{ $log->user->name }}</div>
                                        <small class="text-muted">{{ $log->user->email }}</small>
                                    @else
                                        <span class="text-muted">System</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="action-badge action-{{ $log->action }}">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>
                                <td>
                                    {{ $log->description }}
                                    @if($log->subject_type && $log->subject_id)
                                        <br><small class="text-muted">ID: {{ $log->subject_id }}</small>
                                    @endif
                                </td>
                                <td>
                                    <code class="small">{{ $log->ip_address }}</code>
                                </td>
                                <td>
                                    @if($log->old_values || $log->new_values)
                                        <button class="btn btn-sm btn-outline-info" onclick="showDetails({{ $log->id }})">
                                            <i class="bi bi-eye"></i> Details
                                        </button>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    <i class="bi bi-activity fs-1 d-block mb-2"></i>
                                    No activity logs found matching the current filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($logs->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Activity Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detailsContent">
                    <!-- Content will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const logData = @json($logs->items());

    function showDetails(logId) {
        const log = logData.find(l => l.id === logId);
        if (!log) return;

        let content = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Basic Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Date:</strong></td><td>${new Date(log.created_at).toLocaleString()}</td></tr>
                        <tr><td><strong>User:</strong></td><td>${log.user ? log.user.name + ' (' + log.user.email + ')' : 'System'}</td></tr>
                        <tr><td><strong>Action:</strong></td><td>${log.action.replace('_', ' ').toUpperCase()}</td></tr>
                        <tr><td><strong>IP Address:</strong></td><td>${log.ip_address}</td></tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h6>Subject Information</h6>
                    <table class="table table-sm">
                        <tr><td><strong>Type:</strong></td><td>${log.subject_type || 'N/A'}</td></tr>
                        <tr><td><strong>ID:</strong></td><td>${log.subject_id || 'N/A'}</td></tr>
                        <tr><td><strong>Description:</strong></td><td>${log.description || 'N/A'}</td></tr>
                    </table>
                </div>
            </div>
        `;

        if (log.old_values || log.new_values) {
            content += `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>Changes Made</h6>
            `;
            
            if (log.old_values) {
                content += `
                    <div class="mb-3">
                        <h6 class="text-danger">Old Values:</h6>
                        <pre class="bg-light p-2 rounded">${JSON.stringify(log.old_values, null, 2)}</pre>
                    </div>
                `;
            }
            
            if (log.new_values) {
                content += `
                    <div class="mb-3">
                        <h6 class="text-success">New Values:</h6>
                        <pre class="bg-light p-2 rounded">${JSON.stringify(log.new_values, null, 2)}</pre>
                    </div>
                `;
            }
            
            content += `
                    </div>
                </div>
            `;
        }

        if (log.user_agent) {
            content += `
                <div class="row mt-3">
                    <div class="col-12">
                        <h6>User Agent</h6>
                        <div class="bg-light p-2 rounded small">${log.user_agent}</div>
                    </div>
                </div>
            `;
        }

        document.getElementById('detailsContent').innerHTML = content;
        new bootstrap.Modal(document.getElementById('detailsModal')).show();
    }
</script>
@endsection
