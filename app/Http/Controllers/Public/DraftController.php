<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Services\DraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class DraftController extends Controller
{
    public function __construct(private readonly DraftService $draftService)
    {
    }

    public function index(): View
    {
        $tournaments = Tournament::query()
            ->with(['draft', 'teams'])
            ->where('status', 'live')
            ->where('is_public', true)
            ->whereHas('draft')
            ->orderByDesc('starts_on')
            ->orderByDesc('id')
            ->get();

        $tournaments = $tournaments->map(function (Tournament $tournament) {
            return [
                'tournament' => $tournament,
                'state' => $this->draftService->state($tournament->draft),
                'teams_count' => $tournament->teams->where('is_active', true)->count(),
            ];
        });

        return view('public.live-center', compact('tournaments'));
    }

    public function show(Tournament $tournament): View
    {
        abort_unless($tournament->status === 'live' && $tournament->is_public, 404);
        $draft = $tournament->draft()->firstOrFail();

        return view('public.draft', [
            'tournament' => $tournament,
            'state' => $this->draftService->state($draft),
        ]);
    }

    public function state(Tournament $tournament): JsonResponse
    {
        abort_unless($tournament->status === 'live' && $tournament->is_public, 404);
        $draft = $tournament->draft()->firstOrFail();

        return response()->json($this->draftService->state($draft));
    }
}
