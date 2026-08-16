<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class MatchDelivery extends Model
{
    use HasFactory;

    protected $table = 'match_deliveries';

    protected $fillable = [
        'match_id', 'innings_id', 'over_number', 'ball_number', 'sequence_number',
        'striker_id', 'non_striker_id', 'bowler_id', 'runs_off_bat', 'wides',
        'no_balls', 'byes', 'leg_byes', 'penalty_runs', 'total_runs',
        'is_legal_delivery', 'wicket_id', 'commentary', 'recorded_by',
        'recorded_at', 'revision', 'voided_at', 'void_reason',
    ];

    protected function casts(): array
    {
        return [
            'is_legal_delivery' => 'boolean',
            'recorded_at' => 'datetime',
            'voided_at' => 'datetime',
        ];
    }

    public function match(): BelongsTo { return $this->belongsTo(CricketMatch::class, 'match_id'); }
    public function innings(): BelongsTo { return $this->belongsTo(MatchInnings::class, 'innings_id'); }
    public function striker(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'striker_id'); }
    public function nonStriker(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'non_striker_id'); }
    public function bowler(): BelongsTo { return $this->belongsTo(MatchPlayer::class, 'bowler_id'); }
    public function wicket(): HasOne { return $this->hasOne(MatchWicket::class, 'delivery_id'); }

    public function notation(): string
    {
        if ($this->voided_at) return 'void';
        if ($this->wides > 0) return $this->wides === 1 ? 'Wd' : $this->wides.'Wd';
        if ($this->no_balls > 0) return ($this->runs_off_bat > 0 ? $this->runs_off_bat : '').'Nb';
        if ($this->wicket) return 'W';
        if ($this->byes > 0) return 'B'.$this->byes;
        if ($this->leg_byes > 0) return 'Lb'.$this->leg_byes;
        return (string) $this->runs_off_bat;
    }
}
