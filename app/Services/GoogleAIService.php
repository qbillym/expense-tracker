<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoogleAIService
{
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent';

    public function __construct()
    {
        $this->apiKey = config('services.google_ai.api_key');
    }

    public function generateExpenseAdvice(array $expenses, float $budgetAmount, string $timeframe): ?string
    {
        if (!$this->apiKey) {
            return null;
        }

        $totalSpent = array_sum(array_column($expenses, 'amount'));
        $categories = $this->groupExpensesByCategory($expenses);
        $topCategory = $this->getTopSpendingCategory($categories);
        $averageSpending = count($expenses) > 0 ? $totalSpent / count($expenses) : 0;

        $prompt = $this->buildAdvicePrompt($expenses, $budgetAmount, $timeframe, $totalSpent, $categories, $topCategory, $averageSpending);

        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ],
                'generationConfig' => [
                    'temperature' => 0.7,
                    'topK' => 40,
                    'topP' => 0.95,
                    'maxOutputTokens' => 1024,
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            Log::error('Google AI API Error: ' . $response->body());
            return null;

        } catch (\Exception $e) {
            Log::error('Google AI Service Error: ' . $e->getMessage());
            return null;
        }
    }

    public function generateSpendingInsights(array $expenses, string $period): ?string
    {
        if (!$this->apiKey) {
            return null;
        }

        $categories = $this->groupExpensesByCategory($expenses);
        $totalSpent = array_sum(array_column($expenses, 'amount'));
        $expenseCount = count($expenses);
        $dailyAverage = $expenseCount > 0 ? $totalSpent / max(1, $this->getUniqueDays($expenses)) : 0;

        $prompt = $this->buildInsightsPrompt($expenses, $period, $categories, $totalSpent, $expenseCount, $dailyAverage);

        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Google AI Service Error: ' . $e->getMessage());
            return null;
        }
    }

    public function generateBudgetRecommendations(float $income, array $expenses): ?string
    {
        if (!$this->apiKey) {
            return null;
        }

        $currentSpending = array_sum(array_column($expenses, 'amount'));
        $categories = $this->groupExpensesByCategory($expenses);

        $prompt = $this->buildBudgetPrompt($income, $currentSpending, $categories);

        try {
            $response = Http::post($this->baseUrl . '?key=' . $this->apiKey, [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => $prompt
                            ]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
            }

            return null;

        } catch (\Exception $e) {
            Log::error('Google AI Service Error: ' . $e->getMessage());
            return null;
        }
    }

    private function buildAdvicePrompt(array $expenses, float $budget, string $timeframe, float $totalSpent, array $categories, string $topCategory, float $averageSpending): string
    {
        $expensesText = $this->formatExpensesForPrompt($expenses);
        
        return "As a financial advisor, analyze the following expense data and provide personalized advice:

Budget Information:
- Budget: RWF " . number_format($budget, 0) . "
- Timeframe: {$timeframe}
- Total Spent: RWF " . number_format($totalSpent, 0) . "
- Remaining: RWF " . number_format($budget - $totalSpent, 0) . "
- Top Spending Category: {$topCategory}
- Average Transaction: RWF " . number_format($averageSpending, 0) . "

Expense Breakdown by Category:
{$this->formatCategoriesForPrompt($categories)}

Recent Expenses:
{$expensesText}

Please provide:
1. Specific actionable advice to optimize spending
2. Warning signs to watch for
3. Suggestions for the top spending category
4. Tips to stay within budget
5. Positive reinforcement for good habits

Keep the advice practical, encouraging, and specific to the data provided. Use bullet points for easy reading.";
    }

    private function buildInsightsPrompt(array $expenses, string $period, array $categories, float $totalSpent, int $expenseCount, float $dailyAverage): string
    {
        return "Analyze these spending patterns for {$period} and provide key insights:

Total Spent: RWF " . number_format($totalSpent, 0) . "
Number of Transactions: {$expenseCount}
Daily Average: RWF " . number_format($dailyAverage, 0) . "

Category Breakdown:
{$this->formatCategoriesForPrompt($categories)}

Provide:
1. Spending patterns and trends
2. Unusual spending to investigate
3. Areas for potential savings
4. Comparison to typical spending habits
5. Recommendations for improvement

Format as a concise analysis with clear insights.";
    }

    private function buildBudgetPrompt(float $income, float $currentSpending, array $categories): string
    {
        return "Help create a budget recommendation based on this financial data:

Monthly Income: RWF " . number_format($income, 0) . "
Current Monthly Spending: RWF " . number_format($currentSpending, 0) . "
Savings Potential: RWF " . number_format($income - $currentSpending, 0) . "

Current Spending by Category:
{$this->formatCategoriesForPrompt($categories)}

Provide:
1. Recommended budget allocations by category (using 50/30/20 rule as guideline)
2. Areas to reduce spending
3. Realistic savings goals
4. Strategies to stick to the budget
5. Monthly financial goals

Make recommendations practical and achievable based on the income level.";
    }

    private function groupExpensesByCategory(array $expenses): array
    {
        $categories = [];
        foreach ($expenses as $expense) {
            $category = $expense['category'] ?? 'Other';
            $categories[$category] = ($categories[$category] ?? 0) + $expense['amount'];
        }
        arsort($categories);
        return $categories;
    }

    private function getTopSpendingCategory(array $categories): string
    {
        return array_key_first($categories) ?: 'None';
    }

    private function formatExpensesForPrompt(array $expenses): string
    {
        $formatted = [];
        foreach (array_slice($expenses, 0, 10) as $expense) {
            $formatted[] = "- {$expense['title']}: RWF " . number_format($expense['amount'], 0) . " ({$expense['category']})";
        }
        return implode("\n", $formatted);
    }

    private function formatCategoriesForPrompt(array $categories): string
    {
        $formatted = [];
        foreach ($categories as $category => $amount) {
            $formatted[] = "- {$category}: RWF " . number_format($amount, 0);
        }
        return implode("\n", $formatted);
    }

    private function getUniqueDays(array $expenses): int
    {
        $days = [];
        foreach ($expenses as $expense) {
            $days[] = date('Y-m-d', strtotime($expense['date']));
        }
        return count(array_unique($days));
    }
}
