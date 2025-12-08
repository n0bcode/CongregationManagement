<?php

namespace App\Livewire\Reports;

use App\Models\ReportTemplate;
use App\Services\ReportService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ReportBuilder extends Component
{
    use WithPagination;

    public $source = 'members';
    public $availableSources = [
        'members' => 'Members',
        'financials' => 'Financials',
        'assignments' => 'Assignments',
    ];

    public $filters = [];
    public $selectedFields = [];
    public $availableFields = [];
    
    public $templateName = '';
    public $showSaveModal = false;

    protected $queryString = ['source'];

    public function mount()
    {
        $this->updateAvailableFields();
        // Default fields
        $this->selectedFields = array_keys($this->availableFields);
    }

    public function updatedSource()
    {
        $this->filters = [];
        $this->updateAvailableFields();
        $this->selectedFields = array_keys($this->availableFields);
        $this->resetPage();
    }

    public function updateAvailableFields()
    {
        // In a real app, this would be dynamic based on the model
        switch ($this->source) {
            case 'members':
                $this->availableFields = [
                    'first_name' => 'First Name',
                    'last_name' => 'Last Name',
                    'email' => 'Email',
                    'status' => 'Status',
                ];
                break;
            case 'financials':
                $this->availableFields = [
                    'amount' => 'Amount',
                    'category' => 'Category',
                    'description' => 'Description',
                    'date' => 'Date',
                ];
                break;
            case 'assignments':
                $this->availableFields = [
                    'role' => 'Role',
                    'start_date' => 'Start Date',
                    'end_date' => 'End Date',
                ];
                break;
        }
    }

    public function addFilter($key, $value)
    {
        $this->filters[$key] = $value;
    }

    public function removeFilter($key)
    {
        unset($this->filters[$key]);
    }

    public function saveTemplate()
    {
        $this->validate([
            'templateName' => 'required|string|max:255',
        ]);

        ReportTemplate::create([
            'name' => $this->templateName,
            'config' => [
                'source' => $this->source,
                'filters' => $this->filters,
                'fields' => $this->selectedFields,
            ],
            'created_by' => auth()->id(),
        ]);

        $this->showSaveModal = false;
        $this->dispatch('notify', 'Template saved successfully!');
    }

    public function export($format = 'csv')
    {
        $service = new ReportService();
        
        // Create a temporary template object for the service
        $template = new ReportTemplate([
            'config' => [
                'source' => $this->source,
                'filters' => $this->filters,
                'fields' => $this->selectedFields,
            ]
        ]);

        $data = $service->generate($template);
        
        return response()->streamDownload(function () use ($service, $data, $format) {
            echo $service->export($data, $format);
        }, 'report.' . $format);
    }

    public function render()
    {
        // Preview Logic
        $service = new ReportService();
        $template = new ReportTemplate([
            'config' => [
                'source' => $this->source,
                'filters' => $this->filters,
                'fields' => $this->selectedFields,
            ]
        ]);
        
        // For preview, we might want to limit results, but ReportService returns a Collection.
        // Ideally ReportService should support pagination or query builder return.
        // For now, we'll just take first 10 for preview.
        $previewData = $service->generate($template)->take(10);

        return view('livewire.reports.report-builder', [
            'previewData' => $previewData,
        ]);
    }
}
