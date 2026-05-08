<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\AIInsightController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AdminController;
use App\Models\BudgetTarget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('guest')->group(function () {
    Route::get('register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('register', [AuthController::class, 'register'])->name('register.perform');

    Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('login', [AuthController::class, 'login'])->name('login.perform');
});

Route::post('logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    // Admin Routes
    Route::middleware('admin')->group(function () {
        Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
        Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
        Route::get('/admin/targets', [AdminController::class, 'targets'])->name('admin.targets');
        Route::get('/admin/categories', [AdminController::class, 'categories'])->name('admin.categories');
        Route::get('/admin/activity-logs', [AdminController::class, 'activityLogs'])->name('admin.activity-logs');
        Route::get('/admin/users/download', [AdminController::class, 'downloadUsers'])->name('admin.users.download');
    });

    // AI Insights Routes
    Route::get('/ai/expense-advice', [AIInsightController::class, 'getExpenseAdvice']);
    Route::get('/ai/spending-insights', [AIInsightController::class, 'getSpendingInsights']);
    Route::post('/ai/budget-recommendations', [AIInsightController::class, 'getBudgetRecommendations']);
    Route::get('/ai/fallback-advice', [AIInsightController::class, 'getFallbackAdvice']);

    // Report Routes
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/download/excel', [ReportController::class, 'downloadExcel'])->name('reports.download.excel');
    Route::get('/reports/download/pdf', [ReportController::class, 'downloadPDF'])->name('reports.download.pdf');
    Route::get('/reports/download/csv', [ReportController::class, 'downloadCSV'])->name('reports.download.csv');
    Route::get('/reports/chart-data', [ReportController::class, 'getChartData'])->name('reports.chart-data');

    // Target-specific Reports
    Route::get('/reports/target/{target}', [ReportController::class, 'downloadTargetReport'])->name('reports.target');

    // Monthly Reports
    Route::get('/reports/monthly', [ReportController::class, 'downloadMonthlyReport'])->name('reports.monthly');

    Route::get('dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::post('budget', function (Request $request) {
        $user = auth()->user();

        // Check if there's an active (non-completed) target
        $activeTarget = $user->activeBudgetTarget();
        if ($activeTarget && $activeTarget->id) {
            return redirect()
                ->route('dashboard')
                ->with('error', 'You already have an active target from ' . $activeTarget->start_date->format('M d') . ' to ' . $activeTarget->end_date->format('M d') . '. Please complete it or wait for it to end before creating a new one.');
        }

        $validated = $request->validate([
            'target_amount' => ['required', 'numeric', 'min:1', 'max:10000000'],
            'duration_days' => ['required', 'integer', 'in:7,14,30,60,90'],
        ], [
            'target_amount.min' => 'Budget amount must be at least RWF 1.',
            'target_amount.max' => 'Budget amount cannot exceed RWF 10,000,000.',
            'target_amount.required' => 'Please enter a target amount.',
            'duration_days.required' => 'Please select a duration.',
        ]);

        $startDate = now()->startOfDay();
        $endDate = $startDate->copy()->addDays((int) $validated['duration_days']);

        BudgetTarget::create([
            'user_id' => $user->id,
            'target_amount' => $validated['target_amount'],
            'start_date' => $startDate->toDateString(),
            'end_date' => $endDate->toDateString(),
        ]);

        // Keep legacy fields synced for backward compatibility.
        $user->update([
            'target_amount' => $validated['target_amount'],
            'budget_start_date' => $startDate->toDateString(),
            'budget_end_date' => $endDate->toDateString(),
        ]);

        return redirect()
            ->route('dashboard')
            ->with('success', 'New spending target created from ' . $startDate->format('M d') . ' to ' . $endDate->format('M d, Y') . '.');
    })->name('budget.store');

    Route::resource('expenses', ExpenseController::class);
});