<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Services\DraftService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DraftController extends Controller
{
    public function __construct(private readonly DraftService $draftService) {}

    public function show(Tournament $tournament): View
    {
        $draft = $tournament->draft()->with(['picks.team', 'picks.round', 'picks.tournamentPlayer.playerProfile'])->firstOrFail();

        return view('admin.tournaments.draft-control', [
            'tournament' => $tournament,
            'draft' => $draft,
            'state' => $this->draftService->state($draft, request()->user()),
        ]);
    }

    public function state(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function exportHistory(Tournament $tournament): StreamedResponse
    {
        $draft = $tournament->draft()
            ->with(['picks.team', 'picks.round', 'picks.tournamentPlayer.playerProfile', 'picks.selectedBy'])
            ->firstOrFail();

        $filename = Str::slug($tournament->slug.'-draft-history').'-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($draft): void {
            $output = fopen('php://output', 'wb');
            fputcsv($output, [
                'pick_number', 'round', 'team', 'status', 'player', 'playing_role',
                'selected_by', 'pick_duration_seconds', 'extension_count', 'total_extension_seconds',
                'started_at', 'expired_at', 'selected_at', 'skipped_at',
            ]);

            foreach ($draft->picks as $pick) {
                fputcsv($output, [
                    $pick->pick_number,
                    $pick->round?->round_number,
                    $pick->team?->name,
                    $pick->status,
                    $pick->tournamentPlayer?->playerProfile?->full_name,
                    $pick->tournamentPlayer?->playerProfile?->playing_role,
                    $pick->selectedBy?->name,
                    $pick->pick_duration,
                    $pick->extension_count,
                    $pick->total_extension_seconds,
                    $pick->started_at?->toIso8601String(),
                    $pick->expired_at?->toIso8601String(),
                    $pick->selected_at?->toIso8601String(),
                    $pick->skipped_at?->toIso8601String(),
                ]);
            }

            fclose($output);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function start(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->startRound($draft, request()->user());

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function selectPlayer(Request $request, Tournament $tournament): JsonResponse
    {
        $data = $request->validate([
            'pick_number' => ['required', 'integer', 'min:1'],
            'tournament_player_id' => ['required', 'integer', 'exists:tournament_players,id'],
        ]);
        $draft = $tournament->draft()->firstOrFail();
        $player = TournamentPlayer::query()->findOrFail($data['tournament_player_id']);
        $draft = $this->draftService->adminSelectPlayer($draft, request()->user(), $data['pick_number'], $player);

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function reassignPlayer(Request $request, Tournament $tournament): JsonResponse
    {
        $data = $request->validate([
            'from_pick_number' => ['required', 'integer', 'min:1'],
            'to_pick_number' => ['required', 'integer', 'min:1', 'different:from_pick_number'],
        ]);
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->reassignPlayer($draft, request()->user(), $data['from_pick_number'], $data['to_pick_number']);

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function removePlayer(Request $request, Tournament $tournament): JsonResponse
    {
        $data = $request->validate(['pick_number' => ['required', 'integer', 'min:1']]);
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->removeSelectedPlayer($draft, request()->user(), $data['pick_number']);

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function extend(Request $request, Tournament $tournament): JsonResponse
    {
        $data = $request->validate(['seconds' => ['required', 'integer', 'min:1', 'max:3600']]);
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->extendTime($draft, request()->user(), $data['seconds']);

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function skip(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->skipExpiredPick($draft, request()->user());

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function pause(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->pauseDraft($draft, request()->user());

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function resume(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->resumeDraft($draft, request()->user());

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function undo(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->undoLatestPick($draft, request()->user());

        return response()->json($this->draftService->state($draft, request()->user()));
    }

    public function reset(Tournament $tournament): JsonResponse
    {
        $draft = $tournament->draft()->firstOrFail();
        $draft = $this->draftService->resetDraft($draft, request()->user());

        return response()->json($this->draftService->state($draft, request()->user()));
    }
}
