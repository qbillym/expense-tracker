<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Target Report</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            background: linear-gradient(135deg, #0d4f2f 0%, #1a5f3f 100%);
            color: white;
            padding: 20px;
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .summary-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .summary-label {
            font-weight: bold;
            color: #0d4f2f;
        }
        .status-completed {
            color: #198754;
            font-weight: bold;
        }
        .status-exceeded {
            color: #dc3545;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th {
            background: #0d4f2f;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }
        td {
            padding: 8px 10px;
            border-bottom: 1px solid #dee2e6;
        }
        tr:nth-child(even) {
            background: #f8f9fa;
        }
        .category-section {
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .category-title {
            font-size: 14px;
            font-weight: bold;
            color: #0d4f2f;
            margin-bottom: 10px;
            border-bottom: 2px solid #0d4f2f;
            padding-bottom: 5px;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 10px;
            color: #6c757d;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎯 Budget Target Report</h1>
        <p>Period: {{ $target->start_date->format('F d, Y') }} - {{ $target->end_date->format('F d, Y') }}</p>
    </div>

    <div class="summary-box">
        <div class="summary-row">
            <span class="summary-label">Target Amount:</span>
            <span>RWF {{ number_format($targetAmount, 0) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Spent:</span>
            <span>RWF {{ number_format($totalSpent, 0) }}</span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Remaining/Over:</span>
            <span>
                @if($isOverBudget)
                    RWF {{ number_format($totalSpent - $targetAmount, 0) }} over budget
                @else
                    RWF {{ number_format(max(0, $targetAmount - $totalSpent), 0) }} remaining
                @endif
            </span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Status:</span>
            <span class="status-{{ $isOverBudget ? 'exceeded' : 'completed' }}">
                {{ $status }}
            </span>
        </div>
        <div class="summary-row">
            <span class="summary-label">Total Expenses:</span>
            <span>{{ $expenses->count() }}</span>
        </div>
    </div>

    @if($categoryBreakdown->isNotEmpty())
    <div class="category-section">
        <div class="category-title">Spending by Category</div>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Count</th>
                    <th>Total Amount</th>
                    <th>Percentage</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categoryBreakdown as $category => $data)
                <tr>
                    <td>{{ $category }}</td>
                    <td>{{ $data['count'] }}</td>
                    <td>RWF {{ number_format($data['total'], 0) }}</td>
                    <td>{{ $totalSpent > 0 ? round(($data['total'] / $totalSpent) * 100, 1) : 0 }}%</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="category-title">Expense Details</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Category</th>
                <th>Amount</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr>
                <td>{{ $expense->date->format('M d, Y') }}</td>
                <td>{{ $expense->title }}</td>
                <td>{{ $expense->category }}</td>
                <td>RWF {{ number_format($expense->amount, 0) }}</td>
                <td>{{ $expense->notes ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="5" style="text-align: center; color: #6c757d;">No expenses recorded for this period.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Generated on {{ now()->format('F d, Y \a\t h:i A') }} | Expense Tracker</p>
    </div>
</body>
</html>
