<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Services\DraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DraftController extends Controller
{
    public function __construct(private readonly DraftService $draftService)
    {
    }

    public function state(Tournament $tournament, Request $request): JsonResponse
    {
        $this->authorizeCaptain($tournament, $request);
        $draft = $tournament->draft()->firstOrFail();
        return response()->json(['data' => $this->draftService->state($draft, $request->user())]);
    }

    public function pick(Request $request, Tournament $tournament): JsonResponse
    {
        $this->authorizeCaptain($tournament, $request);
        $data = $request->validate(['tournament_player_id' => ['required', 'integer', 'exists:tournament_players,id']]);
        $draft = $tournament->draft()->firstOrFail();
        $player = TournamentPlayer::query()->where('tournament_id', $tournament->id)->findOrFail($data['tournament_player_id']);
        $this->draftService->makePick($draft, $request->user(), $player);
        return response()->json(['data' => $this->draftService->state($draft->fresh(), $request->user())]);
    }

    private function authorizeCaptain(Tournament $tournament, Request $request): void
    {
        abort_unless($request->user()->can('make draft pick'), 403);
        abort_unless($tournament->teams()->whereHas('captainAssignments', fn ($query) => $query->where('user_id', $request->user()->id)->whereNull('revoked_at'))->exists(), 403);
    }
}
