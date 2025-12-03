<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Authorization is handled in the controller/policy
    }

    public function rules(): array
    {
        return [
            'community_id' => ['required', 'exists:communities,id'],
            'transfer_date' => ['required', 'date'],
        ];
    }
}
