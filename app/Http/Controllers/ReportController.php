<?php

namespace App\Http\Controllers;

use App\Models\BudgetTarget;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $category = $request->input('category');
        $sortBy = $request->input('sort_by', 'date');
        $sortOrder = $request->input('sort_order', 'desc');
        
        // Build expenses query with filters
        $expensesQuery = $user->expenses();
        
        if ($startDate) {
            $expensesQuery->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $expensesQuery->where('date', '<=', $endDate);
        }
        
        if ($category && $category !== 'all') {
            $expensesQuery->where('category', $category);
        }
        
        // Apply sorting
        if (in_array($sortBy, ['date', 'amount', 'title', 'category'])) {
            $expensesQuery->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $expensesQuery->orderBy('date', 'desc');
        }
        
        $expenses = $expensesQuery->get();
        
        // Calculate statistics
        $totalAmount = $expenses->sum('amount');
        $totalExpenses = $expenses->count();
        $averageExpense = $totalExpenses > 0 ? $totalAmount / $totalExpenses : 0;
        
        // Category breakdown
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'average' => $group->sum('amount') / $group->count()
            ];
        });
        
        // Monthly trends
        $monthlyTrends = $expenses->groupBy(function ($expense) {
            return $expense->date->format('Y-m');
        })->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
                'month' => $group->first()->date->format('F Y')
            ];
        })->sortByDesc('month');
        
        // Get all categories for filter dropdown
        $allCategories = $user->expenses()->distinct()->pluck('category')->sort()->values();
        
        return view('reports.index', compact(
            'expenses',
            'totalAmount',
            'totalExpenses',
            'averageExpense',
            'categoryBreakdown',
            'monthlyTrends',
            'allCategories',
            'startDate',
            'endDate',
            'category',
            'sortBy',
            'sortOrder'
        ));
    }
    
    public function downloadExcel(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $category = $request->input('category');
        
        $expenses = $user->expenses();
        
        if ($startDate) {
            $expenses->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $expenses->where('date', '<=', $endDate);
        }
        
        if ($category && $category !== 'all') {
            $expenses->where('category', $category);
        }
        
        $expenses = $expenses->orderBy('date', 'desc')->get();

        $filename = 'expenses_report_' . date('Y-m-d_H-i-s') . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $html = '<table border="1" cellpadding="6" cellspacing="0">';
        $html .= '<thead><tr>';
        $html .= '<th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th>Notes</th><th>Mobile Money</th>';
        $html .= '</tr></thead><tbody>';

        foreach ($expenses as $expense) {
            $html .= '<tr>';
            $html .= '<td>' . e($expense->date->format('Y-m-d')) . '</td>';
            $html .= '<td>' . e($expense->title) . '</td>';
            $html .= '<td>' . e($expense->category) . '</td>';
            $html .= '<td>' . e((string) $expense->amount) . '</td>';
            $html .= '<td>' . e($expense->notes ?? '') . '</td>';
            $html .= '<td>' . ($expense->mobile_money_message ? 'Yes' : 'No') . '</td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        return response("\xEF\xBB\xBF" . $html, 200, $headers);
    }
    
    public function downloadPDF(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $category = $request->input('category');
        
        $expenses = $user->expenses();
        
        if ($startDate) {
            $expenses->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $expenses->where('date', '<=', $endDate);
        }
        
        if ($category && $category !== 'all') {
            $expenses->where('category', $category);
        }
        
        $expenses = $expenses->orderBy('date', 'desc')->get();
        
        // Calculate statistics
        $totalAmount = $expenses->sum('amount');
        $totalExpenses = $expenses->count();
        $averageExpense = $totalExpenses > 0 ? $totalAmount / $totalExpenses : 0;
        
        // Category breakdown with percentage
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($group) use ($totalAmount) {
            $total = $group->sum('amount');
            return [
                'count' => $group->count(),
                'total' => $total,
                'percentage' => $totalAmount > 0 ? round(($total / $totalAmount) * 100, 2) : 0,
            ];
        });
        
        $pdf = Pdf::loadView('reports.pdf', compact(
            'expenses',
            'totalAmount',
            'totalExpenses',
            'averageExpense',
            'categoryBreakdown',
            'startDate',
            'endDate',
            'category'
        ));
        
        $filename = 'expenses_report_' . date('Y-m-d_H-i-s') . '.pdf';
        
        return $pdf->download($filename);
    }
    
    public function downloadCSV(Request $request)
    {
        $user = Auth::user();
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $category = $request->input('category');
        
        $expenses = $user->expenses();
        
        if ($startDate) {
            $expenses->where('date', '>=', $startDate);
        }
        
        if ($endDate) {
            $expenses->where('date', '<=', $endDate);
        }
        
        if ($category && $category !== 'all') {
            $expenses->where('category', $category);
        }
        
        $expenses = $expenses->orderBy('date', 'desc')->get();
        
        $filename = 'expenses_report_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($expenses) {
            $file = fopen('php://output', 'w');
            
            // CSV header
            fputcsv($file, ['Date', 'Title', 'Category', 'Amount', 'Notes', 'Mobile Money']);
            
            // CSV data
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('Y-m-d'),
                    $expense->title,
                    $expense->category,
                    $expense->amount,
                    $expense->notes ?? '',
                    $expense->mobile_money_message ? 'Yes' : 'No'
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', '30_days');
        
        $expenses = $user->expenses();
        
        switch ($period) {
            case '7_days':
                $expenses->where('date', '>=', now()->subDays(7));
                break;
            case '30_days':
                $expenses->where('date', '>=', now()->subDays(30));
                break;
            case '90_days':
                $expenses->where('date', '>=', now()->subDays(90));
                break;
            case 'current_month':
                $expenses->where('date', '>=', now()->startOfMonth());
                break;
        }
        
        $expenses = $expenses->get();
        
        // Daily spending data
        $dailyData = $expenses->groupBy(function ($expense) {
            return $expense->date->format('Y-m-d');
        })->map(function ($group) {
            return $group->sum('amount');
        })->toArray();
        
        // Category data
        $categoryData = $expenses->groupBy('category')->map(function ($group) {
            return $group->sum('amount');
        })->toArray();
        
        return response()->json([
            'daily' => $dailyData,
            'categories' => $categoryData,
            'total' => $expenses->sum('amount'),
            'count' => $expenses->count()
        ]);
    }

    public function downloadTargetReport(Request $request, BudgetTarget $target)
    {
        $user = Auth::user();

        // Verify the target belongs to the user
        if ($target->user_id !== $user->id) {
            abort(403, 'Unauthorized');
        }

        $format = $request->input('format', 'pdf');

        // Get expenses for this target period
        $expenses = $user->expenses()
            ->whereBetween('date', [$target->start_date->toDateString(), $target->end_date->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        $totalSpent = $expenses->sum('amount');
        $targetAmount = $target->target_amount;
        $isOverBudget = $totalSpent > $targetAmount;
        $status = $isOverBudget ? 'Exceeded' : ($totalSpent >= $targetAmount ? 'Completed' : 'In Progress');

        // Category breakdown
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($group) {
            return [
                'count' => $group->count(),
                'total' => $group->sum('amount'),
            ];
        });

        $filenameBase = 'target_report_' . $target->start_date->format('Y-m-d') . '_to_' . $target->end_date->format('Y-m-d');

        if ($format === 'csv') {
            return $this->generateTargetCSV($expenses, $target, $filenameBase, $totalSpent, $targetAmount, $status);
        }

        if ($format === 'excel') {
            return $this->generateTargetExcel($expenses, $target, $filenameBase, $totalSpent, $targetAmount, $status);
        }

        // Default PDF
        $pdf = Pdf::loadView('reports.target_pdf', compact(
            'expenses',
            'target',
            'totalSpent',
            'targetAmount',
            'isOverBudget',
            'status',
            'categoryBreakdown'
        ));

        return $pdf->download($filenameBase . '.pdf');
    }

    public function downloadMonthlyReport(Request $request)
    {
        $user = Auth::user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $format = $request->input('format', 'pdf');

        $startDate = now()->setYear($year)->setMonth($month)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $expenses = $user->expenses()
            ->whereBetween('date', [$startDate->toDateString(), $endDate->toDateString()])
            ->orderBy('date', 'desc')
            ->get();

        $totalAmount = $expenses->sum('amount');
        $totalCount = $expenses->count();
        $monthName = $startDate->format('F Y');

        // Category breakdown
        $categoryBreakdown = $expenses->groupBy('category')->map(function ($group) use ($totalAmount) {
            $total = $group->sum('amount');
            return [
                'count' => $group->count(),
                'total' => $total,
                'percentage' => $totalAmount > 0 ? round(($total / $totalAmount) * 100, 2) : 0,
            ];
        });

        $filenameBase = 'monthly_report_' . $startDate->format('Y-m');

        if ($format === 'csv') {
            return $this->generateMonthlyCSV($expenses, $monthName, $filenameBase, $totalAmount, $totalCount, $categoryBreakdown);
        }

        if ($format === 'excel') {
            return $this->generateMonthlyExcel($expenses, $monthName, $filenameBase, $totalAmount, $totalCount, $categoryBreakdown);
        }

        // Default PDF
        $pdf = Pdf::loadView('reports.monthly_pdf', compact(
            'expenses',
            'monthName',
            'totalAmount',
            'totalCount',
            'categoryBreakdown',
            'startDate',
            'endDate'
        ));

        return $pdf->download($filenameBase . '.pdf');
    }

    private function generateTargetCSV($expenses, $target, $filenameBase, $totalSpent, $targetAmount, $status)
    {
        $filename = $filenameBase . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($expenses, $target, $totalSpent, $targetAmount, $status) {
            $file = fopen('php://output', 'w');

            // Summary header
            fputcsv($file, ['TARGET PERIOD REPORT']);
            fputcsv($file, ['Start Date', $target->start_date->format('Y-m-d')]);
            fputcsv($file, ['End Date', $target->end_date->format('Y-m-d')]);
            fputcsv($file, ['Target Amount', $targetAmount]);
            fputcsv($file, ['Total Spent', $totalSpent]);
            fputcsv($file, ['Remaining', max(0, $targetAmount - $totalSpent)]);
            fputcsv($file, ['Status', $status]);
            fputcsv($file, []);

            // Expense data
            fputcsv($file, ['Date', 'Title', 'Category', 'Amount', 'Notes']);
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('Y-m-d'),
                    $expense->title,
                    $expense->category,
                    $expense->amount,
                    $expense->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateTargetExcel($expenses, $target, $filenameBase, $totalSpent, $targetAmount, $status)
    {
        $filename = $filenameBase . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $html = '<table border="1" cellpadding="6" cellspacing="0">';
        $html .= '<tr><th colspan="5"><h2>Target Period Report</h2></th></tr>';
        $html .= '<tr><td><strong>Start Date:</strong></td><td colspan="4">' . $target->start_date->format('F d, Y') . '</td></tr>';
        $html .= '<tr><td><strong>End Date:</strong></td><td colspan="4">' . $target->end_date->format('F d, Y') . '</td></tr>';
        $html .= '<tr><td><strong>Target Amount:</strong></td><td colspan="4">RWF ' . number_format($targetAmount, 0) . '</td></tr>';
        $html .= '<tr><td><strong>Total Spent:</strong></td><td colspan="4">RWF ' . number_format($totalSpent, 0) . '</td></tr>';
        $html .= '<tr><td><strong>Status:</strong></td><td colspan="4">' . $status . '</td></tr>';
        $html .= '<tr><td colspan="5">&nbsp;</td></tr>';

        // Expenses
        $html .= '<tr><th colspan="5">Expense Details</th></tr>';
        $html .= '<tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th>Notes</th></tr>';
        foreach ($expenses as $expense) {
            $html .= '<tr>';
            $html .= '<td>' . e($expense->date->format('Y-m-d')) . '</td>';
            $html .= '<td>' . e($expense->title) . '</td>';
            $html .= '<td>' . e($expense->category) . '</td>';
            $html .= '<td>RWF ' . number_format($expense->amount, 0) . '</td>';
            $html .= '<td>' . e($expense->notes ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return response("\xEF\xBB\xBF" . $html, 200, $headers);
    }

    private function generateMonthlyCSV($expenses, $monthName, $filenameBase, $totalAmount, $totalCount, $categoryBreakdown)
    {
        $filename = $filenameBase . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($expenses, $monthName, $totalAmount, $totalCount, $categoryBreakdown) {
            $file = fopen('php://output', 'w');

            // Summary
            fputcsv($file, ['MONTHLY REPORT']);
            fputcsv($file, ['Month', $monthName]);
            fputcsv($file, ['Total Expenses', $totalCount]);
            fputcsv($file, ['Total Amount', $totalAmount]);
            fputcsv($file, ['Average per Expense', $totalCount > 0 ? round($totalAmount / $totalCount, 2) : 0]);
            fputcsv($file, []);

            // Category breakdown
            fputcsv($file, ['CATEGORY BREAKDOWN']);
            fputcsv($file, ['Category', 'Count', 'Total', 'Percentage']);
            foreach ($categoryBreakdown as $category => $data) {
                fputcsv($file, [$category, $data['count'], $data['total'], $data['percentage'] . '%']);
            }
            fputcsv($file, []);

            // Expenses
            fputcsv($file, ['EXPENSES']);
            fputcsv($file, ['Date', 'Title', 'Category', 'Amount', 'Notes']);
            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('Y-m-d'),
                    $expense->title,
                    $expense->category,
                    $expense->amount,
                    $expense->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateMonthlyExcel($expenses, $monthName, $filenameBase, $totalAmount, $totalCount, $categoryBreakdown)
    {
        $filename = $filenameBase . '.xls';
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'max-age=0',
        ];

        $html = '<table border="1" cellpadding="6" cellspacing="0">';
        $html .= '<tr><th colspan="5"><h2>Monthly Report - ' . e($monthName) . '</h2></th></tr>';
        $html .= '<tr><td><strong>Total Expenses:</strong></td><td>' . $totalCount . '</td>';
        $html .= '<td><strong>Total Amount:</strong></td><td colspan="2">RWF ' . number_format($totalAmount, 0) . '</td></tr>';
        $html .= '<tr><td colspan="5">&nbsp;</td></tr>';

        // Category breakdown
        $html .= '<tr><th colspan="5">Category Breakdown</th></tr>';
        $html .= '<tr><th>Category</th><th>Count</th><th>Total</th><th colspan="2">Percentage</th></tr>';
        foreach ($categoryBreakdown as $category => $data) {
            $html .= '<tr>';
            $html .= '<td>' . e($category) . '</td>';
            $html .= '<td>' . $data['count'] . '</td>';
            $html .= '<td>RWF ' . number_format($data['total'], 0) . '</td>';
            $html .= '<td colspan="2">' . $data['percentage'] . '%</td>';
            $html .= '</tr>';
        }
        $html .= '<tr><td colspan="5">&nbsp;</td></tr>';

        // Expenses
        $html .= '<tr><th colspan="5">Expenses</th></tr>';
        $html .= '<tr><th>Date</th><th>Title</th><th>Category</th><th>Amount</th><th>Notes</th></tr>';
        foreach ($expenses as $expense) {
            $html .= '<tr>';
            $html .= '<td>' . e($expense->date->format('Y-m-d')) . '</td>';
            $html .= '<td>' . e($expense->title) . '</td>';
            $html .= '<td>' . e($expense->category) . '</td>';
            $html .= '<td>RWF ' . number_format($expense->amount, 0) . '</td>';
            $html .= '<td>' . e($expense->notes ?? '') . '</td>';
            $html .= '</tr>';
        }
        $html .= '</table>';

        return response("\xEF\xBB\xBF" . $html, 200, $headers);
    }
}
