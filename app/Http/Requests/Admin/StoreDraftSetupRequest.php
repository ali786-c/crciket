<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreDraftSetupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('configure draft') ?? false;
    }

    public function rules(): array
    {
        return [
            'rounds' => ['required', 'array', 'min:1'],
            'rounds.*.round_number' => ['required', 'integer', 'min:1', 'distinct'],
            'rounds.*.name' => ['nullable', 'string', 'max:100'],
            'rounds.*.picks' => ['required', 'array', 'min:1'],
            'rounds.*.picks.*.pick_number' => ['required', 'integer', 'min:1', 'distinct'],
            'rounds.*.picks.*.team_id' => ['required', 'integer', 'exists:teams,id'],
            'rounds.*.picks.*.pick_duration' => ['required', 'integer', 'min:5', 'max:3600'],
        ];
    }
}
