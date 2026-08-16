<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Models\Tournament;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function index(Tournament $tournament): View
    {
        abort_unless($tournament->publiclyVisibleNow(), 404);
        return view('public.matches.index', [
            'tournament' => $tournament,
            'fixtures' => $tournament->fixtures()->with(['homeTeam', 'awayTeam', 'match'])->get(),
        ]);
    }

    public function show(CricketMatch $match): View
    {
        $match->load(['tournament', 'fixture', 'ruleProfile', 'winner', 'innings.battingTeam', 'innings.bowlingTeam', 'innings.battingStats.player', 'innings.bowlingStats.player', 'innings.deliveries.striker', 'innings.deliveries.nonStriker', 'innings.deliveries.bowler', 'innings.deliveries.wicket']);
        abort_unless($match->tournament->publiclyVisibleNow() && in_array($match->status, ['live', 'completed', 'result_pending', 'approved'], true), 404);
        return view('public.matches.show', compact('match'));
    }

    public function state(CricketMatch $match): JsonResponse
    {
        $match->load(['tournament', 'fixture', 'ruleProfile', 'innings.battingTeam', 'innings.bowlingTeam', 'innings.battingStats.player', 'innings.bowlingStats.player', 'innings.deliveries.wicket']);
        abort_unless($match->tournament->publiclyVisibleNow() && in_array($match->status, ['live', 'completed', 'result_pending', 'approved'], true), 404);
        $innings = $match->innings->map(fn ($inning) => [
            'id' => $inning->id,
            'number' => $inning->innings_number,
            'team' => $inning->battingTeam?->short_name ?: $inning->battingTeam?->name,
            'runs' => $inning->total_runs,
            'wickets' => $inning->wickets,
            'overs' => $inning->oversDisplay($match->ruleProfile?->legal_balls_per_over),
            'maximum_overs' => (int) $inning->maximum_overs,
            'status' => $inning->status,
            'target' => $inning->target_runs,
            'batting' => $inning->battingStats->sortBy('batting_position')->values()->map(fn ($stat) => [
                'player' => $stat->player?->player_name_snapshot,
                'dismissal' => $stat->dismissal_type ? str_replace('_', ' ', ucfirst($stat->dismissal_type)) : ($stat->status === 'did_not_bat' ? 'Did not bat' : 'not out'),
                'runs' => $stat->runs,
                'balls' => $stat->balls,
                'fours' => $stat->fours,
                'sixes' => $stat->sixes,
                'strike_rate' => number_format((float) $stat->strike_rate, 2),
            ]),
            'bowling' => $inning->bowlingStats->values()->map(fn ($stat) => [
                'player' => $stat->player?->player_name_snapshot,
                'overs' => intdiv((int) $stat->legal_balls, (int) ($match->ruleProfile?->legal_balls_per_over ?: 6)).'.'.((int) $stat->legal_balls % (int) ($match->ruleProfile?->legal_balls_per_over ?: 6)),
                'runs' => $stat->runs_conceded,
                'wickets' => $stat->wickets,
                'wides' => $stat->wides,
                'no_balls' => $stat->no_balls,
                'economy' => number_format((float) $stat->economy, 2),
            ]),
            'recent' => $inning->deliveries->whereNull('voided_at')->sortByDesc('sequence_number')->take(12)->values()->map(fn ($delivery) => ['over' => $delivery->over_number.'.'.$delivery->ball_number, 'notation' => $delivery->notation()]),
        ]);
        return response()->json(['revision' => $match->revision, 'status' => $match->status, 'overs_per_innings' => (int) ($match->overs_per_innings ?: $match->ruleProfile?->overs_per_innings), 'result_summary' => $match->result_summary, 'innings' => $innings]);
    }

    public function standings(Tournament $tournament): View
    {
        abort_unless($tournament->publiclyVisibleNow(), 404);
        return view('public.matches.standings', [
            'tournament' => $tournament,
            'standings' => $tournament->standings()->with('team')->orderByDesc('points')->orderByDesc('net_run_rate')->get(),
        ]);
    }
}
