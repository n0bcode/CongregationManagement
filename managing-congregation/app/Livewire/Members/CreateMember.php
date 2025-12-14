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
    public $member_type = 'postulant'; // postulant, novice, professed
    public $novitiate_entry_date = '';
    public $first_vows_date = '';
    public $perpetual_vows_date = '';
    
    // Passport Data
    public $passport_number = '';
    public $passport_issued_at = '';
    public $passport_expired_at = '';
    public $passport_place_of_issue = '';

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
        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'religious_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'entry_date' => ['required', 'date'],
            'community_id' => ['required', 'exists:communities,id'],
            'member_type' => ['required', 'in:postulant,novice,professed'],
            
            // Passport Validation
            'passport_number' => ['nullable', 'string', 'max:50'],
            'passport_issued_at' => ['nullable', 'date'],
            'passport_expired_at' => ['nullable', 'date', 'after:passport_issued_at'],
            'passport_place_of_issue' => ['nullable', 'string', 'max:255'],
        ];

        // Conditional validation based on member type
        if ($this->member_type === 'novice' || $this->member_type === 'professed') {
            $rules['novitiate_entry_date'] = ['required', 'date', 'after:entry_date'];
        }

        if ($this->member_type === 'professed') {
            $rules['first_vows_date'] = ['required', 'date', 'after:novitiate_entry_date'];
            $rules['perpetual_vows_date'] = ['nullable', 'date', 'after:first_vows_date'];
        }

        return $rules;
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
