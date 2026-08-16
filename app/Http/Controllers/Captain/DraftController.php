<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\Draft;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Services\DraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DraftController extends Controller
{
    public function __construct(private readonly DraftService $draftService)
    {
    }

    public function show(Tournament $tournament): View
    {
        $this->authorizeCaptain($tournament);
        $draft = $tournament->draft()->firstOrFail();

        return view('captain.draft', [
            'tournament' => $tournament,
            'state' => $this->draftService->state($draft, request()->user()),
        ]);
    }

    public function state(Tournament $tournament): JsonResponse
    {
        $this->authorizeCaptain($tournament);
        $draft = $tournament->draft()->firstOrFail();

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function pick(Request $request, Tournament $tournament): JsonResponse
    {
        $this->authorizeCaptain($tournament);
        $data = $request->validate([
            'tournament_player_id' => ['required', 'integer', 'exists:tournament_players,id'],
        ]);

        $draft = $tournament->draft()->firstOrFail();
        $player = TournamentPlayer::query()->findOrFail($data['tournament_player_id']);
        $this->draftService->makePick($draft, request()->user(), $player);

        return response()->json($this->draftService->state($draft->fresh(), request()->user()));
    }

    private function authorizeCaptain(Tournament $tournament): void
    {
        abort_unless(request()->user()->can('make draft pick'), 403);

        $assigned = $tournament->teams()
            ->whereHas('captainAssignments', function ($query) {
                $query->where('user_id', request()->user()->id)->whereNull('revoked_at');
            })
            ->exists();

        abort_unless($assigned, 403);
    }
}
