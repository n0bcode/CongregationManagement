<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMemberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('members')->where(function ($query) {
                    return $query->where('last_name', $this->last_name)
                        ->where('dob', $this->dob);
                }),
            ],
            'last_name' => ['required', 'string', 'max:255'],
            'religious_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'entry_date' => ['required', 'date'],
            'community_id' => [
                Rule::requiredIf(fn () => auth()->user()->community_id === null),
                'nullable',
                'exists:communities,id',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'first_name.unique' => 'A member with this name and date of birth already exists.',
            'dob.before' => 'The date of birth must be in the past.',
            'entry_date.required' => 'Please provide the date the member entered the congregation.',
            'community_id.required' => 'Please select a community for the new member.',
        ];
    }
}
