<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CricketMatch;
use App\Modules\Scoring\Services\MatchScoringService;
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
            'wagon_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'wagon_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);
        $delivery = $this->scoring->recordDelivery($match, $validated, (int) $request->user()->id, isset($validated['expected_revision']) ? (int) $validated['expected_revision'] : null);
        return response()->json(['data' => ['delivery_id' => $delivery->id, 'revision' => $delivery->revision, 'notation' => $delivery->notation()]]);
    }

    public function sync(Request $request, CricketMatch $match): JsonResponse
    {
        $validated = $request->validate([
            'deliveries' => ['required', 'array', 'min:1'],
            'deliveries.*.local_uuid' => ['required', 'string', 'uuid'],
            'deliveries.*.device_timestamp' => ['required', 'string'],
            'deliveries.*.striker_id' => ['required', 'integer'],
            'deliveries.*.non_striker_id' => ['required', 'integer', 'different:deliveries.*.striker_id'],
            'deliveries.*.bowler_id' => ['required', 'integer'],
            'deliveries.*.runs_off_bat' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.wides' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.no_balls' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.byes' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.leg_byes' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.penalty_runs' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.commentary' => ['nullable', 'string', 'max:1000'],
            'deliveries.*.wicket' => ['nullable', 'array'],
            'deliveries.*.wicket.dismissed_player_id' => ['nullable', 'integer'],
            'deliveries.*.wicket.dismissal_type' => ['nullable', 'string'],
            'deliveries.*.wicket.fielder_id' => ['nullable', 'integer'],
            'deliveries.*.wicket.runs_completed' => ['nullable', 'integer', 'min:0', 'max:6'],
            'deliveries.*.wicket.notes' => ['nullable', 'string', 'max:500'],
            'deliveries.*.wagon_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'deliveries.*.wagon_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $actorId = (int) $request->user()->id;

        $responseList = \DB::transaction(function () use ($match, $validated, $actorId) {
            $match = CricketMatch::query()->lockForUpdate()->findOrFail($match->id);
            $localUuids = collect($validated['deliveries'])->pluck('local_uuid')->all();
            
            // Find existing deliveries by local_uuid
            $existingDeliveries = \App\Models\MatchDelivery::query()
                ->where('match_id', $match->id)
                ->whereIn('local_uuid', $localUuids)
                ->get()
                ->keyBy('local_uuid');

            $newDeliveries = collect($validated['deliveries'])
                ->filter(fn ($d) => !$existingDeliveries->has($d['local_uuid']))
                ->sortBy('device_timestamp')
                ->values();

            $results = [];

            // Add already existing ones to result first so client gets acknowledgment
            foreach ($existingDeliveries as $uuid => $delivery) {
                $results[] = [
                    'local_uuid' => $uuid,
                    'delivery_id' => $delivery->id,
                    'revision' => $delivery->revision,
                    'notation' => $delivery->notation(),
                    'status' => 'already_sync',
                ];
            }

            // Process new ones sequentially
            foreach ($newDeliveries as $d) {
                $match = $match->fresh();
                $delivery = $this->scoring->recordDelivery($match, $d, $actorId, (int) $match->revision);

                $results[] = [
                    'local_uuid' => $d['local_uuid'],
                    'delivery_id' => $delivery->id,
                    'revision' => $delivery->revision,
                    'notation' => $delivery->notation(),
                    'status' => 'synced',
                ];
            }

            return $results;
        });

        // Fetch fresh match metrics
        $match = $match->fresh();
        $innings = $match->innings()->whereKey($match->current_innings_id)->first();

        return response()->json([
            'data' => [
                'deliveries' => $responseList,
                'match' => [
                    'id' => $match->id,
                    'status' => $match->status,
                    'revision' => $match->revision,
                    'total_runs' => $innings?->total_runs ?? 0,
                    'wickets' => $innings?->wickets ?? 0,
                    'legal_balls' => $innings?->legal_balls ?? 0,
                ]
            ]
        ]);
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

    public function mvp(CricketMatch $match, \App\Modules\Analytics\Services\MVPPointsService $mvpService): JsonResponse
    {
        return response()->json([
            'data' => $mvpService->getMatchMVP($match)
        ]);
    }

    public function editDelivery(Request $request, \App\Models\MatchDelivery $matchDelivery, \App\Modules\Scoring\Services\MatchRecalculationService $recalcService): JsonResponse
    {
        $validated = $request->validate([
            'striker_id' => ['sometimes', 'integer', 'exists:match_players,id'],
            'non_striker_id' => ['sometimes', 'integer', 'exists:match_players,id', 'different:striker_id'],
            'bowler_id' => ['sometimes', 'integer', 'exists:match_players,id'],
            'runs_off_bat' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'wides' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'no_balls' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'byes' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'leg_byes' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'penalty_runs' => ['sometimes', 'integer', 'min:0', 'max:6'],
            'commentary' => ['nullable', 'string', 'max:1000'],
            'wagon_x' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'wagon_y' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        \DB::transaction(function () use ($matchDelivery, $validated, $recalcService) {
            $matchDelivery->update($validated);

            $matchDelivery->update([
                'total_runs' => $matchDelivery->runs_off_bat + $matchDelivery->wides + $matchDelivery->no_balls + $matchDelivery->byes + $matchDelivery->leg_byes + $matchDelivery->penalty_runs,
                'is_legal_delivery' => ($matchDelivery->wides === 0 && $matchDelivery->no_balls === 0),
            ]);

            $recalcService->recalculateMatch($matchDelivery->match);
        });

        return response()->json([
            'message' => 'Delivery corrected and match scorecards recalculated successfully.',
            'data' => $matchDelivery->fresh(['striker', 'nonStriker', 'bowler', 'wicket']),
        ]);
    }
}
