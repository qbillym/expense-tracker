<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Users Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #0d4f2f;
            margin-bottom: 10px;
        }
        .header p {
            color: #666;
            margin: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #0d4f2f;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .summary {
            margin-top: 30px;
            padding: 15px;
            background-color: #f0f8f0;
            border-left: 4px solid #0d4f2f;
        }
        .summary h3 {
            margin-top: 0;
            color: #0d4f2f;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Admin Users Report</h1>
        <p>Generated on: {{ now()->format('F d, Y H:i:s') }}</p>
        <p>Total Users: {{ $users->count() }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Registration Date</th>
                <th>Total Expenses</th>
                <th>Total Amount (RWF)</th>
                <th>Average Expense (RWF)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $user->expenses_count ?? 0 }}</td>
                    <td>{{ number_format($user->expenses_sum_amount ?? 0, 0) }}</td>
                    <td>{{ number_format(($user->expenses_sum_amount ?? 0) / max(1, $user->expenses_count ?? 1), 0) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary Statistics</h3>
        <p><strong>Total Users:</strong> {{ $users->count() }}</p>
        <p><strong>Total Expenses:</strong> {{ $users->sum('expenses_count') ?? 0 }}</p>
        <p><strong>Total Amount Spent:</strong> RWF {{ number_format($users->sum('expenses_sum_amount') ?? 0, 0) }}</p>
        <p><strong>Average Expenses per User:</strong> {{ number_format(($users->sum('expenses_count') ?? 0) / max(1, $users->count()), 1) }}</p>
        <p><strong>Average Spending per User:</strong> RWF {{ number_format(($users->sum('expenses_sum_amount') ?? 0) / max(1, $users->count()), 0) }}</p>
    </div>
</body>
</html>
