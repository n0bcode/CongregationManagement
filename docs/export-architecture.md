# Export Architecture - Congregation Management System

**Last Updated:** 2025-12-27  
**Purpose:** Document export strategies, patterns, and implementation details for PDF, Excel, and DOCX generation

---

## Overview

The Congregation Management System implements a comprehensive multi-format export architecture supporting:

- **PDF** - Reports, directories, celebration cards
- **Excel** - Data exports, demographic analysis
- **DOCX** - Formatted documents, directories

This document defines the patterns, libraries, and best practices for all export functionality.

---

## Technology Stack

### PDF Generation

**Library:** `barryvdh/laravel-dompdf` v3.1  
**Engine:** DomPDF  
**Use Cases:** Reports, directories, celebration cards

### Excel Generation

**Library:** `maatwebsite/excel` v3.1  
**Engine:** PhpSpreadsheet  
**Use Cases:** Data exports, demographic reports, birthday listings

### DOCX Generation

**Library:** `phpoffice/phpword` v1.4  
**Engine:** PHPWord  
**Use Cases:** Formatted directories, communion lists, deceased member reports

---

## PDF Export Patterns

### Pattern 1: Report Generation

**Use Case:** Financial reports, demographic reports

**Implementation Pattern:**

```php
use Barryvdh\DomPDF\Facade\Pdf;

public function generateReport()
{
    $data = $this->prepareReportData();

    $pdf = Pdf::loadView('reports.financial-monthly', [
        'data' => $data,
        'generatedAt' => now(),
    ]);

    return $pdf->download('financial-report-' . now()->format('Y-m') . '.pdf');
}
```

**Key Principles:**

- Use dedicated Blade views for PDF templates
- Pass minimal, pre-processed data to views
- Include generation timestamp
- Use descriptive filenames with dates

**View Structure:**

```blade
{{-- resources/views/reports/financial-monthly.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        /* Inline CSS for PDF rendering */
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
    </style>
</head>
<body>
    <h1>Monthly Financial Report</h1>
    <p>Generated: {{ $generatedAt->format('F d, Y') }}</p>

    <table>
        @foreach($data as $row)
            <tr>
                <td>{{ $row->category }}</td>
                <td>{{ number_format($row->amount, 2) }}</td>
            </tr>
        @endforeach
    </table>
</body>
</html>
```

---

### Pattern 2: Directory Exports

**Use Case:** Member directories, communion lists

**Implementation Pattern:**

```php
public function exportDirectory(string $format)
{
    $members = Member::with('community')
        ->active()
        ->orderBy('religious_name')
        ->get();

    if ($format === 'pdf') {
        return $this->generateDirectoryPdf($members);
    }

    // DOCX fallback
    return $this->generateDirectoryDocx($members);
}

private function generateDirectoryPdf($members)
{
    $pdf = Pdf::loadView('exports.directory-pdf', [
        'members' => $members,
        'title' => 'Member Directory',
    ])
    ->setPaper('a4', 'portrait')
    ->setOption('margin-top', 10)
    ->setOption('margin-bottom', 10);

    return $pdf->download('member-directory.pdf');
}
```

**Key Principles:**

- Eager load relationships to avoid N+1 queries
- Apply scopes for filtering (e.g., `active()`)
- Configure paper size and margins
- Use semantic view names

---

### Pattern 3: Celebration Cards

**Use Case:** Birthday cards, anniversary cards

**Implementation Pattern:**

```php
public function generateBirthdayCard(Member $member)
{
    $pdf = Pdf::loadView('celebrations.birthday-card', [
        'member' => $member,
        'community' => $member->community,
        'font' => request('font', 'Caveat'), // Playful, Elegant, Modern
    ])
    ->setPaper([0, 0, 595, 842], 'landscape') // A4 landscape
    ->setOption('isHtml5ParserEnabled', true)
    ->setOption('isRemoteEnabled', true); // For external fonts/images

    return $pdf->stream('birthday-card-' . $member->id . '.pdf');
}
```

**Key Principles:**

- Support dynamic font selection
- Use landscape orientation for cards
- Enable HTML5 parser for advanced CSS
- Enable remote resources for Google Fonts
- Use `stream()` for preview, `download()` for saving

**View Structure:**

