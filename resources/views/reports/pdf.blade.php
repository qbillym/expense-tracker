<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Expense Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d4f2f;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d4f2f;
            margin: 0;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 5px;
        }
        .stat-box h3 {
            margin: 0 0 5px 0;
            color: #0d4f2f;
            font-size: 24px;
        }
        .stat-box p {
            margin: 0;
            color: #666;
            font-size: 11px;
        }
        .section-title {
            color: #0d4f2f;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin: 30px 0 15px 0;
            font-size: 16px;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #0d4f2f;
            color: white;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            text-align: center;
            color: #666;
            font-size: 10px;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 10px;
            color: white;
        }
        .badge-food { background-color: #dc3545; }
        .badge-transport { background-color: #0d6efd; }
        .badge-shopping { background-color: #198754; }
        .badge-entertainment { background-color: #fd7e14; }
        .badge-healthcare { background-color: #6f42c1; }
        .badge-education { background-color: #0dcaf0; }
        .badge-utilities { background-color: #ffc107; color: #333; }
        .badge-bills { background-color: #adb5bd; color: #333; }
        .badge-personal { background-color: #e83e8c; }
        .badge-travel { background-color: #6610f2; }
        .badge-gifts { background-color: #20c997; }
        .badge-business { background-color: #17a2b8; }
        .badge-other { background-color: #6c757d; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Expense Tracker Report</h1>
        <p>Generated on {{ date('F j, Y, g:i A') }}</p>
        @if($startDate || $endDate || $category)
            <p>
                @if($startDate) From: {{ date('M d, Y', strtotime($startDate)) }} @endif
                @if($endDate) To: {{ date('M d, Y', strtotime($endDate)) }} @endif
                @if($category && $category !== 'all') Category: {{ $category }} @endif
            </p>
        @endif
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <h3>{{ $totalExpenses }}</h3>
            <p>Total Expenses</p>
        </div>
        <div class="stat-box">
            <h3>RWF {{ number_format($totalAmount, 0) }}</h3>
            <p>Total Spent</p>
        </div>
        <div class="stat-box">
            <h3>RWF {{ number_format($averageExpense, 0) }}</h3>
            <p>Average Expense</p>
        </div>
        <div class="stat-box">
            <h3>{{ $categoryBreakdown->count() }}</h3>
            <p>Categories</p>
        </div>
    </div>

    <div class="section-title">Category Breakdown</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-right">Count</th>
                <th class="text-right">Total Amount</th>
                <th class="text-right">Average</th>
                <th class="text-right">Percentage</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categoryBreakdown as $category => $data)
                <tr>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '-', str_replace('&', '', $category))) }}">
                            {{ $category }}
                        </span>
                    </td>
                    <td class="text-right">{{ $data['count'] }}</td>
                    <td class="text-right">RWF {{ number_format($data['total'], 0) }}</td>
                    <td class="text-right">RWF {{ number_format($data['average'], 0) }}</td>
                    <td class="text-right">{{ $data['percentage'] }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">Recent Expenses</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Title</th>
                <th>Category</th>
                <th class="text-right">Amount</th>
                <th>Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses->take(50) as $expense)
                <tr>
                    <td>{{ $expense->date->format('M d, Y') }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>
                        <span class="badge badge-{{ strtolower(str_replace(' ', '-', str_replace('&', '', $expense->category))) }}">
                            {{ $expense->category }}
                        </span>
                    </td>
                    <td class="text-right">RWF {{ number_format($expense->amount, 0) }}</td>
                    <td class="text-center">
                        @if($expense->mobile_money_message)
                            <span class="badge badge-info">Mobile Money</span>
                        @else
                            <span class="badge badge-secondary">Manual</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($expenses->count() > 50)
        <p class="text-center text-muted">
            <em>Showing 50 of {{ $expenses->count() }} expenses</em>
        </p>
    @endif

    <div class="footer">
        <p>Generated by Expense Tracker - Smart Financial Management</p>
    </div>
</body>
</html>
