<?php

namespace App\Modules\Analytics\Services;

use App\Models\Fixture;
use App\Models\Tournament;
use App\Models\TournamentStanding;

class StandingsSimulationService
{
    public function simulateQualifications(Tournament $tournament): array
    {
        $teams = $tournament->teams()->where('is_active', true)->get();
        $ruleProfile = $tournament->cricketRuleProfile;
        $winPoints = $ruleProfile?->win_points ?? 2;

        $standings = TournamentStanding::query()
            ->where('tournament_id', $tournament->id)
            ->get()
            ->keyBy('team_id');

        $scheduledFixtures = Fixture::query()
            ->where('tournament_id', $tournament->id)
            ->where('status', 'scheduled')
            ->get();

        $simulationData = [];

        foreach ($teams as $team) {
            $standing = $standings->get($team->id);
            $currentPoints = $standing?->points ?? 0;
            $currentPos = $standing ? TournamentStanding::query()
                ->where('tournament_id', $tournament->id)
                ->where('points', '>', $currentPoints)
                ->count() + 1 : $teams->count();

            // Count remaining scheduled matches for this team
            $remainingMatches = $scheduledFixtures->filter(function ($f) use ($team) {
                return $f->home_team_id === $team->id || $f->away_team_id === $team->id;
            })->count();

            $maxPoints = $currentPoints + ($remainingMatches * $winPoints);
            $minPoints = $currentPoints;

            $simulationData[] = [
                'team_id' => $team->id,
                'name' => $team->name,
                'short_name' => $team->short_name,
                'logo_path' => $team->logo_path,
                'current_points' => $currentPoints,
                'current_position' => $currentPos,
                'remaining_matches' => $remainingMatches,
                'min_possible_points' => $minPoints,
                'max_possible_points' => $maxPoints,
                'status' => 'in_contention', // default placeholder
            ];
        }

        // Sort by current points descending to calculate thresholds
        usort($simulationData, function ($a, $b) {
            return $b['current_points'] <=> $a['current_points'];
        });

        $totalTeamsCount = count($simulationData);

        if ($totalTeamsCount >= 4) {
            // P4 = Points of team currently in 4th place (index 3)
            $p4Points = $simulationData[3]['current_points'];

            // Sort by max points to identify maximum potential of 5th team (index 4)
            $sortedByMax = $simulationData;
            usort($sortedByMax, function ($a, $b) {
                return $b['max_possible_points'] <=> $a['max_possible_points'];
            });
            $m5MaxPoints = $sortedByMax[min($totalTeamsCount - 1, 4)]['max_possible_points'];

            foreach ($simulationData as &$data) {
                if ($data['max_possible_points'] < $p4Points) {
                    $data['status'] = 'eliminated';
                } elseif ($data['min_possible_points'] > $m5MaxPoints) {
                    $data['status'] = 'qualified';
                } else {
                    $data['status'] = 'in_contention';
                }
            }
        } else {
            // Less than 4 teams: all are qualified by default
            foreach ($simulationData as &$data) {
                $data['status'] = 'qualified';
            }
        }

        return $simulationData;
    }
}
