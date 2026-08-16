<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminPlayerController extends Controller
{
    public function index(Request $request, Tournament $tournament): JsonResponse
    {
        $query = $tournament->tournamentPlayers()->with('playerProfile.user')->latest();
        if ($request->filled('status')) {
            $query->where('status', $request->string('status')->toString());
        }
        return response()->json(['data' => $query->paginate(30)]);
    }

    public function approve(Request $request, Tournament $tournament, TournamentPlayer $registration): JsonResponse
    {
        $this->belongs($tournament, $registration);
        $registration->update(['status' => 'approved', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now(), 'review_notes' => null]);
        return response()->json(['data' => $registration->fresh()->load('playerProfile.user'), 'message' => 'Player approved for this tournament.']);
    }

    public function reject(Request $request, Tournament $tournament, TournamentPlayer $registration): JsonResponse
    {
        $this->belongs($tournament, $registration);
        $data = $request->validate(['review_notes' => ['nullable', 'string', 'max:2000']]);
        $registration->update(['status' => 'rejected', 'reviewed_by' => $request->user()->id, 'reviewed_at' => now(), 'review_notes' => $data['review_notes'] ?? null]);
        return response()->json(['data' => $registration->fresh()->load('playerProfile.user'), 'message' => 'Player registration rejected.']);
    }

    private function belongs(Tournament $tournament, TournamentPlayer $registration): void
    {
        abort_unless($registration->tournament_id === $tournament->id, 404);
    }
}
