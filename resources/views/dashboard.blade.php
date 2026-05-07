@extends('layouts.app')

@section('content')
<style>
    /* Clean Dashboard Styling */
    .stats-card {
        background: linear-gradient(135deg, var(--primary-green) 0%, var(--medium-green) 50%, var(--accent-green) 100%);
        color: white;
        border-radius: 20px;
        padding: 28px;
        text-align: center;
        box-shadow: 0 10px 30px rgba(13, 79, 47, 0.3);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .stats-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 15px 40px rgba(13, 79, 47, 0.4);
    }
    
    .stats-card i {
        font-size: 2.5rem;
        margin-bottom: 15px;
        opacity: 0.9;
    }

    .stats-card h2 {
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stats-card small {
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 500;
    }
    
    .card {
        border-radius: 16px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }
    
    .card-header {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        color: #1f2937;
        font-weight: 600;
        border: none;
        border-bottom: 1px solid #e5e7eb;
        padding: 20px 24px;
    }
    
    .progress {
        border-radius: 8px;
        height: 8px;
        background: #e5e7eb;
    }
    
    .progress-bar {
        border-radius: 8px;
        background: #3b82f6;
        transition: all 0.3s ease;
    }
    
    .btn {
        border-radius: 8px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .expense-item {
        transition: all 0.2s ease;
    }
    
    .expense-item:hover {
        background: #f8fafc;
        transform: translateX(4px);
    }
    
    .category-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-weight: 500;
        font-size: 0.75rem;
        text-transform: uppercase;
    }
    
    .alert {
        border-radius: 8px;
        border: none;
        font-weight: 500;
    }
    
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.1);
    }
    
    /* Simple loading animation */
    .ai-thinking {
        display: inline-block;
        width: 6px;
        height: 6px;
        background: #3b82f6;
        border-radius: 50%;
        margin: 0 1px;
        animation: aiPulse 1.4s ease-in-out infinite both;
    }
    
    .ai-thinking:nth-child(1) { animation-delay: -0.32s; }
    .ai-thinking:nth-child(2) { animation-delay: -0.16s; }
    .ai-thinking:nth-child(3) { animation-delay: 0s; }
    
    @keyframes aiPulse {
        0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
        40% { transform: scale(1); opacity: 1; }
    }

    /* Fun Interactive Elements */
    .fun-toast {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        animation: slideInRight 0.5s ease-out;
    }

    .fun-toast.good {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .fun-toast.warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .fun-toast.danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .budget-celebration {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 10000;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        padding: 30px 50px;
        border-radius: 20px;
        font-size: 1.5rem;
        font-weight: bold;
        text-align: center;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        animation: celebratePop 0.6s ease-out;
    }

    @keyframes celebratePop {
        0% {
            transform: translate(-50%, -50%) scale(0);
            opacity: 0;
        }
        50% {
            transform: translate(-50%, -50%) scale(1.1);
        }
        100% {
            transform: translate(-50%, -50%) scale(1);
            opacity: 1;
        }
    }

    .confetti {
        position: fixed;
        width: 10px;
        height: 10px;
        background: #fbbf24;
        z-index: 9999;
        animation: confettiFall 3s linear forwards;
    }

    @keyframes confettiFall {
        0% {
            transform: translateY(-100vh) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(720deg);
            opacity: 0;
        }
    }

    .target-history-item {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s ease;
    }

    .target-history-item:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced Chart Animations */
    canvas {
        animation: fadeInScale 0.8s ease-out;
    }

    @keyframes fadeInScale {
        from {
            opacity: 0;
            transform: scale(0.9);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Interactive hover effects */
    .stats-card {
        cursor: pointer;
    }

    .stats-card:active {
        transform: translateY(-2px) scale(1.01);
    }

    /* AI Advice Styling */
    #aiAdviceContent {
        min-height: 120px;
    }

    #aiAdviceContent .badge {
        font-size: 0.8rem;
        padding: 6px 12px;
        border-radius: 20px;
    }

    #aiAdviceContent li {
        position: relative;
        padding-left: 20px;
    }

    #aiAdviceContent li:before {
        content: "💡";
        position: absolute;
        left: 0;
    }

    #aiAdviceContent strong {
        color: #1f2937;
        font-weight: 600;
    }
