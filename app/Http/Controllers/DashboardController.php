<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            $user = auth()->user();
            
            // Check if user exists
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to access the dashboard.');
            }
            
            // Prepare safe default data
            $dashboardData = [
                'user' => $user,
                'activeTarget' => null,
                'completedTargets' => collect(),
                'userExpenses' => collect(),
                'totalExpenses' => 0,
                'totalAmount' => 0,
                'thisMonthExpenses' => collect(),
                'thisMonthCount' => 0,
                'thisMonthAmount' => 0,
                'recentExpenses' => collect(),
                'budgetActive' => false,
                'budgetSpent' => 0,
                'budgetRemaining' => 0,
                'budgetProgress' => 0,
                'budgetTopCategory' => null,
                'budgetTargetReached' => false,
                'budgetDurationDays' => 1,
                'budgetCategories' => collect(),
                'budgetChartLabels' => [],
                'budgetChartValues' => [],
                'budgetTone' => 'good',
                'budgetFunnyMessage' => 'Wallet check: smooth ride. Keep up the discipline!',
                'error' => null
            ];
            
            // Try to get user data with error handling
            try {
                $dashboardData['userExpenses'] = $user->expenses ?? collect();
                $dashboardData['totalExpenses'] = $dashboardData['userExpenses']->count();
                $dashboardData['totalAmount'] = $dashboardData['userExpenses']->sum('amount') ?? 0;
                $dashboardData['thisMonthExpenses'] = $dashboardData['userExpenses']->where('date', '>=', now()->startOfMonth());
                $dashboardData['thisMonthCount'] = $dashboardData['thisMonthExpenses']->count();
                $dashboardData['thisMonthAmount'] = $dashboardData['thisMonthExpenses']->sum('amount') ?? 0;
                $dashboardData['recentExpenses'] = $dashboardData['userExpenses']->sortByDesc('date')->take(5);
                
                // Try to get budget data
                if (method_exists($user, 'refreshBudgetLocks')) {
                    $user->refreshBudgetLocks();
                }
                
                if (method_exists($user, 'activeBudgetTarget')) {
                    $dashboardData['activeTarget'] = $user->activeBudgetTarget();
                    $dashboardData['budgetActive'] = $dashboardData['activeTarget'] !== null;
                    
                    if ($dashboardData['budgetActive']) {
                        try {
                            if (method_exists($user, 'budgetSpent')) {
                                $dashboardData['budgetSpent'] = $user->budgetSpent();
                            }
                            if (method_exists($user, 'budgetRemaining')) {
                                $dashboardData['budgetRemaining'] = $user->budgetRemaining();
                            }
                            if (method_exists($user, 'budgetProgressPercent')) {
                                $dashboardData['budgetProgress'] = $user->budgetProgressPercent();
                            }
                            if (method_exists($user, 'budgetTopCategory')) {
                                $dashboardData['budgetTopCategory'] = $user->budgetTopCategory();
                            }
                            
                            $dashboardData['budgetTargetReached'] = $dashboardData['budgetSpent'] >= $dashboardData['activeTarget']->target_amount;
                            $dashboardData['budgetDurationDays'] = max(1, $dashboardData['activeTarget']->start_date->diffInDays($dashboardData['activeTarget']->end_date));
                            
                            if (method_exists($user, 'isOverspending') && $user->isOverspending()) {
                                $dashboardData['budgetTone'] = 'danger';
                                $dashboardData['budgetFunnyMessage'] = 'Red alert: your wallet just screamed "plot twist!" Time to slow down.';
                            } elseif ($dashboardData['budgetProgress'] >= 85 || (method_exists($user, 'budgetDaysRemaining') && $user->budgetDaysRemaining() <= 3)) {
                                $dashboardData['budgetTone'] = 'warning';
                                $dashboardData['budgetFunnyMessage'] = 'Careful mode: almost at the edge. Every franc now has a mission.';
                            }
                            
                            if (method_exists($user, 'budgetPeriodExpenses')) {
                                $budgetExpenses = $user->budgetPeriodExpenses();
                                $dashboardData['budgetCategories'] = $budgetExpenses->groupBy('category')->map(function ($group) {
                                    return $group->sum('amount');
                                })->sortDesc();
                                $dashboardData['budgetChartLabels'] = $dashboardData['budgetCategories']->keys()->all();
                                $dashboardData['budgetChartValues'] = $dashboardData['budgetCategories']->values()->map(fn($value) => (float) $value)->all();
                            }
                        } catch (\Throwable $budgetError) {
                            \Log::error('Budget calculation error: ' . $budgetError->getMessage());
                            $dashboardData['budgetActive'] = false;
                        }
                    }
                }
                
                if (method_exists($user, 'completedBudgetTargets')) {
                    $dashboardData['completedTargets'] = $user->completedBudgetTargets()->take(5)->get();
                }
                
            } catch (\Throwable $dataError) {
                \Log::error('Dashboard data error: ' . $dataError->getMessage());
                $dashboardData['error'] = 'Some data could not be loaded. Please try again later.';
            }
            
            return view('dashboard', $dashboardData);
            
        } catch (\Throwable $e) {
            // Log the error for debugging
            \Log::error('Dashboard Error: ' . $e->getMessage());
            
            // Return a safe fallback with minimal data
            return view('dashboard', [
                'user' => auth()->user(),
                'activeTarget' => null,
                'completedTargets' => collect(),
                'userExpenses' => collect(),
                'totalExpenses' => 0,
                'totalAmount' => 0,
                'thisMonthExpenses' => collect(),
                'thisMonthCount' => 0,
                'thisMonthAmount' => 0,
                'recentExpenses' => collect(),
                'budgetActive' => false,
                'budgetSpent' => 0,
                'budgetRemaining' => 0,
                'budgetProgress' => 0,
                'budgetTopCategory' => null,
                'budgetTargetReached' => false,
                'budgetDurationDays' => 1,
                'budgetCategories' => collect(),
                'budgetChartLabels' => [],
                'budgetChartValues' => [],
                'budgetTone' => 'good',
                'budgetFunnyMessage' => 'Wallet check: smooth ride. Keep up the discipline!',
                'error' => 'There was an issue loading your dashboard. Please try again.'
            ]);
        }
    }
}
