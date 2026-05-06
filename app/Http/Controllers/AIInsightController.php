<?php

namespace App\Http\Controllers;

use App\Services\GoogleAIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AIInsightController extends Controller
{
    private $aiService;

    public function __construct(GoogleAIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function getExpenseAdvice(Request $request)
    {
        $user = auth()->user();
        $timeframe = $request->input('timeframe', 'current_budget');
        
        $expenses = $this->getExpensesForTimeframe($user, $timeframe);
        $budgetAmount = $user->activeBudgetTarget()?->target_amount ?? 0;
        
        // Cache AI insights for 1 hour to avoid excessive API calls
        $cacheKey = "ai_advice_{$user->id}_{$timeframe}";
        $advice = Cache::remember($cacheKey, 3600, function () use ($expenses, $budgetAmount, $timeframe) {
            return $this->aiService->generateExpenseAdvice($expenses, $budgetAmount, $timeframe);
        });

        return response()->json([
            'success' => true,
            'advice' => $advice,
            'fallback_available' => true
        ]);
    }

    public function getSpendingInsights(Request $request)
    {
        $user = auth()->user();
        $period = $request->input('period', '30_days');
        
        $expenses = $this->getExpensesForPeriod($user, $period);
        
        // Cache insights for 1 hour
        $cacheKey = "ai_insights_{$user->id}_{$period}";
        $insights = Cache::remember($cacheKey, 3600, function () use ($expenses, $period) {
            return $this->aiService->generateSpendingInsights($expenses, $period);
        });

        return response()->json([
            'success' => true,
            'insights' => $insights,
            'fallback_available' => true
        ]);
    }

    public function getBudgetRecommendations(Request $request)
    {
        $user = auth()->user();
        $income = $request->input('income', 0);
        
        if ($income <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid income amount'
            ]);
        }

        $expenses = $user->expenses()
            ->where('date', '>=', now()->subDays(30))
            ->get()
            ->toArray();
        
        // Cache recommendations for 24 hours
        $cacheKey = "ai_budget_rec_{$user->id}_{$income}";
        $recommendations = Cache::remember($cacheKey, 86400, function () use ($income, $expenses) {
            return $this->aiService->generateBudgetRecommendations($income, $expenses);
        });

        return response()->json([
            'success' => true,
            'recommendations' => $recommendations,
            'fallback_available' => true
        ]);
    }

    private function getExpensesForTimeframe($user, string $timeframe): array
    {
        switch ($timeframe) {
            case 'current_budget':
                if ($user->hasActiveBudget()) {
                    return $user->budgetPeriodExpenses()
                        ->map(function ($expense) {
                            return [
                                'title' => $expense->title,
                                'amount' => $expense->amount,
                                'category' => $expense->category,
                                'date' => $expense->date->format('Y-m-d')
                            ];
                        })
                        ->toArray();
                }
                // Fall through to last 30 days if no active budget
            case '30_days':
                return $user->expenses()
                    ->where('date', '>=', now()->subDays(30))
                    ->get()
                    ->map(function ($expense) {
                        return [
                            'title' => $expense->title,
                            'amount' => $expense->amount,
                            'category' => $expense->category,
                            'date' => $expense->date->format('Y-m-d')
                        ];
                    })
                    ->toArray();
            
            case '7_days':
                return $user->expenses()
                    ->where('date', '>=', now()->subDays(7))
                    ->get()
                    ->map(function ($expense) {
                        return [
                            'title' => $expense->title,
                            'amount' => $expense->amount,
                            'category' => $expense->category,
                            'date' => $expense->date->format('Y-m-d')
                        ];
                    })
                    ->toArray();
            
            default:
                return [];
        }
    }

    private function getExpensesForPeriod($user, string $period): array
    {
        switch ($period) {
            case '7_days':
                return $user->expenses()
                    ->where('date', '>=', now()->subDays(7))
                    ->get()
                    ->map(function ($expense) {
                        return [
                            'title' => $expense->title,
                            'amount' => $expense->amount,
                            'category' => $expense->category,
                            'date' => $expense->date->format('Y-m-d')
                        ];
                    })
                    ->toArray();
            
            case '30_days':
                return $user->expenses()
                    ->where('date', '>=', now()->subDays(30))
                    ->get()
                    ->map(function ($expense) {
                        return [
                            'title' => $expense->title,
                            'amount' => $expense->amount,
                            'category' => $expense->category,
                            'date' => $expense->date->format('Y-m-d')
                        ];
                    })
                    ->toArray();
            
            case 'current_month':
                return $user->expenses()
                    ->where('date', '>=', now()->startOfMonth())
                    ->get()
                    ->map(function ($expense) {
                        return [
                            'title' => $expense->title,
                            'amount' => $expense->amount,
                            'category' => $expense->category,
                            'date' => $expense->date->format('Y-m-d')
                        ];
                    })
                    ->toArray();
            
            default:
                return [];
        }
    }

    public function getFallbackAdvice(Request $request)
    {
        $user = auth()->user();
        $timeframe = $request->input('timeframe', 'current_budget');
        
        $expenses = $this->getExpensesForTimeframe($user, $timeframe);
        $totalSpent = array_sum(array_column($expenses, 'amount'));
        $budgetAmount = $user->activeBudgetTarget()?->target_amount ?? 0;
        
        $categories = [];
        foreach ($expenses as $expense) {
            $category = $expense['category'];
            $categories[$category] = ($categories[$category] ?? 0) + $expense['amount'];
        }
        arsort($categories);
        
        $topCategory = array_key_first($categories) ?: 'None';
        $transactionCount = count($expenses);
        $averageTransaction = $transactionCount > 0 ? $totalSpent / $transactionCount : 0;
        
        $advice = $this->generateBasicAdvice($totalSpent, $budgetAmount, $topCategory, $categories, $transactionCount, $averageTransaction);
        
        return response()->json([
            'success' => true,
            'advice' => $advice,
            'is_fallback' => true
        ]);
    }

    private function generateBasicAdvice(float $totalSpent, float $budget, string $topCategory, array $categories, int $transactionCount, float $averageTransaction): string
    {
        $advice = [];
        
        // Budget status
        if ($budget > 0) {
            $percentage = ($totalSpent / $budget) * 100;
            if ($percentage > 100) {
                $advice[] = "You've exceeded your budget by " . number_format($percentage - 100, 1) . "%. Consider reducing expenses immediately.";
            } elseif ($percentage > 80) {
                $advice[] = "You've used " . number_format($percentage, 1) . "% of your budget. Monitor remaining expenses carefully.";
            } else {
                $advice[] = "You're on track with your budget, using only " . number_format($percentage, 1) . "% so far.";
            }
        }
        
        // Top category advice
        if ($topCategory !== 'None' && isset($categories[$topCategory])) {
            $topAmount = $categories[$topCategory];
            $topPercentage = $totalSpent > 0 ? ($topAmount / $totalSpent) * 100 : 0;
            $advice[] = "Your highest spending category is {$topCategory} at " . number_format($topPercentage, 1) . "% of total expenses.";
            
            if ($topPercentage > 40) {
                $advice[] = "Consider reviewing {$topCategory} expenses as they represent a large portion of your spending.";
            }
        }
        
        // Transaction analysis
        if ($transactionCount > 0) {
            if ($averageTransaction > 50000) {
                $advice[] = "Your average transaction is RWF " . number_format($averageTransaction, 0) . ". Consider breaking down larger purchases.";
            } elseif ($transactionCount > 50) {
                $advice[] = "You have many transactions ({$transactionCount}). Track smaller purchases that add up quickly.";
            }
        }
        
        // General tips
        $advice[] = "Review your expenses weekly to identify patterns and opportunities for savings.";
        $advice[] = "Set aside money for savings before spending on discretionary items.";
        
        return implode("\n\n", $advice);
    }
}
