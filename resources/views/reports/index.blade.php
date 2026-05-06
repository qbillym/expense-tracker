@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0 fw-bold">
                    <i class="bi bi-file-earmark-text me-2"></i>Expense Reports
                </h3>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown">
                        <i class="bi bi-download me-1"></i>Download Report
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="openDownloadModal()">
                            <i class="bi bi-sliders me-2"></i>Custom Download
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="{{ route('reports.download.excel') }}">
                            <i class="bi bi-file-earmark-excel me-2"></i>Excel (All Data)
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.download.pdf') }}">
                            <i class="bi bi-file-earmark-pdf me-2"></i>PDF (All Data)
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('reports.download.csv') }}">
                            <i class="bi bi-file-earmark-text me-2"></i>CSV (All Data)
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-funnel me-2"></i>Filters & Sorting
                    </h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('reports.index') }}" id="filterForm">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate ?? '' }}">
                            </div>
                            <div class="col-md-3">
                                <label for="category" class="form-label">Category</label>
                                <select class="form-select" id="category" name="category">
                                    <option value="all" {{ (!$category || $category === 'all') ? 'selected' : '' }}>All Categories</option>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat }}" {{ ($category === $cat) ? 'selected' : '' }}>{{ $cat }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="sort_by" class="form-label">Sort By</label>
                                <select class="form-select" id="sort_by" name="sort_by">
                                    <option value="date" {{ ($sortBy === 'date') ? 'selected' : '' }}>Date</option>
                                    <option value="amount" {{ ($sortBy === 'amount') ? 'selected' : '' }}>Amount</option>
                                    <option value="title" {{ ($sortBy === 'title') ? 'selected' : '' }}>Title</option>
                                    <option value="category" {{ ($sortBy === 'category') ? 'selected' : '' }}>Category</option>
                                </select>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <label for="sort_order" class="form-label">Sort Order</label>
                                <select class="form-select" id="sort_order" name="sort_order">
                                    <option value="desc" {{ ($sortOrder === 'desc') ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ ($sortOrder === 'asc') ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                            <div class="col-md-9 d-flex align-items-end">
                                <div class="btn-group me-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-funnel me-1"></i>Apply Filters
                                    </button>
                                    <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Clear
                                    </a>
                                </div>
                                @if($startDate || $endDate || ($category && $category !== 'all'))
                                    <div class="alert alert-info mb-0 py-2 px-3">
                                        <small class="mb-0">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Showing {{ $expenses->count() }} filtered expenses
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-0 fw-bold">{{ $totalExpenses }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">Total Expenses</small>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-receipt fs-1 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card animate-fade-in" style="animation-delay: 0.2s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-0 fw-bold">RWF {{ number_format($totalAmount, 0) }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">Total Spent</small>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-cash fs-1 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card animate-fade-in" style="animation-delay: 0.3s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-0 fw-bold">RWF {{ number_format($averageExpense, 0) }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">Average Expense</small>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-calculator fs-1 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-0 fw-bold">{{ $categoryBreakdown->count() }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">Categories</small>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-tags fs-1 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <div class="btn-group" role="group" aria-label="View Toggle">
                    <button type="button" class="btn btn-outline-primary active" id="btnGraphView" onclick="switchView('graph')">
                        <i class="bi bi-bar-chart-fill me-1"></i>Graphs
                    </button>
                    <button type="button" class="btn btn-outline-primary" id="btnTableView" onclick="switchView('table')">
                        <i class="bi bi-table me-1"></i>Tables
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row mb-4" id="graphView">
        <div class="col-lg-6 mb-4">
            <div class="card animate-fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart-fill me-2"></i>Spending by Category
                    </h5>
                    <span class="badge bg-primary">Graph View</span>
                </div>
                <div class="card-body">
                    <canvas id="categoryChart" height="300"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card animate-fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up me-2"></i>Monthly Trends
                    </h5>
                    <span class="badge bg-primary">Graph View</span>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Section (Hidden by default) -->
    <div class="row mb-4" id="tableView" style="display: none;">
        <div class="col-lg-6 mb-4">
            <div class="card animate-fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>Spending by Category
                    </h5>
                    <span class="badge bg-secondary">Table View</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Category</th>
                                    <th>Amount (RWF)</th>
                                    <th>% of Total</th>
                                    <th>Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryBreakdown->sortByDesc('total') as $category => $data)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ \App\Models\Expense::getCategoryColorStatic($category) }}">
                                                {{ $category }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">{{ number_format($data['total'], 0) }}</td>
                                        <td>{{ $totalAmount > 0 ? round(($data['total'] / $totalAmount) * 100, 1) : 0 }}%</td>
                                        <td style="width: 30%;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-{{ \App\Models\Expense::getCategoryColorStatic($category) }}"
                                                     style="width: {{ $totalAmount > 0 ? ($data['total'] / $totalAmount) * 100 : 0 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold">Total</td>
                                    <td class="fw-bold">{{ number_format($totalAmount, 0) }}</td>
                                    <td class="fw-bold">100%</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card animate-fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>Monthly Trends
                    </h5>
                    <span class="badge bg-secondary">Table View</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Month</th>
                                    <th>Expenses</th>
                                    <th>Amount (RWF)</th>
                                    <th>Avg/Expense</th>
                                    <th>Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $maxMonthly = $monthlyTrends->max('total');
                                @endphp
                                @foreach($monthlyTrends as $month => $data)
                                    <tr>
                                        <td>{{ $data['month'] }}</td>
                                        <td>{{ $data['count'] }}</td>
                                        <td class="fw-semibold">{{ number_format($data['total'], 0) }}</td>
                                        <td>{{ $data['count'] > 0 ? number_format($data['total'] / $data['count'], 0) : 0 }}</td>
                                        <td style="width: 25%;">
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-success"
                                                     style="width: {{ $maxMonthly > 0 ? ($data['total'] / $maxMonthly) * 100 : 0 }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Breakdown Table -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card animate-fade-in">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-table me-2"></i>Category Breakdown
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Count</th>
                                    <th>Total Amount</th>
                                    <th>Average</th>
                                    <th>Percentage</th>
                                    <th>Visual</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categoryBreakdown as $category => $data)
                                    <tr>
                                        <td>
                                            <span class="badge bg-{{ \App\Models\Expense::getCategoryColorStatic($category) }}">
                                                {{ $category }}
                                            </span>
                                        </td>
                                        <td>{{ $data['count'] }}</td>
                                        <td class="fw-semibold">RWF {{ number_format($data['total'], 0) }}</td>
                                        <td>RWF {{ number_format($data['average'], 0) }}</td>
                                        <td>{{ round(($data['total'] / $totalAmount) * 100, 1) }}%</td>
                                        <td>
                                            <div class="progress" style="height: 8px;">
                                                <div class="progress-bar bg-{{ \App\Models\Expense::getCategoryColorStatic($category) }}"
                                                     style="width: {{ round(($data['total'] / $totalAmount) * 100) }}%"></div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Expenses -->
    <div class="row">
        <div class="col-12">
            <div class="card animate-fade-in">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Expenses
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Notes</th>
                                    <th>Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses->take(20) as $expense)
                                    <tr>
                                        <td>{{ $expense->date->format('M d, Y') }}</td>
                                        <td>{{ $expense->title }}</td>
                                        <td>
                                            <span class="badge bg-{{ $expense->getCategoryColor() }}">
                                                {{ $expense->category }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold text-success">{{ $expense->getFormattedAmount() }}</td>
                                        <td>{{ Str::limit($expense->notes ?? '', 30) }}</td>
                                        <td>
                                            @if($expense->mobile_money_message)
                                                <span class="badge bg-info">Mobile Money</span>
                                            @else
                                                <span class="badge bg-secondary">Manual</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($expenses->count() > 20)
                        <div class="text-center mt-3">
                            <small class="text-muted">Showing 20 of {{ $expenses->count() }} expenses. Download full report for complete data.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Download Modal -->
    <div class="modal fade" id="downloadModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-download me-2"></i>Download Custom Report
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="downloadForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category">
                                <option value="all">All Categories</option>
                                @foreach(\App\Models\Expense::CATEGORIES as $categoryName => $keywords)
                                    <option value="{{ $categoryName }}">{{ $categoryName }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Format</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="format" id="excel" value="excel" checked>
                                <label class="btn btn-outline-primary" for="excel">
                                    <i class="bi bi-file-earmark-excel me-1"></i>Excel
                                </label>
                                
                                <input type="radio" class="btn-check" name="format" id="pdf" value="pdf">
                                <label class="btn btn-outline-primary" for="pdf">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                </label>
                                
                                <input type="radio" class="btn-check" name="format" id="csv" value="csv">
                                <label class="btn btn-outline-primary" for="csv">
                                    <i class="bi bi-file-earmark-text me-1"></i>CSV
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="downloadCustomReport()">
                        <i class="bi bi-download me-1"></i>Download
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Chart data
        const categoryData = @json($categoryBreakdown->map(function($data, $category) {
            return [
                $category,
                $data['total']
            ];
        })->values()->toArray());

        const monthlyData = @json($monthlyTrends->map(function($data, $month) {
            return [
                $data['month'],
                $data['total']
            ];
        })->values()->toArray());

        // Initialize charts
        document.addEventListener('DOMContentLoaded', function() {
            // Category pie chart
            const categoryCtx = document.getElementById('categoryChart').getContext('2d');
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.map(item => item[0]),
                    datasets: [{
                        data: categoryData.map(item => item[1]),
                        backgroundColor: [
                            '#0d6efd', '#198754', '#fd7e14', '#dc3545', '#6f42c1', 
                            '#0dcaf0', '#ffc107', '#adb5bd', '#6610f2', '#20c997',
                            '#17a2b8', '#6c757d', '#e83e8c', '#fd7e14', '#20c997'
                        ],
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });

            // Monthly trend line chart
            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            new Chart(monthlyCtx, {
                type: 'line',
                data: {
                    labels: monthlyData.map(item => item[0]),
                    datasets: [{
                        label: 'Monthly Spending',
                        data: monthlyData.map(item => item[1]),
                        borderColor: 'rgb(13, 79, 47)',
                        backgroundColor: 'rgba(13, 79, 47, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    return 'RWF ' + value.toLocaleString();
                                }
                            }
                        }
                    }
                }
            });
        });

        // View switching function
        function switchView(view) {
            const graphView = document.getElementById('graphView');
            const tableView = document.getElementById('tableView');
            const btnGraphView = document.getElementById('btnGraphView');
            const btnTableView = document.getElementById('btnTableView');

            if (view === 'graph') {
                graphView.style.display = 'flex';
                tableView.style.display = 'none';
                btnGraphView.classList.add('active');
                btnTableView.classList.remove('active');
                localStorage.setItem('reportsViewPreference', 'graph');
            } else {
                graphView.style.display = 'none';
                tableView.style.display = 'flex';
                btnGraphView.classList.remove('active');
                btnTableView.classList.add('active');
                localStorage.setItem('reportsViewPreference', 'table');
            }
        }

        // Restore view preference on page load
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('reportsViewPreference');
            if (savedView === 'table') {
                switchView('table');
            }
        });

        // Modal functions
        function openDownloadModal() {
            const modal = new bootstrap.Modal(document.getElementById('downloadModal'));
            modal.show();
        }

        function downloadCustomReport() {
            const form = document.getElementById('downloadForm');
            const formData = new FormData(form);
            const format = formData.get('format');
            
            let url = '';
            switch(format) {
                case 'excel':
                    url = '{{ route("reports.download.excel") }}';
                    break;
                case 'pdf':
                    url = '{{ route("reports.download.pdf") }}';
                    break;
                case 'csv':
                    url = '{{ route("reports.download.csv") }}';
                    break;
            }
            
            // Add query parameters
            const params = new URLSearchParams();
            if (formData.get('start_date')) params.append('start_date', formData.get('start_date'));
            if (formData.get('end_date')) params.append('end_date', formData.get('end_date'));
            if (formData.get('category') && formData.get('category') !== 'all') {
                params.append('category', formData.get('category'));
            }
            
            window.location.href = url + '?' + params.toString();
        }
    </script>
@endsection
