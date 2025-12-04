<?php

declare(strict_types=1);

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Generate a financial report PDF.
     */
    public function generateFinancialReport(array $report, string $communityName): string
    {
        $pdf = Pdf::loadView('financials.pdf.monthly-report', [
            'report' => $report,
            'communityName' => $communityName,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = sprintf(
            'financial-report-%s-%s-%s.pdf',
            $communityName,
            $report['period']['year'],
            str_pad((string) $report['period']['month'], 2, '0', STR_PAD_LEFT)
        );

        return $pdf->download($filename);
    }

    /**
     * Generate a member profile PDF.
     */
    public function generateMemberProfile($member): string
    {
        $member->load([
            'community',
            'formationEvents' => function ($query) {
                $query->orderBy('event_date', 'desc');
            },
            'healthRecords' => function ($query) {
                $query->orderBy('recorded_at', 'desc');
            },
            'skills',
            'assignments' => function ($query) {
                $query->orderBy('start_date', 'desc');
            },
        ]);

        $pdf = Pdf::loadView('members.pdf.profile', [
            'member' => $member,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = sprintf(
            'member-profile-%s-%s.pdf',
            $member->first_name,
            $member->last_name
        );

        return $pdf->download($filename);
    }

    /**
     * Generate year-to-date summary PDF.
     */
    public function generateYearToDateReport(array $summary, string $communityName): string
    {
        $pdf = Pdf::loadView('financials.pdf.ytd-report', [
            'summary' => $summary,
            'communityName' => $communityName,
        ]);

        $pdf->setPaper('a4', 'landscape');

        $filename = sprintf(
            'ytd-report-%s-%s.pdf',
            $communityName,
            $summary['year']
        );

        return $pdf->download($filename);
    }

    /**
     * Generate expense receipt PDF.
     */
    public function generateExpenseReceipt($expense): string
    {
        $expense->load(['community', 'creator']);

        $pdf = Pdf::loadView('financials.pdf.receipt', [
            'expense' => $expense,
        ]);

        $pdf->setPaper('a4', 'portrait');

        $filename = sprintf(
            'expense-receipt-%s.pdf',
            $expense->id
        );

        return $pdf->download($filename);
    }

    /**
     * Stream PDF instead of download.
     */
    public function streamFinancialReport(array $report, string $communityName)
    {
        $pdf = Pdf::loadView('financials.pdf.monthly-report', [
            'report' => $report,
            'communityName' => $communityName,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->stream();
    }

    /**
     * Save PDF to storage.
     */
    public function saveFinancialReport(array $report, string $communityName, string $path): bool
    {
        $pdf = Pdf::loadView('financials.pdf.monthly-report', [
            'report' => $report,
            'communityName' => $communityName,
        ]);

        $pdf->setPaper('a4', 'portrait');

        return $pdf->save($path);
    }

    /**
     * Generate demographic report PDF
     */
    public function generateDemographicReport(array $data): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = Pdf::loadView('reports.pdf.demographic', $data);

        $pdf->setPaper('a4', 'portrait');

        $filename = sprintf(
            'demographic-report-%s.pdf',
            now()->format('Y-m-d')
        );

        return $pdf->download($filename);
    }
}
