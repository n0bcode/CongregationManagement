<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audit Log Report</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #d4a574;
            padding-bottom: 15px;
        }

        .header h1 {
            color: #5c4a3a;
            font-size: 24px;
            margin: 0 0 10px 0;
        }

        .header .subtitle {
            color: #7a6a5a;
            font-size: 12px;
        }

        .metadata {
            background-color: #f5f5f0;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }

        .metadata table {
            width: 100%;
        }

        .metadata td {
            padding: 5px;
        }

        .metadata .label {
            font-weight: bold;
            color: #5c4a3a;
            width: 150px;
        }

        .checksum-box {
            background-color: #fff8e1;
            border: 2px solid #d4a574;
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }

        .checksum-box h3 {
            color: #5c4a3a;
            margin: 0 0 10px 0;
            font-size: 14px;
        }

        .checksum {
            font-family: 'Courier New', monospace;
            font-size: 9px;
            word-break: break-all;
            background-color: white;
            padding: 10px;
            border-radius: 3px;
        }

        table.logs {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table.logs th {
            background-color: #5c4a3a;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }

        table.logs td {
            padding: 6px 8px;
            border-bottom: 1px solid #e0e0e0;
            font-size: 9px;
        }

        table.logs tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-created {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-updated {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .badge-deleted {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge-transferred {
            background-color: #e2d9f3;
            color: #4a148c;
        }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e0e0e0;
            text-align: center;
            font-size: 8px;
            color: #999;
        }

        .warning {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 10px;
            margin: 20px 0;
            font-size: 9px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>Audit Log Report</h1>
        <div class="subtitle">Tamper-Evident System Activity Report</div>
    </div>

    <div class="metadata">
        <table>
            <tr>
                <td class="label">Report Generated:</td>
                <td>{{ $report['generated_at'] }}</td>
            </tr>
            <tr>
                <td class="label">Date Range:</td>
                <td>
                    @if ($report['start_date'] && $report['end_date'])
                        {{ $report['start_date'] }} to {{ $report['end_date'] }}
                    @else
                        All Records
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Total Entries:</td>
                <td>{{ $report['total_count'] }}</td>
            </tr>
        </table>
    </div>

    <div class="checksum-box">
        <h3>🔒 Report Integrity Checksum</h3>
        <div class="checksum">{{ $report['report_checksum'] }}</div>
        <p style="margin: 10px 0 0 0; font-size: 8px; color: #666;">
            This SHA-256 checksum can be used to verify that the report has not been tampered with.
            Any modification to the report data will result in a different checksum.
        </p>
    </div>

    <div class="warning">
        <strong>⚠️ Important:</strong> This report contains tamper-evident checksums. Each entry has been
        cryptographically hashed to ensure data integrity. Any modification to the audit log data will be detectable
        through checksum verification.
    </div>

    <table class="logs">
        <thead>
            <tr>
                <th style="width: 15%;">Date & Time</th>
                <th style="width: 12%;">User</th>
                <th style="width: 10%;">Action</th>
                <th style="width: 38%;">Description</th>
                <th style="width: 25%;">Checksum</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($report['entries'] as $entry)
                <tr>
                    <td>{{ $entry['log']->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>{{ $entry['log']->user?->name ?? 'System' }}</td>
                    <td>
                        <span
                            class="badge
                            @if ($entry['log']->action === 'created') badge-created
                            @elseif($entry['log']->action === 'updated') badge-updated
                            @elseif($entry['log']->action === 'deleted') badge-deleted
                            @elseif($entry['log']->action === 'transferred') badge-transferred @endif">
                            {{ ucfirst($entry['log']->action) }}
                        </span>
                    </td>
                    <td>{{ $entry['log']->description }}</td>
                    <td style="font-family: 'Courier New', monospace; font-size: 7px; word-break: break-all;">
                        {{ substr($entry['checksum'], 0, 32) }}...
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>This report was generated by the Congregation Management System</p>
        <p>Report ID: {{ $report['report_checksum'] }}</p>
    </div>
</body>

</html>
