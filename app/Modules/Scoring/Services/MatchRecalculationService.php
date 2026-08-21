<?php

namespace App\Modules\Scoring\Services;

use App\Models\CricketMatch;
use App\Models\MatchDelivery;
use App\Models\MatchInnings;
use Illuminate\Database\DatabaseManager;

class MatchRecalculationService
{
    public function __construct(
        private readonly DatabaseManager $database,
        private readonly MatchScoringService $scoringService
    ) {
    }

    public function recalculateMatch(CricketMatch $match): void
    {
        $this->database->transaction(function () use ($match) {
            $inningsList = MatchInnings::where('match_id', $match->id)->orderBy('innings_number')->get();

            foreach ($inningsList as $innings) {
                $deliveries = $innings->deliveries()->whereNull('voided_at')->get();
                $legal = $deliveries->where('is_legal_delivery', true)->count();
                $wicketsCount = $deliveries->whereNotNull('wicket_id')->count();

                $innings->update([
                    'total_runs' => $deliveries->sum('total_runs'),
                    'wickets' => $wicketsCount,
                    'legal_balls' => $legal,
                ]);

                // Rebuild batsman and bowler stats using modular service
                $this->scoringService->rebuildStats($innings->fresh(['match']));
            }

            // Refresh target for 2nd innings if present
            if ($inningsList->count() >= 2) {
                $first = $inningsList->first();
                $second = $inningsList->get(1);
                if ($first && $second) {
                    $second->update([
                        'target_runs' => $first->total_runs + 1
                    ]);
                }
            }
        });
    }
}
