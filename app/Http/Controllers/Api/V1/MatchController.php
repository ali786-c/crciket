<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use Illuminate\Http\JsonResponse;

class MatchController extends Controller
{
    public function state(CricketMatch $match): JsonResponse
    {
        $match->load(['tournament', 'ruleProfile', 'innings.battingTeam', 'innings.bowlingTeam', 'innings.battingStats.player', 'innings.bowlingStats.player', 'innings.deliveries.wicket']);
        abort_unless($match->tournament->publiclyVisibleNow() && in_array($match->status, ['live', 'completed', 'result_pending', 'approved'], true), 404);
        $ballsPerOver = (int) ($match->ruleProfile?->legal_balls_per_over ?: 6);
        return response()->json([
            'data' => [
                'id' => $match->id,
                'revision' => $match->revision,
                'status' => $match->status,
                'result_type' => $match->result_type,
                'result_summary' => $match->result_summary,
                'winner_team_id' => $match->winner_team_id,
                'overs_per_innings' => (int) ($match->overs_per_innings ?: $match->ruleProfile?->overs_per_innings),
                'innings' => $match->innings->map(fn ($inning) => [
                    'id' => $inning->id,
                    'number' => $inning->innings_number,
                    'batting_team' => ['id' => $inning->battingTeam?->id, 'name' => $inning->battingTeam?->name, 'short_name' => $inning->battingTeam?->short_name],
                    'bowling_team' => ['id' => $inning->bowlingTeam?->id, 'name' => $inning->bowlingTeam?->name, 'short_name' => $inning->bowlingTeam?->short_name],
                    'runs' => $inning->total_runs,
                    'wickets' => $inning->wickets,
                    'legal_balls' => $inning->legal_balls,
                    'maximum_overs' => (int) $inning->maximum_overs,
                    'overs' => $inning->oversDisplay($ballsPerOver),
                    'target' => $inning->target_runs,
                    'status' => $inning->status,
                    'batting' => $inning->battingStats->sortBy('batting_position')->values()->map(fn ($stat) => [
                        'player' => $stat->player?->player_name_snapshot,
                        'dismissal' => $stat->dismissal_type,
                        'runs' => $stat->runs,
                        'balls' => $stat->balls,
                        'fours' => $stat->fours,
                        'sixes' => $stat->sixes,
                        'strike_rate' => (float) $stat->strike_rate,
                    ]),
                    'bowling' => $inning->bowlingStats->values()->map(fn ($stat) => [
                        'player' => $stat->player?->player_name_snapshot,
                        'overs' => intdiv((int) $stat->legal_balls, $ballsPerOver).'.'.((int) $stat->legal_balls % $ballsPerOver),
                        'runs' => $stat->runs_conceded,
                        'wickets' => $stat->wickets,
                        'wides' => $stat->wides,
                        'no_balls' => $stat->no_balls,
                        'economy' => (float) $stat->economy,
                    ]),
                    'recent_deliveries' => $inning->deliveries->whereNull('voided_at')->sortByDesc('sequence_number')->take(12)->values()->map(fn ($delivery) => [
                        'over' => $delivery->over_number.'.'.$delivery->ball_number,
                        'notation' => $delivery->notation(),
                        'total_runs' => $delivery->total_runs,
                    ]),
                ])->values(),
            ],
        ]);
    }
}
