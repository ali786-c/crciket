<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TournamentStanding extends Model
{
    use HasFactory;

    protected $fillable = ['tournament_id', 'team_id', 'played', 'wins', 'losses', 'ties', 'no_results', 'points', 'runs_for', 'balls_faced', 'runs_against', 'balls_bowled', 'net_run_rate'];
    protected function casts(): array { return ['net_run_rate' => 'decimal:3']; }
    public function tournament(): BelongsTo { return $this->belongsTo(Tournament::class); }
    public function team(): BelongsTo { return $this->belongsTo(Team::class); }
}