```blade
{{-- resources/views/celebrations/birthday-card.blade.php --}}
<!DOCTYPE html>
<html>
<head>
    <style>
        @import url('https://fonts.googleapis.com/css2?family={{ $font }}');

        body {
            font-family: '{{ $font }}', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
            padding: 50px;
        }

        .confetti {
            /* SVG confetti effects */
        }
    </style>
</head>
<body>
    <div class="confetti"></div>
    <h1 style="font-size: 48px;">Happy Birthday!</h1>
    <h2>{{ $member->religious_name }}</h2>
    <p>{{ $community->name }}</p>
</body>
</html>
```

---

## Excel Export Patterns

### Pattern 1: Data Export

**Use Case:** Member lists, demographic data

**Implementation Pattern:**

```php
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\MembersExport;

public function exportMembers()
{
    return Excel::download(
        new MembersExport(),
        'members-' . now()->format('Y-m-d') . '.xlsx'
    );
}
```

**Export Class:**

```php
namespace App\Exports;

use App\Models\Member;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MembersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Member::with('community')->active()->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Religious Name',
            'Civil Name',
            'Community',
            'Entry Date',
            'Status',
        ];
    }

    public function map($member): array
    {
        return [
            $member->id,
            $member->religious_name,
            $member->civil_name,
            $member->community->name ?? 'N/A',
            $member->entry_date?->format('Y-m-d'),
            $member->status,
        ];
    }
}
```

**Key Principles:**

- Use dedicated Export classes
- Implement `WithHeadings` for column headers
- Implement `WithMapping` for data transformation
- Eager load relationships
- Format dates consistently

---

### Pattern 2: Multi-Sheet Exports

**Use Case:** Comprehensive reports with multiple data sets

**Implementation Pattern:**

```php
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class DemographicExport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            new MembersSheet(),
            new CommunitiesSheet(),
            new FormationSheet(),
        ];
    }
}
```

---

## DOCX Export Patterns

### Pattern 1: Formatted Directory

**Use Case:** Communion directory, deceased member list

**Implementation Pattern:**

```php
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

public function exportCommunionDirectory()
{
    $phpWord = new PhpWord();
    $section = $phpWord->addSection();

    // Title
    $section->addText(
        'Communion Directory',
        ['bold' => true, 'size' => 20],
        ['alignment' => 'center']
    );

    $section->addTextBreak(2);

    // Members
    $members = Member::active()->orderBy('religious_name')->get();

    foreach ($members as $member) {
        $section->addText(
            $member->religious_name,
            ['bold' => true, 'size' => 12]
        );

        $section->addText(
            $member->community->name ?? 'No Community',
            ['size' => 10, 'italic' => true]
        );

        $section->addTextBreak(1);
    }

    // Save
    $filename = 'communion-directory-' . now()->format('Y-m-d') . '.docx';
    $filepath = storage_path('app/temp/' . $filename);

    $writer = IOFactory::createWriter($phpWord, 'Word2007');
    $writer->save($filepath);

    return response()->download($filepath)->deleteFileAfterSend();
}
```

**Key Principles:**

- Use sections for page layout
- Apply consistent text formatting
- Use text breaks for spacing
- Store temporary files in `storage/app/temp/`
- Delete files after download with `deleteFileAfterSend()`

---

### Pattern 2: Structured Documents

**Use Case:** Community reports with tables

**Implementation Pattern:**

```php
$table = $section->addTable([
    'borderSize' => 6,
    'borderColor' => '999999',
]);

// Header row
$table->addRow();
$table->addCell(3000)->addText('Name', ['bold' => true]);
$table->addCell(3000)->addText('Role', ['bold' => true]);
$table->addCell(3000)->addText('Date', ['bold' => true]);

// Data rows
foreach ($assignments as $assignment) {
    $table->addRow();
    $table->addCell(3000)->addText($assignment->member->religious_name);
    $table->addCell(3000)->addText($assignment->role);
    $table->addCell(3000)->addText($assignment->start_date->format('Y-m-d'));
}
```

---

## Controller Patterns

### Unified Export Controller

**Pattern:** Single controller method handling multiple formats

```php
public function exportDirectory(string $type, string $format)
{
    // Validate format
    if (!in_array($format, ['pdf', 'docx', 'excel'])) {
        abort(400, 'Invalid format');
    }

    // Get data
    $data = $this->getDirectoryData($type);

    // Route to appropriate exporter
    return match($format) {
        'pdf' => $this->exportPdf($data, $type),
        'docx' => $this->exportDocx($data, $type),
        'excel' => $this->exportExcel($data, $type),
    };
}
```

