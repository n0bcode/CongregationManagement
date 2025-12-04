<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMemberPhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('member'));
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:2048', 'mimes:jpg,jpeg,png'],
        ];
    }
}
