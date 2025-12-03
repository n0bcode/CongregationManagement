<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Financial Report - {{ $report['period']['month_name'] }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11pt;
            line-height: 1.6;
            color: #1e293b;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #d97706;
        }
        
        .header h1 {
            font-size: 24pt;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .header h2 {
            font-size: 16pt;
            color: #64748b;
            font-weight: normal;
        }
        
        .header .period {
            font-size: 14pt;
            color: #d97706;
            margin-top: 10px;
            font-weight: bold;
        }
        
        .summary-box {
            background-color: #fef3c7;
            border: 2px solid #d97706;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .summary-grid {
            display: table;
            width: 100%;
        }
        
        .summary-item {
            display: table-row;
        }
        
        .summary-label {
            display: table-cell;
            font-weight: bold;
            padding: 8px 0;
            width: 60%;
        }
        
        .summary-value {
            display: table-cell;
            text-align: right;
            padding: 8px 0;
            font-size: 13pt;
        }
        
        .summary-value.highlight {
            color: #d97706;
            font-weight: bold;
            font-size: 16pt;
        }
        
        .section-title {
            font-size: 16pt;
            color: #1e293b;
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #f1f5f9;
        }
        
        table th {
            text-align: left;
            padding: 12px 8px;
            font-weight: bold;
            border-bottom: 2px solid #cbd5e1;
        }
        
        table td {
            padding: 10px 8px;
            border-bottom: 1px solid #e2e8f0;
        }
        
        table tr:last-child td {
            border-bottom: none;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .amount {
            font-weight: bold;
            color: #0f172a;
        }
        
        .category-bar {
            background-color: #e2e8f0;
            height: 20px;
            border-radius: 4px;
            overflow: hidden;
            margin-top: 5px;
        }
        
        .category-bar-fill {
            background-color: #d97706;
            height: 100%;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            font-size: 9pt;
            color: #64748b;
        }
        
        .page-break {
            page-break-after: always;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
        }
        
        .badge-locked {
            background-color: #fee2e2;
            color: #991b1b;
        }
    </style>
</head>
<body>
    {{-- Header --}}
    <div class="header">
        <h1>Monthly Financial Report</h1>
        <h2>{{ $communityName }}</h2>
        <div class="period">{{ $report['period']['month_name'] }}</div>
    </div>

    {{-- Summary --}}
    <div class="summary-box">
        <div class="summary-grid">
            <div class="summary-item">
                <div class="summary-label">Total Expenses:</div>
                <div class="summary-value highlight">${{ number_format($report['summary']['total_amount_dollars'], 2) }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Number of Transactions:</div>
                <div class="summary-value">{{ $report['summary']['total_count'] }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Average Expense:</div>
                <div class="summary-value">${{ number_format($report['summary']['average_expense_dollars'], 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Expenses by Category --}}
    <div class="section-title">Expenses by Category</div>
    <table>
        <thead>
            <tr>
                <th>Category</th>
                <th class="text-center">Count</th>
                <th class="text-right">Total</th>
                <th class="text-right">Average</th>
                <th class="text-right">% of Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['by_category'] as $category)
                <tr>
                    <td>{{ $category['category'] }}</td>
                    <td class="text-center">{{ $category['count'] }}</td>
                    <td class="text-right amount">${{ number_format($category['total_dollars'], 2) }}</td>
                    <td class="text-right">${{ number_format($category['average_dollars'], 2) }}</td>
                    <td class="text-right">{{ number_format($category['percentage'], 1) }}%</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Daily Breakdown --}}
    @if($report['daily_breakdown']->count() > 0)
        <div class="section-title">Daily Breakdown</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th class="text-center">Transactions</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($report['daily_breakdown'] as $day)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($day['date'])->format('l, F j, Y') }}</td>
                        <td class="text-center">{{ $day['count'] }}</td>
                        <td class="text-right amount">${{ number_format($day['total'] / 100, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    {{-- Page Break --}}
    <div class="page-break"></div>

    {{-- Detailed Expense List --}}
    <div class="section-title">Detailed Expense List</div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Category</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report['expenses'] as $expense)
                <tr>
                    <td>{{ $expense->date->format('M j, Y') }}</td>
                    <td>{{ $expense->category }}</td>
                    <td>
                        {{ Str::limit($expense->description, 60) }}
                        @if($expense->is_locked)
                            <span class="badge badge-locked">LOCKED</span>
                        @endif
                    </td>
                    <td class="text-right amount">${{ number_format($expense->amount / 100, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>Generated on {{ now()->format('F j, Y \a\t g:i A') }}</p>
        <p>{{ $communityName }} - Congregation Management System</p>
    </div>
</body>
</html>
