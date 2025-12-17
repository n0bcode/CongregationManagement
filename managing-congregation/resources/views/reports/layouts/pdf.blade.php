<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Directory Report')</title>
    <style>
        @page {
            margin: 20mm;
        }
        
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10pt;
            line-height: 1.4;
            color: #333;
        }
        
        h1 {
            text-align: center;
            font-size: 18pt;
            margin-bottom: 5mm;
            color: #1a56db;
            border-bottom: 2px solid #1a56db;
            padding-bottom: 3mm;
        }
        
        h2 {
            font-size: 14pt;
            margin-top: 8mm;
            margin-bottom: 3mm;
            color: #1a56db;
        }
        
        h3 {
            font-size: 12pt;
            margin-top: 5mm;
            margin-bottom: 2mm;
            color: #374151;
        }
        
        .header {
            text-align: center;
            margin-bottom: 10mm;
        }
        
        .header .logo {
            font-size: 16pt;
            font-weight: bold;
            color: #1a56db;
        }
        
        .header .subtitle {
            font-size: 12pt;
            color: #6b7280;
            margin-top: 2mm;
        }
        
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 8pt;
            color: #9ca3af;
            border-top: 1px solid #e5e7eb;
            padding-top: 2mm;
        }
        
        .page-number:before {
            content: "Page " counter(page);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 5mm;
        }
        
        table th {
            background-color: #f3f4f6;
            padding: 3mm;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #d1d5db;
        }
        
        table td {
            padding: 2mm 3mm;
            border-bottom: 1px solid #e5e7eb;
        }
        
        table tr:hover {
            background-color: #f9fafb;
        }
        
        .community-block {
            margin-bottom: 8mm;
            page-break-inside: avoid;
        }
        
        .community-header {
            background-color: #eff6ff;
            padding: 3mm;
            margin-bottom: 3mm;
            border-left: 4px solid #1a56db;
        }
        
        .community-code {
            font-weight: bold;
            font-size: 12pt;
            color: #1a56db;
        }
        
        .community-name {
            font-size: 11pt;
            color: #374151;
        }
        
        .community-info {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 1mm;
        }
        
        .member-item {
            margin-bottom: 3mm;
            padding-left: 5mm;
        }
        
        .member-name {
            font-weight: bold;
            color: #111827;
        }
        
        .member-role {
            display: inline-block;
            background-color: #dbeafe;
            color: #1e40af;
            padding: 1mm 2mm;
            border-radius: 2mm;
            font-size: 8pt;
            font-weight: bold;
            margin-right: 2mm;
        }
        
        .member-details {
            font-size: 9pt;
            color: #6b7280;
            margin-top: 1mm;
        }
        
        .month-section {
            margin-bottom: 8mm;
            page-break-inside: avoid;
        }
        
        .month-header {
            background-color: #fef3c7;
            padding: 3mm;
            margin-bottom: 3mm;
            border-left: 4px solid #f59e0b;
            font-weight: bold;
            font-size: 12pt;
            color: #92400e;
        }
        
        .deceased-item {
            margin-bottom: 3mm;
            padding: 2mm;
            border-left: 3px solid #dc2626;
            background-color: #fef2f2;
        }
        
        .text-muted {
            color: #9ca3af;
        }
        
        .text-sm {
            font-size: 9pt;
        }
        
        .mb-2 {
            margin-bottom: 2mm;
        }
        
        .mb-4 {
            margin-bottom: 4mm;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">⛪ AFE SALESIAN PROVINCE</div>
        <div class="subtitle">@yield('subtitle', 'Directory Report')</div>
    </div>

    @yield('content')

    <div class="footer">
        <div>Generated on {{ now()->format('d F Y, H:i') }}</div>
        <div class="page-number"></div>
    </div>
</body>
</html>
