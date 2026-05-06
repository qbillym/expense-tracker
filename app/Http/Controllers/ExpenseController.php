<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index()
    {
        return view('expenses.index', [
            'expenses' => auth()->user()->expenses()->latest()->get(),
        ]);
    }

    public function create()
    {
        return view('expenses.create');
    }

    public function store(Request $request)
    {
        $entryMethod = $request->input('entry_method', 'manual');
        $isMobileMoney = $entryMethod === 'mobile' || $request->filled('mobile_money_message');

        if ($isMobileMoney) {
            // Validate mobile money input
            $validated = $request->validate([
                'entry_method' => ['required', 'string', 'in:manual,mobile'],
                'mobile_money_message' => ['required', 'string'],
                'category' => ['required', 'string', 'max:255'],
                'notes' => ['nullable', 'string'],
                'amount' => ['nullable', 'numeric'],
                'title' => ['nullable', 'string', 'max:255'],
                'detected_balance' => ['nullable', 'numeric'],
            ]);

            // Parse the mobile money message
            $parsedData = Expense::parseMobileMoneyMessage($validated['mobile_money_message']);

            // Merge parsed data with form data and make sure blank values do not override parsed defaults
            $expenseData = [
                'mobile_money_message' => $validated['mobile_money_message'],
                'title' => trim($validated['title'] ?? '') ?: $parsedData['title'],
                'amount' => $validated['amount'] ?? $parsedData['amount'],
                'category' => trim($validated['category'] ?? '') ?: $parsedData['category'],
                'date' => now()->toDateString(),
                'notes' => trim($validated['notes'] ?? '') ?: null,
                'detected_balance' => $validated['detected_balance'] ?? $parsedData['balance'],
            ];

            // Validate that we have required data
            if ($expenseData['amount'] === null) {
                return back()->withErrors(['mobile_money_message' => 'Could not detect amount from the message. Please enter it manually.'])
                           ->withInput();
            }

        } else {
            // Standard manual entry validation
            $validated = $request->validate([
                'title' => ['required', 'string', 'max:255'],
                'amount' => ['required', 'numeric', 'min:0'],
                'category' => ['required', 'string', 'max:255'],
                'date' => ['required', 'date'],
                'notes' => ['nullable', 'string'],
            ]);

            $expenseData = $validated;
        }

        auth()->user()->expenses()->create($expenseData);

        $budgetAlert = null;
        $user = auth()->user();

        // Check if we have an active target
        if ($user->hasActiveBudget()) {
            $activeTarget = $user->activeBudgetTarget();
            $spent = $user->budgetSpent();
            $targetAmount = $activeTarget->target_amount;

            // If spending has reached or exceeded target, lock it immediately
            if ($spent >= $targetAmount) {
                $user->lockCurrentTarget();
                $budgetAlert = "🎉 TARGET COMPLETED & LOCKED! You've spent RWF " . number_format($spent, 0) .
                              " of your RWF " . number_format($targetAmount, 0) .
                              " target. You can now create a new target from the dashboard.";
            } else {
                $remaining = $targetAmount - $spent;
                $topCategory = $user->budgetTopCategory();
                $budgetAlert = "Expense added! You have RWF " . number_format($remaining, 0) .
                              " remaining to reach your target of RWF " . number_format($targetAmount, 0) . ".";

                if ($topCategory) {
                    $budgetAlert .= " Most spending in: " . $topCategory . ".";
                }
            }
        }

        $redirect = redirect()->route('expenses.index')->with('success', 'Expense saved successfully.');
        if ($budgetAlert) {
            $redirect = $redirect->with('budgetAlert', $budgetAlert);
        }

        return $redirect;
    }

    public function edit(Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        return view('expenses.edit', ['expense' => $expense]);
    }

    public function update(Request $request, Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric'],
            'category' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
        ]);

        $expense->update($validated);

        // Refresh budget locks after update
        auth()->user()->refreshBudgetLocks();

        return redirect()->route('expenses.index')->with('success', 'Expense updated successfully.');
    }

    public function destroy(Expense $expense)
    {
        if ($expense->user_id !== auth()->id()) {
            abort(403);
        }

        $expense->delete();

        return redirect()->route('expenses.index')->with('success', 'Expense deleted successfully.');
    }
}
