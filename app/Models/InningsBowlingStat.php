<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InningsBowlingStat extends Model
{
    use HasFactory;

    protected $table = 'innings_bowling_stats';
    protected $fillable = ['innings_id', 'match_player_id', 'legal_balls', 'maidens', 'runs_conceded', 'wickets', 'no_balls', 'wides', 'economy'];
    protected function casts(): array { return ['economy' => 'decimal:2']; }
    public function innings(): BelongsTo { return $this->belongsTo(MatchInnings::class, 'innings_id'); }
    public function player(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'match_player_id'); }
}
