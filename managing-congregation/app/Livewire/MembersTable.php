<?php

namespace App\Livewire;

use App\Models\Member;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

class MembersTable extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public array $selected = [];
    public bool $selectAll = false;
    public int $perPage = 10;

    // Filters
    public ?int $communityId = null;
    public ?string $status = null;

    public function applyPreset($presetId)
    {
        // Handle built-in presets if needed, or just DB ones
        if (in_array($presetId, ['active', 'novitiates'])) {
             $this->applyBuiltInPreset($presetId);
             return;
        }

        $preset = \App\Models\FilterPreset::find($presetId);
        if ($preset) {
            $this->reset(['search', 'communityId', 'status']);
            $filters = $preset->filters;
            
            if (isset($filters['search'])) $this->search = $filters['search'];
            if (isset($filters['communityId'])) $this->communityId = $filters['communityId'];
            if (isset($filters['status'])) $this->status = $filters['status'];
        }
    }

    protected function applyBuiltInPreset($preset)
    {
        $this->reset(['search', 'communityId', 'status']);
        switch ($preset) {
            case 'active':
                $this->status = \App\Enums\MemberStatus::Active->value;
                break;
            case 'novitiates':
                $this->search = 'Novitiate'; 
                break;
        }
    }

    public function saveCurrentFiltersAsPreset($name)
    {
        \App\Models\FilterPreset::create([
            'user_id' => auth()->id(),
            'name' => $name,
            'context' => 'members_list',
            'filters' => [
                'search' => $this->search,
                'communityId' => $this->communityId,
                'status' => $this->status,
            ],
        ]);
        
        session()->flash('status', 'Filter preset saved.');
    }

    protected $queryString = [
        'search' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'communityId' => ['except' => null],
        'status' => ['except' => null],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCommunityId()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function resetFilters() 
    {
        $this->reset(['search', 'communityId', 'status']);
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    /**
     * Sort by a field with validation to prevent SQL injection.
     * Only allows whitelisted fields to be sorted.
     */
    public function sortBy($field)
    {
        // Whitelist of sortable columns
        $allowedFields = ['first_name', 'last_name', 'religious_name', 'status', 'created_at', 'dob', 'entry_date'];
        
        if (!in_array($field, $allowedFields)) {
            return; // Ignore invalid sort requests
        }
        
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function deleteSelected()
    {
        Member::whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
        session()->flash('status', 'Selected members deleted successfully.');
    }

    public function exportSelected()
    {
        $members = Member::whereIn('id', $this->selected)->with('community')->get();
        
        // Create CSV content
        $csv = "First Name,Last Name,Religious Name,Status,Community\n";
        foreach ($members as $member) {
            $csv .= sprintf(
                '"%s","%s","%s","%s","%s"' . "\n",
                $member->first_name,
                $member->last_name,
                $member->religious_name ?? '',
                $member->status->name,
                $member->community?->name ?? ''
            );
        }
        
        // Return download response
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'members-export-' . now()->format('Y-m-d-His') . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    /**
     * Update a member field with validation.
     * Only allows whitelisted fields to prevent unauthorized modifications.
     */
    public function updateMember($id, $field, $value)
    {
        // Whitelist of allowed fields for inline updates
        $allowedFields = ['first_name', 'last_name', 'religious_name', 'email', 'status'];
        
        if (!in_array($field, $allowedFields)) {
            session()->flash('error', 'Field update not allowed.');
            return;
        }
        
        $member = Member::find($id);
        
        if (!$member) {
            session()->flash('error', 'Member not found.');
            return;
        }
        
        // Check authorization
        $this->authorize('update', $member);
        
        // Additional validation based on field type
        try {
            $validated = $this->validateField($field, $value);
            $member->update([$field => $validated]);
            session()->flash('status', 'Member updated successfully.');
        } catch (\InvalidArgumentException $e) {
            session()->flash('error', $e->getMessage());
        }
    }
    
    /**
     * Validate field value based on field type.
     */
    protected function validateField(string $field, $value)
    {
        return match($field) {
            'status' => in_array($value, array_column(\App\Enums\MemberStatus::cases(), 'value')) 
                ? $value 
                : throw new \InvalidArgumentException('Invalid status value'),
            'email' => filter_var($value, FILTER_VALIDATE_EMAIL) 
                ? $value 
                : throw new \InvalidArgumentException('Invalid email format'),
            default => $value,
        };
    }

    /**
     * Build the query with filters and search.
     * Escapes wildcards in search terms to prevent unintended matches.
     */
    protected function getQuery(): Builder
    {
        return Member::query()
            ->when($this->search, function ($query) {
                // Escape SQL wildcards (% and _) to treat them as literal characters
                $escapedSearch = addcslashes($this->search, '%_');
                
                $query->where(function ($q) use ($escapedSearch) {
                    $q->where('first_name', 'like', '%' . $escapedSearch . '%')
                      ->orWhere('last_name', 'like', '%' . $escapedSearch . '%')
                      ->orWhere('religious_name', 'like', '%' . $escapedSearch . '%');
                });
            })
            ->when($this->communityId, fn($q) => $q->where('community_id', $this->communityId))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.members-table', [
            'members' => $this->getQuery()->paginate($this->perPage),
            'communities' => \App\Models\Community::all(),
            'statuses' => \App\Enums\MemberStatus::cases(),
            'presets' => \App\Models\FilterPreset::where('user_id', auth()->id())
                ->where('context', 'members_list')
                ->get(),
        ]);
    }
}
