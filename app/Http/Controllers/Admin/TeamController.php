<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamRequest;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Tournament $tournament): View
    {
        return view('admin.tournaments.teams', [
            'tournament' => $tournament->load('teams.activeCaptain.user'),
            'captainCandidates' => User::query()->role('captain')->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTeamRequest $request, Tournament $tournament): RedirectResponse
    {
        $tournament->teams()->create($request->validated());

        return back()->with('status', 'Team added successfully.');
    }

    public function assignCaptain(Request $request, Tournament $tournament, Team $team): RedirectResponse
    {
        abort_unless($team->tournament_id === $tournament->id, 404);
        abort_if($tournament->draft?->status !== null && $tournament->draft?->status !== 'setup', 409, 'Captain assignments cannot be changed after the draft starts.');

        $data = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);
        $captain = User::query()->role('captain')->findOrFail($data['user_id']);

        $alreadyAssigned = TeamCaptain::query()
            ->where('user_id', $captain->id)
            ->whereNull('revoked_at')
            ->whereHas('team', fn ($query) => $query->where('tournament_id', $tournament->id)->where('teams.id', '<>', $team->id))
            ->exists();

        if ($alreadyAssigned) {
            return back()->withErrors(['user_id' => 'This captain is already assigned to another team in this tournament.']);
        }

        DB::transaction(function () use ($team, $captain) {
            $team->captainAssignments()->whereNull('revoked_at')->update(['revoked_at' => now()]);
            TeamCaptain::create([
                'team_id' => $team->id,
                'user_id' => $captain->id,
                'assigned_at' => now(),
            ]);
        });

        return back()->with('status', 'Captain assigned successfully.');
    }

    public function revokeCaptain(Tournament $tournament, Team $team): RedirectResponse
    {
        abort_unless($team->tournament_id === $tournament->id, 404);
        abort_if($tournament->draft?->status !== null && $tournament->draft?->status !== 'setup', 409, 'Captain assignments cannot be changed after the draft starts.');

        $team->captainAssignments()->whereNull('revoked_at')->update(['revoked_at' => now()]);

        return back()->with('status', 'Captain assignment revoked.');
    }

    public function destroy(Tournament $tournament, Team $team): RedirectResponse
    {
        abort_unless($team->tournament_id === $tournament->id, 404);
        abort_if($tournament->draft?->status !== null && $tournament->draft?->status !== 'setup', 409, 'Teams cannot be removed after the draft starts.');

        $team->delete();

        return back()->with('status', 'Team removed successfully.');
    }
}
