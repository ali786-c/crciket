<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftPick extends Model
{
    use HasFactory;

    protected $fillable = [
        'draft_id',
        'draft_round_id',
        'team_id',
        'pick_number',
        'pick_duration',
        'status',
        'tournament_player_id',
        'selected_by',
        'started_at',
        'expired_at',
        'selected_at',
        'skipped_at',
        'extension_count',
        'total_extension_seconds',
    ];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'expired_at' => 'datetime',
            'selected_at' => 'datetime',
            'skipped_at' => 'datetime',
        ];
    }

    public function draft(): BelongsTo
    {
        return $this->belongsTo(Draft::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(DraftRound::class, 'draft_round_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function tournamentPlayer(): BelongsTo
    {
        return $this->belongsTo(TournamentPlayer::class);
    }

    public function selectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'selected_by');
    }
}
