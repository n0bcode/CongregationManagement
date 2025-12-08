<!DOCTYPE html>
<html>
<head>
    <title>Expense Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1 { text-align: center; }
    </style>
</head>
<body>
    <h1>Expense Report</h1>
    <p>Generated on: {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th>Project</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td>{{ $expense->date->format('Y-m-d') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>{{ $expense->description }}</td>
                    <td>{{ $expense->project->name ?? 'N/A' }}</td>
                    <td>${{ number_format($expense->amount_in_dollars, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
