<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Expense;
use App\Models\BudgetTarget;
use App\Models\ActivityLog;

class AdminController extends Controller
{
    public function dashboard()
    {
        $users = User::where('is_admin', false)->get();
        $totalUsers = User::count();
        $totalExpenses = Expense::count();
        $totalAmount = Expense::sum('amount');
        $activeTargets = BudgetTarget::whereNull('locked_at')->count();
        
        // Get category statistics
        $categoryStats = Expense::groupBy('category')
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as total')
            ->orderByDesc('total')
            ->get();
        
        // Get most spent categories
        $mostSpentCategories = $categoryStats->take(5);
        
        // Get recent expenses
        $recentExpenses = Expense::with('user')
            ->orderByDesc('date')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'users',
            'totalUsers',
            'totalExpenses',
            'totalAmount',
            'activeTargets',
            'categoryStats',
            'mostSpentCategories',
            'recentExpenses'
        ));
    }

    public function users(Request $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $search = $request->input('search');

        $query = User::where('is_admin', false)
            ->withCount(['expenses'])
            ->withSum('expenses', 'amount');

        // Apply search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Apply sorting
        if (in_array($sortBy, ['name', 'email', 'created_at', 'expenses_count', 'expenses_sum_amount'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('created_at');
        }

        $users = $query->paginate(20);

        return view('admin.users', compact('users', 'sortBy', 'sortOrder', 'search'));
    }

    public function targets(Request $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = BudgetTarget::with('user');

        // Apply sorting
        if (in_array($sortBy, ['created_at', 'target_amount', 'start_date', 'end_date'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('created_at');
        }

        $targets = $query->paginate(20);

        return view('admin.targets', compact('targets', 'sortBy', 'sortOrder'));
    }

    public function categories(Request $request)
    {
        $sortBy = $request->input('sort_by', 'total');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = Expense::groupBy('category')
            ->selectRaw('category, COUNT(*) as count, SUM(amount) as total, AVG(amount) as average');

        // Apply sorting
        if (in_array($sortBy, ['category', 'count', 'total', 'average'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('total');
        }

        $categoryStats = $query->get();

        return view('admin.categories', compact('categoryStats', 'sortBy', 'sortOrder'));
    }

    public function activityLogs(Request $request)
    {
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $action = $request->input('action', 'all');
        $userId = $request->input('user_id', 'all');

        $query = ActivityLog::with('user');

        // Apply filters
        if ($action !== 'all') {
            $query->where('action', $action);
        }

        if ($userId !== 'all') {
            $query->where('user_id', $userId);
        }

        // Apply sorting
        if (in_array($sortBy, ['created_at', 'action', 'user_id'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('created_at');
        }

        $logs = $query->paginate(50);
        $users = User::where('is_admin', false)->orderBy('name')->get();
        $actions = ActivityLog::distinct()->pluck('action')->sort();

        return view('admin.activity-logs', compact('logs', 'users', 'actions', 'sortBy', 'sortOrder', 'action', 'userId'));
    }

    public function downloadUsers(Request $request)
    {
        $format = $request->input('format', 'excel');
        $search = $request->input('search');
        $sortBy = $request->input('sort_by', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');

        $query = User::where('is_admin', false)
            ->withCount(['expenses'])
            ->withSum('expenses', 'amount');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if (in_array($sortBy, ['name', 'email', 'created_at', 'expenses_count', 'expenses_sum_amount'])) {
            $query->orderBy($sortBy, $sortOrder === 'asc' ? 'asc' : 'desc');
        } else {
            $query->orderByDesc('created_at');
        }

        $users = $query->get();

        $filename = 'admin_users_report_' . date('Y-m-d_H-i-s');

        if ($format === 'csv') {
            return $this->generateUsersCSV($users, $filename);
        }

        if ($format === 'pdf') {
            return $this->generateUsersPDF($users, $filename);
        }

        // Default Excel
        return $this->generateUsersExcel($users, $filename);
    }

    private function generateUsersCSV($users, $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Name', 'Email', 'Registration Date', 'Total Expenses', 'Total Amount (RWF)', 'Average Expense (RWF)']);
            
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->created_at->format('Y-m-d H:i:s'),
                    $user->expenses_count ?? 0,
                    number_format($user->expenses_sum_amount ?? 0, 0),
                    number_format(($user->expenses_sum_amount ?? 0) / max(1, $user->expenses_count ?? 1), 0)
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function generateUsersExcel($users, $filename)
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.xls"',
            'Cache-Control' => 'max-age=0',
        ];

        $html = '<table border="1" cellpadding="6" cellspacing="0">';
        $html .= '<tr><th colspan="6"><h2>Admin Users Report</h2></th></tr>';
        $html .= '<tr><th>Name</th><th>Email</th><th>Registration Date</th><th>Total Expenses</th><th>Total Amount (RWF)</th><th>Average Expense (RWF)</th></tr>';

        foreach ($users as $user) {
            $html .= '<tr>';
            $html .= '<td>' . e($user->name) . '</td>';
            $html .= '<td>' . e($user->email) . '</td>';
            $html .= '<td>' . $user->created_at->format('Y-m-d H:i:s') . '</td>';
            $html .= '<td>' . ($user->expenses_count ?? 0) . '</td>';
            $html .= '<td>RWF ' . number_format($user->expenses_sum_amount ?? 0, 0) . '</td>';
            $html .= '<td>RWF ' . number_format(($user->expenses_sum_amount ?? 0) / max(1, $user->expenses_count ?? 1), 0) . '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return response("\xEF\xBB\xBF" . $html, 200, $headers);
    }

    private function generateUsersPDF($users, $filename)
    {
        $pdf = Pdf::loadView('admin.reports.users', compact('users'));
        return $pdf->download($filename . '.pdf');
    }
}
