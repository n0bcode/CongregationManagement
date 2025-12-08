<?php

namespace App\Services;

use App\Models\Expense;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class FinancialReportService
{
    /**
     * Export expenses based on filters and format.
     *
     * @param array $filters
     * @param string $format 'csv' or 'pdf'
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Illuminate\Http\Response
     */
    public function exportExpenses(array $filters, string $format = 'csv')
    {
        $query = Expense::query();

        if (isset($filters['community_id'])) {
            $query->where('community_id', $filters['community_id']);
        }

        if (isset($filters['year'])) {
            $query->whereYear('date', $filters['year']);
        }

        if (isset($filters['project_id'])) {
            $query->where('project_id', $filters['project_id']);
        }

        $expenses = $query->orderBy('date')->get();

        if ($format === 'pdf') {
            return $this->exportPdf($expenses);
        }

        return $this->exportCsv($expenses);
    }

    protected function exportCsv(Collection $expenses)
    {
        $filename = 'expenses-' . date('Y-m-d-H-i-s') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($expenses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Category', 'Description', 'Amount', 'Project']);

            foreach ($expenses as $expense) {
                fputcsv($file, [
                    $expense->date->format('Y-m-d'),
                    $expense->category,
                    $expense->description,
                    $expense->amount_in_dollars,
                    $expense->project->name ?? 'N/A',
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    protected function exportPdf(Collection $expenses)
    {
        $pdf = Pdf::loadView('reports.financial.expenses', ['expenses' => $expenses]);
        return $pdf->download('expenses-' . date('Y-m-d-H-i-s') . '.pdf');
    }
}
