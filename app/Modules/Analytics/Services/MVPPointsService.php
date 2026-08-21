<?php

namespace App\Modules\Analytics\Services;

use App\Models\CricketMatch;
use App\Models\InningsBattingStat;
use App\Models\InningsBowlingStat;
use App\Models\MatchWicket;
use Illuminate\Support\Collection;

class MVPPointsService
{
    public function getMatchMVP(CricketMatch $match): Collection
    {
        $players = $match->players()->where('selection_type', 'playing_xi')->get();
        $inningsIds = $match->innings()->pluck('id')->all();

        $allBattingStats = InningsBattingStat::query()
            ->whereIn('innings_id', $inningsIds)
            ->get()
            ->groupBy('match_player_id');

        $allBowlingStats = InningsBowlingStat::query()
            ->whereIn('innings_id', $inningsIds)
            ->get()
            ->groupBy('match_player_id');

        $allFieldingWickets = MatchWicket::query()
            ->whereHas('delivery', fn($q) => $q->where('match_id', $match->id)->whereNull('voided_at'))
            ->whereNotNull('fielder_id')
            ->get()
            ->groupBy('fielder_id');

        return $players->map(function ($player) use ($allBattingStats, $allBowlingStats, $allFieldingWickets) {
            $battingStats = $allBattingStats->get($player->id, collect());
            $bowlingStats = $allBowlingStats->get($player->id, collect());
            $fieldingWickets = $allFieldingWickets->get($player->id, collect());

            // 1. Batting Points
            $runs = $battingStats->sum('runs');
            $fours = $battingStats->sum('fours');
            $sixes = $battingStats->sum('sixes');

            $battingPoints = $runs;
            $battingPoints += $fours * 1;
            $battingPoints += $sixes * 2;
            if ($runs >= 100) {
                $battingPoints += 50;
            } elseif ($runs >= 50) {
                $battingPoints += 20;
            } elseif ($runs >= 30) {
                $battingPoints += 10;
            }

            // 2. Bowling Points
            $wickets = $bowlingStats->sum('wickets');
            $maidens = $bowlingStats->sum('maidens');
            $noBalls = $bowlingStats->sum('no_balls');
            $wides = $bowlingStats->sum('wides');

            $bowlingPoints = $wickets * 20;
            $bowlingPoints += $maidens * 10;
            $bowlingPoints -= $noBalls * 1;
            $bowlingPoints -= $wides * 1;
            if ($wickets >= 5) {
                $bowlingPoints += 25;
            } elseif ($wickets >= 3) {
                $bowlingPoints += 10;
            }

            // 3. Fielding Points
            $catches = $fieldingWickets->filter(fn($w) => in_array($w->dismissal_type, ['caught', 'caught_and_bowled'], true))->count();
            $stumpings = $fieldingWickets->where('dismissal_type', 'stumped')->count();
            $runOuts = $fieldingWickets->where('dismissal_type', 'run_out')->count();

            $fieldingPoints = ($catches * 10) + ($stumpings * 10) + ($runOuts * 15);

            $totalPoints = $battingPoints + $bowlingPoints + $fieldingPoints;

            return [
                'player_id' => $player->id,
                'player_name' => $player->player_name_snapshot,
                'player_role' => $player->player_role_snapshot,
                'team_id' => $player->team_id,
                'points' => [
                    'batting' => $battingPoints,
                    'bowling' => $bowlingPoints,
                    'fielding' => $fieldingPoints,
                    'total' => $totalPoints,
                ],
                'stats' => [
                    'batting' => [
                        'runs' => $runs,
                        'fours' => $fours,
                        'sixes' => $sixes,
                    ],
                    'bowling' => [
                        'wickets' => $wickets,
                        'maidens' => $bowlingStats->sum('maidens'),
                        'no_balls' => $bowlingStats->sum('no_balls'),
                        'wides' => $bowlingStats->sum('wides'),
                    ],
                    'fielding' => [
                        'catches' => $catches,
                        'stumpings' => $stumpings,
                        'run_outs' => $runOuts,
                    ]
                ]
            ];
        })->sortByDesc('points.total')->values();
    }
}
