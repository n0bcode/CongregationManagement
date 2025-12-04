<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Demographic Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 12px;
            color: #1e293b;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #d97706;
        }
        .header h1 {
            color: #d97706;
            font-size: 28px;
            margin: 0 0 10px 0;
        }
        .header p {
            color: #64748b;
            margin: 5px 0;
        }
        .summary-box {
            background: #fef3c7;
            border: 2px solid #d97706;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            margin-bottom: 30px;
        }
        .summary-box h2 {
            color: #92400e;
            font-size: 16px;
            margin: 0 0 10px 0;
        }
        .summary-box .number {
            color: #d97706;
            font-size: 48px;
            font-weight: bold;
            margin: 0;
        }
        .section {
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .section h3 {
            color: #1e293b;
            font-size: 18px;
            margin: 0 0 15px 0;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        .chart-item {
            margin-bottom: 15px;
        }
        .chart-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 13px;
        }
        .chart-label .name {
            font-weight: 600;
            color: #475569;
        }
        .chart-label .value {
            font-weight: bold;
            color: #1e293b;
        }
        .chart-bar {
            background: #e2e8f0;
            height: 20px;
            border-radius: 4px;
            overflow: hidden;
        }
        .chart-bar-fill {
            height: 100%;
            background: #d97706;
            border-radius: 4px;
        }
        .chart-bar-fill.green {
            background: #059669;
        }
        .chart-bar-fill.blue {
            background: #2563eb;
        }
        .chart-bar-fill.purple {
            background: #7c3aed;
        }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 11px;
        }
        .grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .grid-row {
            display: table-row;
        }
        .grid-cell {
            display: table-cell;
            width: 50%;
            padding: 10px;
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Demographic Report</h1>
        <p>Congregation Management System</p>
        <p>Generated on {{ now()->format('F d, Y \a\t g:i A') }}</p>
    </div>

    <div class="summary-box">
        <h2>Total Members</h2>
        <p class="number">{{ $totalMembers }}</p>
    </div>

    <div class="grid">
        <div class="grid-row">
            <div class="grid-cell">
                <div class="section">
                    <h3>Age Distribution</h3>
                    @foreach($ageDistribution as $group => $count)
                        <div class="chart-item">
                            <div class="chart-label">
                                <span class="name">{{ $group }}</span>
                                <span class="value">{{ $count }} ({{ $totalMembers > 0 ? round($count / $totalMembers * 100) : 0 }}%)</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-bar-fill" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid-cell">
                <div class="section">
                    <h3>Status Distribution</h3>
                    @foreach($statusDistribution as $statusName => $count)
                        <div class="chart-item">
                            <div class="chart-label">
                                <span class="name">{{ ucfirst($statusName) }}</span>
                                <span class="value">{{ $count }} ({{ $totalMembers > 0 ? round($count / $totalMembers * 100) : 0 }}%)</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-bar-fill green" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid-row">
            <div class="grid-cell">
                <div class="section">
                    <h3>Community Distribution</h3>
                    @foreach($communityDistribution as $communityName => $count)
                        <div class="chart-item">
                            <div class="chart-label">
                                <span class="name">{{ $communityName }}</span>
                                <span class="value">{{ $count }} ({{ $totalMembers > 0 ? round($count / $totalMembers * 100) : 0 }}%)</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-bar-fill blue" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid-cell">
                <div class="section">
                    <h3>Formation Stages</h3>
                    @foreach($formationStages as $stage => $count)
                        <div class="chart-item">
                            <div class="chart-label">
                                <span class="name">{{ ucfirst(str_replace('_', ' ', $stage)) }}</span>
                                <span class="value">{{ $count }} ({{ $totalMembers > 0 ? round($count / $totalMembers * 100) : 0 }}%)</span>
                            </div>
                            <div class="chart-bar">
                                <div class="chart-bar-fill purple" style="width: {{ $totalMembers > 0 ? ($count / $totalMembers * 100) : 0 }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>This report is confidential and intended for authorized personnel only.</p>
        <p>Congregation Management System - {{ now()->year }}</p>
    </div>
</body>
</html>
