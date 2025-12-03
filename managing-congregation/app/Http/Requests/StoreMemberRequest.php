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
                })
            ],
            'last_name' => ['required', 'string', 'max:255'],
            'religious_name' => ['nullable', 'string', 'max:255'],
            'dob' => ['required', 'date', 'before:today'],
            'entry_date' => ['required', 'date'],
        ];
    }

    // Removed withValidator as it is replaced by Rule::unique
}
