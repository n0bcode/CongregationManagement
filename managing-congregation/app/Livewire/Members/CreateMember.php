<?php

namespace App\Livewire\Members;

use App\Models\Community;
use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class CreateMember extends Component
{
    public $first_name = '';
    public $last_name = '';
    public $religious_name = '';
    public $dob = '';
    public $entry_date = '';
    public $community_id = '';

    public function mount()
    {
        $this->entry_date = date('Y-m-d');
        
        // Pre-fill community if user is not super admin
        if (Auth::user()->community_id) {
            $this->community_id = Auth::user()->community_id;
        }
    }

    protected function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'religious_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'entry_date' => ['required', 'date'],
            'community_id' => ['required', 'exists:communities,id'],
        ];
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $validated = $this->validate();

        Member::create($validated);

        session()->flash('success', 'Member created successfully.');

        return redirect()->route('members.index');
    }

    public function render()
    {
        $communities = Community::orderBy('name')->get();

        return view('livewire.members.create-member', [
            'communities' => $communities,
        ]);
    }
}
