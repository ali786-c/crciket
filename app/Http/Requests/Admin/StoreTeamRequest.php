<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTeamRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage teams') ?? false;
    }

    public function rules(): array
    {
        $tournament = $this->route('tournament');

        return [
            'name' => [
                'required',
                'string',
                'max:120',
                Rule::unique('teams', 'name')->where(fn ($query) => $query->where('tournament_id', $tournament->id)),
            ],
            'short_name' => ['nullable', 'string', 'max:20'],
            'logo_path' => ['nullable', 'string', 'max:255'],
            'display_order' => [
                'required',
                'integer',
                'min:1',
                Rule::unique('teams', 'display_order')->where(fn ($query) => $query->where('tournament_id', $tournament->id)),
            ],
        ];
    }
}
