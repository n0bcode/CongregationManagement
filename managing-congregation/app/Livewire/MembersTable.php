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

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selected = $this->getQuery()->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function sortBy($field)
    {
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

    public function updateMember($id, $field, $value)
    {
        $member = Member::find($id);
        // Check authorization if needed
        
        $member->update([$field => $value]);
        session()->flash('status', 'Member updated successfully.');
    }

    protected function getQuery(): Builder
    {
        return Member::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('first_name', 'like', '%' . $this->search . '%')
                      ->orWhere('last_name', 'like', '%' . $this->search . '%')
                      ->orWhere('religious_name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->communityId, fn($q) => $q->where('community_id', $this->communityId))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function render()
    {
        return view('livewire.members-table', [
            'members' => $this->getQuery()->paginate(10),
            'communities' => \App\Models\Community::all(),
            'statuses' => \App\Enums\MemberStatus::cases(),
            'presets' => \App\Models\FilterPreset::where('user_id', auth()->id())
                ->where('context', 'members_list')
                ->get(),
        ]);
    }
}