**Route Example:**

```php
Route::get('/reports/directory/{type}/{format}', [DirectoryReportController::class, 'export'])
    ->where('format', 'pdf|docx|excel')
    ->name('reports.directory.export');
```

---

## Performance Optimization

### Chunking Large Exports

**For Excel:**

```php
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class LargeMembersExport implements FromQuery, WithChunkReading
{
    public function query()
    {
        return Member::query();
    }

    public function chunkSize(): int
    {
        return 1000;
    }
}
```

### Memory Management

**For PDF:**

```php
// Limit data before passing to view
$members = Member::select(['id', 'religious_name', 'community_id'])
    ->with('community:id,name')
    ->limit(500)
    ->get();
```

---

## Error Handling

### Standard Pattern

```php
try {
    $pdf = Pdf::loadView('reports.financial', $data);
    return $pdf->download('report.pdf');
} catch (\Exception $e) {
    Log::error('PDF generation failed', [
        'error' => $e->getMessage(),
        'view' => 'reports.financial',
    ]);

    return back()->withErrors([
        'export' => 'Failed to generate PDF. Please try again.',
    ]);
}
```

---

## Testing Patterns

### PDF Generation Test

```php
public function test_financial_report_pdf_generation()
{
    $user = User::factory()->create(['role' => 'general']);

    $response = $this->actingAs($user)
        ->get(route('financials.monthly-report'));

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/pdf');
}
```

### Excel Export Test

```php
public function test_members_excel_export()
{
    Excel::fake();

    $user = User::factory()->create(['role' => 'general']);

    $this->actingAs($user)
        ->get(route('reports.demographic.export'));

    Excel::assertDownloaded('members-export.xlsx', function (MembersExport $export) {
        return $export->collection()->count() > 0;
    });
}
```

---

## File Storage Strategy

### Temporary Files

```php
// Store in temp directory
$filepath = storage_path('app/temp/' . $filename);

// Clean up old temp files (scheduled command)
Storage::disk('local')->delete(
    Storage::disk('local')->files('temp')
);
```

### Permanent Exports

```php
// Store in private storage
$filepath = storage_path('app/private/exports/' . $filename);

// Serve with authorization check
public function download($filename)
{
    $this->authorize('download-exports');

    return response()->download(
        storage_path('app/private/exports/' . $filename)
    );
}
```

---

## Common Pitfalls & Solutions

### Issue 1: Font Not Found in PDF

**Problem:** Custom fonts not rendering in PDF

**Solution:**

```php
// Use web-safe fonts or embed fonts
$pdf->setOption('defaultFont', 'DejaVu Sans');

// Or use Google Fonts with remote enabled
$pdf->setOption('isRemoteEnabled', true);
```

### Issue 2: Memory Exhaustion on Large Exports

**Problem:** Script runs out of memory

**Solution:**

```php
// Increase memory limit temporarily
ini_set('memory_limit', '512M');

// Or use chunking for Excel
implements WithChunkReading
```

### Issue 3: Slow PDF Generation

**Problem:** PDF generation takes too long

**Solution:**

```php
// Optimize queries
$members = Member::select(['id', 'name', 'community_id'])
    ->with('community:id,name')
    ->get();

// Disable debug mode in production
$pdf->setOption('debugPng', false);
```

---

## Implementation Checklist

When adding new export functionality:

- [ ] Choose appropriate format (PDF/Excel/DOCX)
- [ ] Create dedicated Export class or view
- [ ] Implement error handling
- [ ] Add authorization checks
- [ ] Optimize queries (eager loading, select specific columns)
- [ ] Test with large datasets
- [ ] Add route with format validation
- [ ] Document in this file
- [ ] Write automated tests

---

## Future Enhancements

Potential improvements to the export architecture:

1. **Queue-Based Exports:** For large datasets, queue export jobs
2. **Export Templates:** User-customizable export templates
3. **Scheduled Exports:** Automated report generation and email
4. **Export History:** Track all exports with audit trail
5. **CSV Support:** Add CSV format for maximum compatibility

---

**Document Status:** ✅ Complete  
**Maintained By:** Development Team  
**Review Frequency:** When new export features are added
