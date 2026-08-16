<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InningsBattingStat extends Model
{
    use HasFactory;

    protected $table = 'innings_batting_stats';
    protected $fillable = ['innings_id', 'match_player_id', 'batting_position', 'runs', 'balls', 'fours', 'sixes', 'strike_rate', 'dismissal_type', 'dismissed_by', 'fielder_id', 'status'];
    protected function casts(): array { return ['strike_rate' => 'decimal:2']; }
    public function innings(): BelongsTo { return $this->belongsTo(MatchInnings::class, 'innings_id'); }
    public function player(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'match_player_id'); }
    public function dismissedBy(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'dismissed_by'); }
    public function fielder(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'fielder_id'); }
}
