<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Draft extends Model
{
    use HasFactory;

    protected $fillable = [
        'tournament_id',
        'status',
        'current_pick_number',
        'pick_started_at',
        'pick_duration',
        'revision',
        'started_at',
        'paused_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'pick_started_at' => 'datetime',
            'started_at' => 'datetime',
            'paused_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function tournament(): BelongsTo
    {
        return $this->belongsTo(Tournament::class);
    }

    public function rounds(): HasMany
    {
        return $this->hasMany(DraftRound::class);
    }

    public function picks(): HasMany
    {
        return $this->hasMany(DraftPick::class)->orderBy('pick_number');
    }

    public function activePick(): ?DraftPick
    {
        return $this->picks()->where('status', 'active')->first();
    }
}