</style>

    <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="h4 mb-1">Welcome back, {{ auth()->user()->name }}</h2>
        <p class="text-muted mb-0">Here's your expense overview</p>
    </div>
</div>

    @php
        $user = auth()->user();
        $user->refreshBudgetLocks();
        $activeTarget = $user->activeBudgetTarget();
        $completedTargets = $user->completedBudgetTargets()->take(5)->get();
        $userExpenses = $user->expenses;
        $totalExpenses = $userExpenses->count();
        $totalAmount = $userExpenses->sum('amount');
        $thisMonthExpenses = $userExpenses->where('date', '>=', now()->startOfMonth());
        $thisMonthCount = $thisMonthExpenses->count();
        $thisMonthAmount = $thisMonthExpenses->sum('amount');
        $recentExpenses = $userExpenses->sortByDesc('date')->take(5);

        $budgetActive = $activeTarget !== null;
        $budgetSpent = $budgetActive ? $user->budgetSpent() : 0;
        $budgetRemaining = $budgetActive ? $user->budgetRemaining() : 0;
        $budgetProgress = $budgetActive ? $user->budgetProgressPercent() : 0;
        $budgetTopCategory = $budgetActive ? $user->budgetTopCategory() : null;
        $budgetTargetReached = $budgetActive && $budgetSpent >= $activeTarget->target_amount;
        $budgetDurationDays = $budgetActive ? max(1, $activeTarget->start_date->diffInDays($activeTarget->end_date)) : 1;
        $budgetCategories = collect();
        $budgetChartLabels = [];
        $budgetChartValues = [];
        $budgetTone = 'good';
        $budgetFunnyMessage = 'Wallet check: smooth ride. Keep up the discipline!';

        if ($budgetActive) {
            if ($user->isOverspending()) {
                $budgetTone = 'danger';
                $budgetFunnyMessage = 'Red alert: your wallet just screamed "plot twist!" Time to slow down.';
            } elseif ($budgetProgress >= 85 || $user->budgetDaysRemaining() <= 3) {
                $budgetTone = 'warning';
                $budgetFunnyMessage = 'Careful mode: almost at the edge. Every franc now has a mission.';
            } else {
                $budgetTone = 'good';
                $budgetFunnyMessage = 'Nice! You are outsmarting overspending like a budget ninja.';
            }
        }

        if ($budgetActive) {
            $budgetCategories = $user->budgetPeriodExpenses()->groupBy('category')->map(function ($group) {
                return $group->sum('amount');
            })->sortDesc();
            $budgetChartLabels = $budgetCategories->keys()->all();
            $budgetChartValues = $budgetCategories->values()->map(fn($value) => (float) $value)->all();
        }
    @endphp

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center" onclick="animateCard(this)">
                <i class="bi bi-receipt mb-2"></i>
                <h2 class="mb-1">{{ $totalExpenses }}</h2>
                <small>Total Expenses</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center" onclick="animateCard(this)">
                <i class="bi bi-cash mb-2"></i>
                <h2 class="mb-1">{{ number_format($totalAmount, 0) }}</h2>
                <small>Total Spent (RWF)</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center" onclick="animateCard(this)">
                <i class="bi bi-calendar-month mb-2"></i>
                <h2 class="mb-1">{{ $thisMonthCount }}</h2>
                <small>{{ now()->format('F') }} Expenses</small>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="stats-card text-center" onclick="animateCard(this)">
                <i class="bi bi-graph-up mb-2"></i>
                <h2 class="mb-1">{{ number_format($thisMonthAmount, 0) }}</h2>
                <small>{{ now()->format('F') }} Spend (RWF)</small>
            </div>
        </div>
    </div>

    <!-- Main Content Row -->
