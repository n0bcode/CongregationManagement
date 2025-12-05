<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\FormationStage;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreFormationEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $member = $this->route('member');

        return $this->user()->can('create', \App\Models\FormationEvent::class) &&
               $this->user()->can('view', $member);
    }

    public function rules(): array
    {
        return [
            'stage' => ['required', Rule::enum(FormationStage::class)],
            'started_at' => [
                'required',
                'date',
                'bail',
                new \App\Rules\FormationChronologyRule($this->route('member'), $this->input('stage')),
            ],
            'notes' => ['nullable', 'string'],
        ];
    }
}
