@extends('layouts.app')

@section('content')
    <div class="row mb-4">
        <!-- Stats Cards -->
        <div class="col-md-3 mb-3">
            <div class="stats-card animate-fade-in" style="animation-delay: 0.1s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-0 fw-bold">{{ $expenses->count() }}</h2>
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
                        <h2 class="mb-0 fw-bold">RWF {{ number_format($expenses->sum('amount'), 0) }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">Total Amount</small>
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
                        <h2 class="mb-0 fw-bold">{{ $expenses->where('date', '>=', now()->startOfMonth())->count() }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">This Month</small>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-calendar fs-1 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="stats-card animate-fade-in" style="animation-delay: 0.4s;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h2 class="mb-0 fw-bold">{{ $expenses->unique('category')->count() }}</h2>
                        <small class="opacity-90 text-uppercase tracking-wider">Categories</small>
                    </div>
                    <div class="icon-wrapper">
                        <i class="bi bi-tags fs-1 opacity-80"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0 fw-bold">Recent Expenses</h4>
                <div class="d-flex gap-2">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-download me-2"></i>Extract Records
                    </a>
                    @if(!Auth::user()?->is_admin)
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add New Expense
                    </a>
                    @endif
                </div>
            </div>

            @if($expenses->isEmpty())
                <div class="card animate-fade-in">
                    <div class="card-body text-center py-5">
                        <i class="bi bi-receipt display-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No expenses found</h5>
                        <p class="text-muted mb-4">Start tracking your expenses by adding your first entry.</p>
                        @if(!Auth::user()?->is_admin)
                        <a href="{{ route('expenses.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Your First Expense
                        </a>
                        @endif
                    </div>
                </div>
            @else
                <div class="row">
                    @foreach($expenses as $expense)
                        <div class="col-lg-6 mb-3">
                            <div class="expense-card animate-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <h6 class="mb-0 fw-semibold">{{ $expense->title }}</h6>
                                    <span class="badge bg-{{ $expense->getCategoryColor() }} category-badge">
                                        {{ $expense->category }}
                                    </span>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <small class="text-muted">Amount</small>
                                        <div class="fw-semibold text-success fs-5">{{ $expense->getFormattedAmount() }}</div>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Date</small>
                                        <div class="fw-semibold">{{ $expense->date->format('M d, Y') }}</div>
                                    </div>
                                </div>

                                @if($expense->mobile_money_message)
                                    <div class="mb-3">
                                        <small class="text-muted">Mobile Money</small>
                                        <div class="small text-truncate" title="{{ $expense->mobile_money_message }}">
                                            <span class="badge bg-light text-dark">
                                                <i class="bi bi-phone me-1"></i>{{ Str::limit($expense->mobile_money_message, 50) }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if($expense->detected_balance)
                                    <div class="mb-3">
                                        <small class="text-muted">Balance After</small>
                                        <div class="fw-semibold">{{ $expense->getFormattedBalance() }}</div>
                                    </div>
                                @endif

                                @if($expense->notes)
                                    <div class="mb-3">
                                        <small class="text-muted">Notes</small>
                                        <div class="small">{{ Str::limit($expense->notes, 100) }}</div>
                                    </div>
                                @endif

                                @if(!Auth::user()?->is_admin)
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-pencil me-1"></i>Edit
                                    </a>
                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                          onsubmit="return confirm('Are you sure you want to delete this expense?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Category Summary -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="bi bi-bar-chart me-2"></i>Expenses by Category
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @php
                                $categoryTotals = $expenses->groupBy('category')->map(function($group) {
                                    return $group->sum('amount');
                                })->sortDesc();
                            @endphp

                            @foreach($categoryTotals as $category => $total)
                                <div class="col-md-6 col-lg-4 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-semibold">{{ $category }}</span>
                                        <span class="badge bg-{{ \App\Models\Expense::getCategoryColorStatic($category) }}">
                                            RWF {{ number_format($total, 0) }}
                                        </span>
                                    </div>
                                    <div class="progress mt-1" style="height: 6px;">
                                        <div class="progress-bar bg-{{ \App\Models\Expense::getCategoryColorStatic($category) }}"
                                             style="width: {{ ($total / $expenses->sum('amount')) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection