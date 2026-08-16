<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTournamentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage tournaments') ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:150'],
            'season_name' => ['nullable', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:180', 'alpha_dash', Rule::unique('tournaments', 'slug')],
            'description' => ['nullable', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:150'],
            'venue' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'timezone' => ['required', 'timezone'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'registration_opens_at' => ['nullable', 'date'],
            'registration_closes_at' => ['nullable', 'date', 'after_or_equal:registration_opens_at'],
            'squad_size' => ['required', 'integer', 'min:1', 'max:99'],
            'default_pick_duration' => ['required', 'integer', 'min:5', 'max:3600'],
            'cricket_rule_profile_id' => ['nullable', 'integer', 'exists:cricket_rule_profiles,id'],
            'default_overs_per_innings' => ['nullable', 'integer', 'min:1', 'max:100'],
            'is_public' => ['sometimes', 'boolean'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'banner' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
        ];
    }
}
