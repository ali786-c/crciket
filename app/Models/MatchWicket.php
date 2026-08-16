<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchWicket extends Model
{
    use HasFactory;

    protected $table = 'match_wickets';

    protected $fillable = [
        'delivery_id', 'dismissed_player_id', 'dismissal_type', 'credited_bowler_id',
        'fielder_id', 'runs_completed', 'is_valid_wicket', 'notes',
    ];

    protected function casts(): array
    {
        return ['is_valid_wicket' => 'boolean'];
    }

    public function delivery(): BelongsTo { return $this->belongsTo(MatchDelivery::class, 'delivery_id'); }
    public function dismissedPlayer(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'dismissed_player_id'); }
    public function creditedBowler(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'credited_bowler_id'); }
    public function fielder(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'fielder_id'); }
}
