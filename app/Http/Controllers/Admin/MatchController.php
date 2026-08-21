<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Models\Tournament;
use App\Modules\Scoring\Services\MatchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MatchController extends Controller
{
    public function __construct(private readonly MatchService $matchService)
    {
    }

    public function index(Tournament $tournament): View
    {
        return view('admin.matches.index', [
            'tournament' => $tournament,
            'matches' => $tournament->matches()->with(['ruleProfile', 'tossWinner', 'fixture.homeTeam', 'fixture.awayTeam'])->latest()->paginate(15),
        ]);
    }

    public function create(Tournament $tournament): View
    {
        return view('admin.matches.create', [
            'tournament' => $tournament->load(['teams', 'cricketRuleProfile']),
            'teams' => $tournament->teams()->where('is_active', true)->orderBy('display_order')->get(),
        ]);
    }

    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        $validated = $request->validate([
            'home_team_id' => ['required', 'integer', 'different:away_team_id'],
            'away_team_id' => ['required', 'integer', 'different:home_team_id'],
            'fixture_id' => ['nullable', 'integer'],
            'overs_per_innings' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $match = $this->matchService->createFromTeams(
            $tournament,
            (int) $validated['home_team_id'],
            (int) $validated['away_team_id'],
            isset($validated['fixture_id']) ? (int) $validated['fixture_id'] : null,
            (int) $request->user()->id,
            isset($validated['overs_per_innings']) ? (int) $validated['overs_per_innings'] : null,
        );

        return redirect()->route('admin.tournaments.matches.show', [$tournament, $match])
            ->with('status', 'Match created from the completed draft squad.');
    }

    public function show(Tournament $tournament, CricketMatch $match): View
    {
        abort_unless($match->tournament_id === $tournament->id, 404);
        return view('admin.matches.show', [
            'tournament' => $tournament,
            'match' => $match->load(['players.team', 'ruleProfile', 'tossWinner', 'fixture']),
            'teams' => $tournament->teams()->whereIn('id', $match->players()->distinct()->pluck('team_id'))->get(),
        ]);
    }

    public function updateOvers(Request $request, Tournament $tournament, CricketMatch $match): RedirectResponse
    {
        abort_unless($match->tournament_id === $tournament->id, 404);
        $validated = $request->validate(['overs_per_innings' => ['required', 'integer', 'min:1', 'max:100']]);
        $this->matchService->updateOversPerInnings($match, (int) $validated['overs_per_innings'], (int) $request->user()->id);
        return back()->with('status', 'Match overs updated successfully.');
    }

    public function submitXi(Request $request, Tournament $tournament, CricketMatch $match, int $team): RedirectResponse
    {
        abort_unless($match->tournament_id === $tournament->id, 404);
        $validated = $request->validate([
            'player_ids' => ['required', 'array'],
            'player_ids.*' => ['integer'],
        ]);
        $this->matchService->submitPlayingXi($match, $team, $validated['player_ids'], (int) $request->user()->id);
        return back()->with('status', 'Playing XI submitted and awaiting approval.');
    }

    public function approveLineup(Request $request, Tournament $tournament, CricketMatch $match): RedirectResponse
    {
        abort_unless($match->tournament_id === $tournament->id, 404);
        $this->matchService->approveLineup($match, (int) $request->user()->id);
        return back()->with('status', 'Playing XIs approved. Record the toss to start the match.');
    }

    public function recordToss(Request $request, Tournament $tournament, CricketMatch $match): RedirectResponse
    {
        abort_unless($match->tournament_id === $tournament->id, 404);
        $validated = $request->validate([
            'toss_winner_team_id' => ['required', 'integer'],
            'toss_decision' => ['required', 'in:bat,field'],
        ]);
        $this->matchService->recordToss($match, (int) $validated['toss_winner_team_id'], $validated['toss_decision'], (int) $request->user()->id);
        return back()->with('status', 'Toss recorded. Match is now live.');
    }
}
