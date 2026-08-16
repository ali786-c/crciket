<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\DraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SyncController extends Controller
{
    public function __construct(private readonly DraftService $drafts)
    {
    }

    public function tournament(Request $request, Tournament $tournament): JsonResponse
    {
        abort_unless($tournament->publiclyVisibleNow(), 404);
        $draft = $tournament->draft;
        $draftState = $draft ? $this->drafts->state($draft, $request->user()) : null;
        $matches = $tournament->matches()->whereIn('status', ['live', 'completed', 'result_pending', 'approved'])->with(['homeTeam', 'awayTeam'])->latest('last_event_at')->limit(30)->get();
        $revision = max((int) ($draftState['revision'] ?? 0), (int) $matches->max('revision'));
        $requested = (int) $request->integer('revision', -1);
        return response()->json([
            'data' => [
                'changed' => $requested < 0 || $requested !== $revision,
                'revision' => $revision,
                'server_time' => now()->toIso8601String(),
                'tournament' => $tournament->only(['id', 'name', 'season_name', 'slug', 'status', 'is_public', 'published_at']),
                'draft' => $draftState,
                'fixtures' => $tournament->fixtures()->with(['homeTeam', 'awayTeam'])->latest('scheduled_at')->limit(50)->get(),
                'matches' => $matches,
                'standings' => $tournament->standings()->with('team')->orderByDesc('points')->orderByDesc('net_run_rate')->get(),
            ],
        ]);
    }
}
