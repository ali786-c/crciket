<?php

namespace App\Modules\Analytics\Services;

use App\Models\InningsBattingStat;
use App\Models\InningsBowlingStat;
use App\Models\MatchPlayer;
use App\Models\MatchWicket;
use App\Models\Tournament;
use App\Models\TournamentPlayer;

class PlayerComparisonService
{
    public function comparePlayers(Tournament $tournament, int $player1Id, int $player2Id): array
    {
        $tp1 = TournamentPlayer::query()->where('tournament_id', $tournament->id)->findOrFail($player1Id);
        $tp2 = TournamentPlayer::query()->where('tournament_id', $tournament->id)->findOrFail($player2Id);

        return [
            'player1' => $this->getPlayerStats($tournament->id, $tp1),
            'player2' => $this->getPlayerStats($tournament->id, $tp2),
        ];
    }

    private function getPlayerStats(int $tournamentId, TournamentPlayer $tp): array
    {
        $matchPlayers = MatchPlayer::query()
            ->where('tournament_player_id', $tp->id)
            ->whereHas('match', fn($q) => $q->where('tournament_id', $tournamentId)->where('status', 'completed'))
            ->get();

        $matchPlayerIds = $matchPlayers->pluck('id')->all();

        // 1. Batting Stats
        $battingStats = InningsBattingStat::query()
            ->whereIn('match_player_id', $matchPlayerIds)
            ->get();

        $runs = $battingStats->sum('runs');
        $balls = $battingStats->sum('balls');
        $fours = $battingStats->sum('fours');
        $sixes = $battingStats->sum('sixes');

        $dismissedCount = MatchWicket::query()
            ->whereIn('dismissed_player_id', $matchPlayerIds)
            ->whereHas('delivery', fn($q) => $q->whereNull('voided_at'))
            ->count();

        $battingAverage = $dismissedCount > 0 ? round($runs / $dismissedCount, 2) : ($runs > 0 ? $runs : 0.0);
        $battingStrikeRate = $balls > 0 ? round(($runs / $balls) * 100, 2) : 0.0;

        // 2. Bowling Stats
        $bowlingStats = InningsBowlingStat::query()
            ->whereIn('match_player_id', $matchPlayerIds)
            ->get();

        $wickets = $bowlingStats->sum('wickets');
        $legalBalls = $bowlingStats->sum('legal_balls');
        $runsConceded = $bowlingStats->sum('runs_conceded');
        $maidens = $bowlingStats->sum('maidens');

        $bowlingAverage = $wickets > 0 ? round($runsConceded / $wickets, 2) : 0.0;
        $bowlingStrikeRate = $wickets > 0 ? round($legalBalls / $wickets, 2) : 0.0;
        $economy = $legalBalls > 0 ? round(($runsConceded / $legalBalls) * 6, 2) : 0.0;

        // 3. Matches Played
        $matchesPlayed = $matchPlayers->where('selection_type', 'playing_xi')->count();

        return [
            'id' => $tp->id,
            'full_name' => $tp->playerProfile?->full_name,
            'playing_role' => $tp->playerProfile?->playing_role,
            'matches_played' => $matchesPlayed,
            'batting' => [
                'runs' => $runs,
                'balls' => $balls,
                'average' => (float) $battingAverage,
                'strike_rate' => (float) $battingStrikeRate,
                'fours' => $fours,
                'sixes' => $sixes,
                'dismissals' => $dismissedCount,
            ],
            'bowling' => [
                'wickets' => $wickets,
                'legal_balls' => $legalBalls,
                'runs_conceded' => $runsConceded,
                'economy' => (float) $economy,
                'average' => (float) $bowlingAverage,
                'strike_rate' => (float) $bowlingStrikeRate,
                'maidens' => $maidens,
            ]
        ];
    }
}
