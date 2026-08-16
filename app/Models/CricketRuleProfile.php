<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CricketRuleProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'format',
        'innings_per_side',
        'overs_per_innings',
        'playing_xi_size',
        'maximum_wickets',
        'legal_balls_per_over',
        'max_overs_per_bowler',
        'no_ball_runs',
        'wide_runs',
        'win_points',
        'tie_points',
        'no_result_points',
        'loss_points',
        'tie_method',
        'version',
        'is_system',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'innings_per_side' => 'integer',
            'overs_per_innings' => 'integer',
            'playing_xi_size' => 'integer',
            'maximum_wickets' => 'integer',
            'legal_balls_per_over' => 'integer',
            'max_overs_per_bowler' => 'integer',
            'no_ball_runs' => 'integer',
            'wide_runs' => 'integer',
            'win_points' => 'integer',
            'tie_points' => 'integer',
            'no_result_points' => 'integer',
            'loss_points' => 'integer',
            'version' => 'integer',
            'is_system' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function tournaments(): HasMany
    {
        return $this->hasMany(Tournament::class);
    }
}
