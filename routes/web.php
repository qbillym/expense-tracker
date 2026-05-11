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

// Simple test route
Route::get('/test', function () {
    return response()->json([
        'status' => 'working',
        'timestamp' => now(),
        'environment' => app()->environment()
    ]);
});

// Test route to bypass authentication and test dashboard
Route::get('/test-dashboard', function () {
    try {
        // Create a test user
        $testUser = \App\Models\User::create([
            'name' => 'Test User',
            'email' => 'test' . time() . '@example.com',
            'password' => \Hash::make('password123'),
        ]);
        
        // Log in the test user
        auth()->login($testUser);
        
        // Try to load the dashboard controller
        $controller = new \App\Http\Controllers\DashboardController();
        $response = $controller->index(request());
        
        // Clean up
        auth()->logout();
        $testUser->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Dashboard controller works without authentication issues'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

// Debug route to test database and identify issues
Route::get('/debug', function () {
    try {
        $debug = [
            'environment' => app()->environment(),
            'database_connection' => config('database.default'),
            'database_host' => config('database.connections.' . config('database.default') . '.host'),
            'database_name' => config('database.connections.' . config('database.default') . '.database'),
            'database_status' => 'connected',
            'tables' => [],
        ];
        
        // Test database connection
        try {
            $tables = \DB::select("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
            foreach ($tables as $table) {
                $debug['tables'][] = $table->table_name;
            }
        } catch (\Exception $e) {
            $debug['database_status'] = 'error: ' . $e->getMessage();
        }
        
        // Test user creation
        try {
            $testUser = [
                'name' => 'Debug Test',
                'email' => 'debug' . time() . '@test.com',
                'password' => \Hash::make('password123'),
            ];
            
            $user = \App\Models\User::create($testUser);
            $debug['user_creation'] = 'success: ' . $user->id;
            $user->delete(); // Clean up
        } catch (\Exception $e) {
            $debug['user_creation'] = 'error: ' . $e->getMessage();
        }
        
        return response()->json($debug);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ], 500);
    }
});

Route::middleware(['guest', 'database.ready'])->group(function () {
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