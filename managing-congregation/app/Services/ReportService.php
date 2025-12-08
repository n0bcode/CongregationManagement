<?php

namespace App\Services;

use App\Models\ReportTemplate;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    /**
     * Generate a report based on a template and filters.
     */
    public function generate(ReportTemplate $template, array $filters = []): Collection
    {
        $config = $template->config;
        $modelClass = $this->getModelClass($config['source']);
        
        $query = $modelClass::query();

        // Apply filters from template
        if (isset($config['filters'])) {
            foreach ($config['filters'] as $key => $value) {
                $query->where($key, $value);
            }
        }

        // Apply runtime filters
        foreach ($filters as $key => $value) {
            if (!empty($value)) {
                $query->where($key, $value);
            }
        }

        // Select specific fields
        if (isset($config['fields'])) {
            $query->select($config['fields']);
        }

        // Grouping
        if (isset($config['group_by'])) {
            $query->groupBy($config['group_by']);
            // If grouping, we likely need aggregate functions in select
        }

        return $query->get();
    }

    /**
     * Export data to a specific format.
     */
    public function export(Collection $data, string $format = 'csv')
    {
        switch ($format) {
            case 'csv':
                return $this->toCsv($data);
            case 'json':
                return $data->toJson();
            default:
                throw new \InvalidArgumentException("Unsupported format: {$format}");
        }
    }

    protected function toCsv(Collection $data): string
    {
        if ($data->isEmpty()) {
            return '';
        }

        $firstItem = $data->first();
        $headers = is_array($firstItem) ? array_keys($firstItem) : array_keys($firstItem->toArray());
        $output = fopen('php://temp', 'r+');
        fputcsv($output, $headers);

        foreach ($data as $row) {
            fputcsv($output, is_array($row) ? $row : $row->toArray());
        }

        rewind($output);
        return stream_get_contents($output);
    }

    protected function getModelClass(string $source): string
    {
        // Map source names to Model classes
        $map = [
            'members' => \App\Models\Member::class,
            'financials' => \App\Models\Expense::class, // Example
            'assignments' => \App\Models\Assignment::class,
        ];

        if (!isset($map[$source])) {
            throw new \InvalidArgumentException("Unknown source: {$source}");
        }

        return $map[$source];
    }
}