<div class="row">
    <!-- Left Column - Main Content -->
    <div class="col-lg-8">
        <!-- Budget Creation Form (when no active budget) -->
        @if(!$budgetActive)
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-bullseye me-2"></i>Create Your Budget Target
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('budget.store') }}" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="target_amount" class="form-label">
                            <i class="bi bi-cash-stack me-1"></i>Target Amount (RWF)
                        </label>
                        <input type="number" 
                               class="form-control form-control-lg" 
                               id="target_amount" 
                               name="target_amount" 
                               placeholder="50000"
                               min="1" 
                               max="10000000" 
                               required>
                        <div class="form-text">Set your spending limit for this period</div>
                    </div>
                    <div class="col-md-6">
                        <label for="duration_days" class="form-label">
                            <i class="bi bi-calendar-range me-1"></i>Duration
                        </label>
                        <select class="form-select form-select-lg" id="duration_days" name="duration_days" required>
                            <option value="">Select duration</option>
                            <option value="7">1 Week (7 days)</option>
                            <option value="14">2 Weeks (14 days)</option>
                            <option value="30" selected>1 Month (30 days)</option>
                            <option value="60">2 Months (60 days)</option>
                            <option value="90">3 Months (90 days)</option>
                        </select>
                        <div class="form-text">Choose your budget period</div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-rocket-takeoff me-2"></i>Start Budget Challenge
                        </button>
                    </div>
                </form>
            </div>
        </div>
        @endif

        <!-- Budget Status -->
        @if($budgetActive)
        <div class="card mb-4 border-{{ $user->isOverspending() ? 'danger' : 'success' }}">
            <div class="card-header bg-{{ $user->isOverspending() ? 'danger' : 'success' }} text-white">
                <h5 class="mb-0">
                    <i class="bi bi-{{ $user->isOverspending() ? 'exclamation-triangle' : 'check-circle' }} me-2"></i>
                    Budget Status: {{ $user->isOverspending() ? 'Over Budget' : 'On Track' }}
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Progress</span>
                            <span class="fw-bold">{{ $budgetProgress }}%</span>
                        </div>
                        <div class="progress mb-3">
                            <div class="progress-bar {{ $user->isOverspending() ? 'bg-danger' : 'bg-success' }}" 
                                 style="width: {{ min(100, $budgetProgress) }}%">
                            </div>
                        </div>
                        <div class="row text-center">
                            <div class="col-4">
                                <small class="text-muted">Target</small>
                                <div class="fw-bold">RWF {{ number_format($activeTarget->target_amount, 0) }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">Spent</small>
                                <div class="fw-bold {{ $user->isOverspending() ? 'text-danger' : 'text-success' }}">RWF {{ number_format($budgetSpent, 0) }}</div>
                            </div>
                            <div class="col-4">
                                <small class="text-muted">{{ $user->isOverspending() ? 'Over' : 'Remaining' }}</small>
                                <div class="fw-bold {{ $user->isOverspending() ? 'text-danger' : 'text-success' }}">
                                    RWF {{ number_format($budgetRemaining, 0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <div class="mb-3">
                            <i class="bi bi-calendar-event fs-1 text-primary"></i>
                        </div>
                        <h6 class="mb-1">{{ $user->budgetDaysRemaining() }} days left</h6>
                        <small class="text-muted">{{ $activeTarget->start_date->format('M d') }} - {{ $activeTarget->end_date->format('M d') }}</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Charts Row -->
        @if($budgetActive)
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-pie-chart me-2"></i>Spending by Category
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="budgetCategoryChart" height="80"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="bi bi-graph-up me-2"></i>Budget Progress
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="budgetProgressChart" height="80"></canvas>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Recent Expenses -->
        @if($recentExpenses->isNotEmpty())
            <div class="card animate-fade-in">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>Recent Expenses
                    </h5>
                    <a href="{{ route('expenses.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body p-0">
                    @foreach($recentExpenses as $expense)
                        <div class="d-flex align-items-center p-4 border-bottom expense-item animate-fade-in" style="animation-delay: {{ $loop->index * 0.1 }}s;">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center mb-2">
                                    <h6 class="mb-0 me-3 fw-semibold">{{ $expense->title }}</h6>
                                    <span class="badge bg-{{ $expense->getCategoryColor() }} category-badge">
                                        {{ $expense->category }}
                                    </span>
                                </div>
                                <div class="d-flex align-items-center text-muted small">
                                    <span class="me-3">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $expense->date->format('M d, Y') }}
                                    </span>
                                    <span class="me-3 fw-semibold text-success">
                                        <i class="bi bi-cash-stack me-1"></i>{{ $expense->getFormattedAmount() }}
                                    </span>
                                    @if($expense->mobile_money_message)
                                        <span class="badge bg-light text-dark">
                                            <i class="bi bi-phone me-1"></i>Mobile Money
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-end">
                                <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column - AI Advice & Reports -->
    <div class="col-lg-4">
        <!-- AI Advice Section -->
        @if($budgetActive)
        <div class="card mb-4 border-primary">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-robot me-2"></i>AI Advisor
                </h5>
                <button onclick="loadAIAdvice()" class="btn btn-sm btn-light">
                    <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                </button>
            </div>
            <div class="card-body" style="min-height: 250px;">
                <div id="aiAdviceLoading" class="text-center py-4" style="display: none;">
                    <div class="ai-thinking"></div>
                    <div class="ai-thinking"></div>
                    <div class="ai-thinking"></div>
                    <div class="mt-2 text-muted">AI is analyzing...</div>
                </div>
                <div id="aiAdviceContent" class="animate-fade-in">
                    <div class="text-center py-4 text-muted">
                        <i class="bi bi-cpu display-4 mb-3"></i>
                        <p>Click refresh for insights</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Spending Mood Indicator -->
        <div class="card mb-4 border-info">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="bi bi-emoji-smile me-2"></i>Spending Mood
                </h5>
            </div>
            <div class="card-body text-center">
                @if($budgetActive)
                    @if($user->isOverspending())
                        <div class="mood-emoji display-4 mb-2">😰</div>
                        <h6 class="text-danger">Wallet Panic!</h6>
                    @elseif($budgetProgress >= 85)
                        <div class="mood-emoji display-4 mb-2">😬</div>
                        <h6 class="text-warning">Budget Anxiety</h6>
                    @elseif($budgetProgress >= 60)
                        <div class="mood-emoji display-4 mb-2">😊</div>
                        <h6 class="text-info">Happy Spending</h6>
                    @else
                        <div class="mood-emoji display-4 mb-2">🎉</div>
                        <h6 class="text-success">Budget Champion!</h6>
                    @endif
                @else
                    <div class="mood-emoji display-4 mb-2">🤔</div>
                    <h6 class="text-secondary">No Budget Set</h6>
                @endif
            </div>
        </div>

        <!-- Quick Reports -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-download me-2"></i>Quick Reports
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('reports.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>View All Reports
                    </a>
                    @if($budgetActive)
                    <a href="{{ route('reports.target', ['target' => $activeTarget, 'format' => 'pdf']) }}" class="btn btn-outline-danger">
                        <i class="bi bi-file-earmark-pdf me-2"></i>Download PDF Report
                    </a>
                    <a href="{{ route('reports.target', ['target' => $activeTarget, 'format' => 'excel']) }}" class="btn btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-2"></i>Download Excel Report
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Completed Targets Section -->
@if($budgetActive && $completedTargets->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-archive me-2"></i>Completed Targets</h5>
                <span class="badge bg-secondary">{{ $completedTargets->count() }} archived</span>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    @foreach($completedTargets as $target)
                        <div class="col-md-6">
                            <div class="target-history-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">RWF {{ number_format($target->target_amount, 0) }}</div>
                                        <small class="text-muted">{{ $target->start_date->format('M d') }} - {{ $target->end_date->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge bg-success">Completed</span>
                                </div>
                                <div class="btn-group w-100 mt-2" role="group">
                                    <a href="{{ route('reports.target', ['target' => $target, 'format' => 'pdf']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-file-earmark-pdf me-1"></i>PDF
                                    </a>
                                    <a href="{{ route('reports.target', ['target' => $target, 'format' => 'excel']) }}" class="btn btn-sm btn-outline-success">
                                        <i class="bi bi-file-earmark-spreadsheet me-1"></i>Excel
                                    </a>
                                    <a href="{{ route('reports.target', ['target' => $target, 'format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-file-earmark-text me-1"></i>CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

    <script>
        // Load AI Advice Function
        function loadAIAdvice() {
            const adviceContent = document.getElementById('aiAdviceContent');
            const loadingDiv = document.getElementById('aiAdviceLoading');
            
            // Show loading state
            adviceContent.style.display = 'none';
            loadingDiv.style.display = 'block';
            
            // Try to get AI advice first, fallback to basic advice
            fetch('/ai/expense-advice?timeframe=current_budget')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.advice) {
                        displayAdvice(data.advice, data.is_fallback || false);
                    } else {
                        // Fallback to basic advice
                        return fetch('/ai/fallback-advice?timeframe=current_budget')
                            .then(response => response.json())
                            .then(fallbackData => {
                                if (fallbackData.success) {
                                    displayAdvice(fallbackData.advice, true);
                                } else {
                                    displayAdvice('Unable to load advice at this time. Please try again later.', true);
                                }
                            });
                    }
                })
                .catch(error => {
                    console.error('Error loading AI advice:', error);
                    // Fallback to basic advice
                    return fetch('/ai/fallback-advice?timeframe=current_budget')
                        .then(response => response.json())
                        .then(fallbackData => {
                            if (fallbackData.success) {
                                displayAdvice(fallbackData.advice, true);
                            } else {
                                displayAdvice('Unable to load advice at this time. Please try again later.', true);
                            }
                        });
                })
                .finally(() => {
                    loadingDiv.style.display = 'none';
                    adviceContent.style.display = 'block';
                });
        }
        
        function displayAdvice(advice, isFallback) {
            const adviceContent = document.getElementById('aiAdviceContent');
            
            // Format the advice with proper HTML
            const formattedAdvice = advice
                .split('\n')
                .filter(line => line.trim())
                .map(line => {
                    // Convert bullet points
                    if (line.trim().startsWith('1.') || line.trim().startsWith('2.') || line.trim().startsWith('3.') || 
                        line.trim().startsWith('4.') || line.trim().startsWith('5.') || line.trim().startsWith('-') ||
                        line.trim().startsWith('·') || line.trim().startsWith('*')) {
                        return '<li class="mb-2">' + line.replace(/^[\d\.\-\*\·]+\s*/, '') + '</li>';
                    }
                    // Convert bold text
                    line = line.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                    return '<p class="mb-2">' + line + '</p>';
                })
                .join('');
            
            const aiBadge = isFallback ? 
                '<span class="badge bg-secondary mb-2"><i class="bi bi-cpu me-1"></i>Basic Analysis</span>' :
                '<span class="badge bg-success mb-2"><i class="bi bi-robot me-1"></i>AI-Powered Insights</span>';
            
            adviceContent.innerHTML = aiBadge + formattedAdvice;
            
            // Add fade-in animation
            adviceContent.classList.add('animate-fade-in');
            setTimeout(() => {
                adviceContent.classList.remove('animate-fade-in');
            }, 600);
        }

        function animateCard(card) {
            card.style.transform = 'scale(0.95)';
            setTimeout(() => {
                card.style.transform = '';
            }, 200);
            
            // Create a fun number animation
            const number = card.querySelector('h2');
            const originalText = number.textContent;
            const originalValue = parseInt(originalText.replace(/[^0-9]/g, ''));
            
            if (originalValue) {
                let current = 0;
                const increment = originalValue / 20;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= originalValue) {
                        current = originalValue;
                        clearInterval(timer);
                    }
                    number.textContent = originalText.includes('RWF') ? 
                        'RWF ' + Math.floor(current).toLocaleString() : 
                        Math.floor(current).toLocaleString();
                }, 50);
            }
        }

        function showBudgetToast(message, level = 'good', icon = 'bi-emoji-laughing') {
            const toast = document.createElement('div');
            toast.className = `fun-toast ${level}`;
            toast.innerHTML = `
                <div class="p-3">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi ${icon} fs-5 mt-1"></i>
                        <div>
                            <strong>Budget Buddy</strong>
                            <div class="small">${message}</div>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 5500);
        }
        
        // Budget Success Celebration Function
        function celebrateBudgetSuccess() {
            // Create celebration popup
            const celebration = document.createElement('div');
            celebration.className = 'budget-celebration';
            celebration.innerHTML = `
                <i class="bi bi-trophy-fill me-3"></i>
                🎉 Budget Target Achieved! 🎉
                <div style="font-size: 1rem; margin-top: 10px;">Great job staying within budget!</div>
            `;
            document.body.appendChild(celebration);
            
            // Create confetti
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + '%';
                    confetti.style.background = ['#0d4f2f', '#22c55e', '#2e8b57', '#fbbf24', '#f59e0b'][Math.floor(Math.random() * 5)];
                    confetti.style.animationDelay = Math.random() * 0.5 + 's';
                    confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                    document.body.appendChild(confetti);
                    
                    // Remove confetti after animation
                    setTimeout(() => confetti.remove(), 3000);
                }, i * 30);
            }
            
            // Remove celebration after 4 seconds
            setTimeout(() => celebration.remove(), 4000);
        }
        
        // Check for budget success and celebrate
        function checkBudgetSuccess() {
            @if($budgetActive && $budgetTargetReached)
                celebrateBudgetSuccess();
            @endif
        }
        
        // Auto-load AI advice when page loads if budget is active
        document.addEventListener('DOMContentLoaded', function() {
            @if($budgetActive)
                setTimeout(() => {
                    loadAIAdvice();
                    // Check for budget success celebration
                    checkBudgetSuccess();
                }, 1000); // Load after 1 second to not interfere with page load

                const vibeKey = 'budget-vibe-{{ auth()->id() }}-{{ now()->format("Ymd") }}-{{ (int) $budgetProgress }}';
                if (!sessionStorage.getItem(vibeKey)) {
                    @if($user->isOverspending())
                        showBudgetToast('You are above budget. Wallet says: "emergency snacks cancelled."', 'danger', 'bi-emoji-dizzy');
                    @elseif($budgetProgress >= 85 || $user->budgetDaysRemaining() <= 3)
                        showBudgetToast('Almost there! Tiny spending choices can save the month.', 'warning', 'bi-emoji-neutral');
                    @else
                        showBudgetToast('Amazing control! Your budget discipline deserves a high-five.', 'good', 'bi-emoji-sunglasses');
                    @endif
                    sessionStorage.setItem(vibeKey, '1');
                }
            @endif
            
            // Chart initialization
            @if($budgetActive)
                const categories = @json($budgetChartLabels);
                const values = @json($budgetChartValues);

                const categoryCtx = document.getElementById('budgetCategoryChart');
                if (categoryCtx) {
                    new Chart(categoryCtx, {
                        type: 'pie',
                        data: {
                            labels: categories,
                            datasets: [{
                                data: values,
                                backgroundColor: [
                                    '#0d6efd', '#198754', '#fd7e14', '#dc3545', '#6f42c1', '#0dcaf0', '#ffc107', '#adb5bd', '#6610f2', '#20c997'
                                ],
                            }],
                        },
                        options: {
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }

                const progressCtx = document.getElementById('budgetProgressChart');
                if (progressCtx) {
                    new Chart(progressCtx, {
                        type: 'doughnut',
                        data: {
                            labels: ['Spent', 'Remaining'],
                            datasets: [{
                                data: [{{ $budgetSpent }}, {{ max(0, $activeTarget->target_amount - $budgetSpent) }}],
                                backgroundColor: [
                                    '{{ $user->isOverspending() ? '#ef4444' : '#dc3545' }}',
                                    '{{ $user->isOverspending() ? '#7f1d1d' : '#198754' }}'
                                ],
                            }],
                        },
                        options: {
                            plugins: {
                                legend: { position: 'bottom' }
                            },
                            cutout: '70%'
                        }
                    });
                }

                if (progressCtx && {{ $user->isOverspending() ? 'true' : 'false' }}) {
                    const parent = progressCtx.parentElement;
                    const downCanvas = document.createElement('canvas');
                    downCanvas.height = 80;
                    downCanvas.classList.add('mt-3');
                    parent.appendChild(downCanvas);
                    new Chart(downCanvas, {
                        type: 'line',
                        data: {
                            labels: ['Target Start', 'Now', 'Risk'],
                            datasets: [{
                                label: 'Budget Health',
                                data: [100, 55, 18],
                                borderColor: '#ef4444',
                                backgroundColor: 'rgba(239, 68, 68, 0.2)',
                                fill: true,
                                tension: 0.45
                            }]
                        },
                        options: {
                            plugins: { legend: { display: false } },
                            scales: {
                                y: {
                                    min: 0,
                                    max: 100,
                                    ticks: {
                                        callback: function(value) {
                                            return value + '%';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            @endif
        });
    </script>
@endsection