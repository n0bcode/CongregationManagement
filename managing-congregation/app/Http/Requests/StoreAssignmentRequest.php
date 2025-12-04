<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('member'));
    }

    public function rules(): array
    {
        return [
            'community_id' => ['required', 'exists:communities,id'],
            'role' => ['nullable', 'string', 'max:255'],
            'start_date' => [
                'required', 
                'date',
                new \App\Rules\AssignmentOverlapRule($this->route('member'), $this->input('end_date'))
            ],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ];
    }
}
