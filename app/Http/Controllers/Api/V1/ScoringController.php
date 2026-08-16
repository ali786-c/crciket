<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Services\MatchScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScoringController extends Controller
{
    public function __construct(private readonly MatchScoringService $scoring)
    {
    }

    public function store(Request $request, CricketMatch $match): JsonResponse
    {
        $validated = $request->validate([
            'striker_id' => ['required', 'integer'], 'non_striker_id' => ['required', 'integer', 'different:striker_id'], 'bowler_id' => ['required', 'integer'],
            'runs_off_bat' => ['nullable', 'integer', 'min:0', 'max:6'], 'wides' => ['nullable', 'integer', 'min:0', 'max:6'], 'no_balls' => ['nullable', 'integer', 'min:0', 'max:6'],
            'byes' => ['nullable', 'integer', 'min:0', 'max:6'], 'leg_byes' => ['nullable', 'integer', 'min:0', 'max:6'], 'penalty_runs' => ['nullable', 'integer', 'min:0', 'max:6'],
            'commentary' => ['nullable', 'string', 'max:1000'], 'expected_revision' => ['nullable', 'integer', 'min:0'], 'wicket' => ['nullable', 'array'],
            'wicket.dismissed_player_id' => ['nullable', 'integer'], 'wicket.dismissal_type' => ['nullable', 'string'], 'wicket.fielder_id' => ['nullable', 'integer'],
            'wicket.runs_completed' => ['nullable', 'integer', 'min:0', 'max:6'], 'wicket.notes' => ['nullable', 'string', 'max:500'],
        ]);
        $delivery = $this->scoring->recordDelivery($match, $validated, (int) $request->user()->id, isset($validated['expected_revision']) ? (int) $validated['expected_revision'] : null);
        return response()->json(['data' => ['delivery_id' => $delivery->id, 'revision' => $delivery->revision, 'notation' => $delivery->notation()]]);
    }

    public function nextInnings(Request $request, CricketMatch $match): JsonResponse
    {
        $innings = $this->scoring->startNextInnings($match, (int) $request->user()->id);
        return response()->json(['data' => ['innings_id' => $innings->id, 'match_id' => $match->id]]);
    }

    public function undo(Request $request, CricketMatch $match): JsonResponse
    {
        $validated = $request->validate(['reason' => ['required', 'string', 'min:5', 'max:500']]);
        $this->scoring->undoLastDelivery($match, (int) $request->user()->id, $validated['reason']);
        return response()->json(['message' => 'The latest delivery was voided and the scorecard was rebuilt.']);
    }
}
