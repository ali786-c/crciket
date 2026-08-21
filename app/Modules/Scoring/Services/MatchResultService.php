<?php

namespace App\Modules\Scoring\Services;

use App\Models\CricketMatch;
use App\Models\Tournament;
use App\Modules\Tournament\Services\StandingsService;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class MatchResultService
{
    public function __construct(private readonly DatabaseManager $database, private readonly StandingsService $standings)
    {
    }

    public function submit(CricketMatch $match, int $actorId): CricketMatch
    {
        return $this->database->transaction(function () use ($match, $actorId) {
            $match = CricketMatch::query()->with(['innings.battingTeam', 'innings.bowlingTeam', 'ruleProfile'])->lockForUpdate()->findOrFail($match->id);
            if ($match->status !== 'completed') $this->fail('match', 'Only a completed match can be submitted for result approval.');
            $innings = $match->innings->sortBy('innings_number')->values();
            if ($innings->count() < ((int) $match->ruleProfile->innings_per_side * 2)) $this->fail('match', 'Both teams’ configured innings must be completed first.');
            $first = $innings->first();
            $second = $innings->get(1);
            $resultType = 'tie';
            $winner = null;
            $summary = 'Match tied';
            if ($second && $second->total_runs >= (int) $first->total_runs + 1) {
                $winner = $second->batting_team_id;
                $resultType = 'win';
                $summary = $second->battingTeam?->short_name.' won by '.max(0, (int) ($match->ruleProfile->maximum_wickets - $second->wickets)).' wickets';
            } elseif ($second && $first->total_runs > $second->total_runs) {
                $winner = $first->batting_team_id;
                $resultType = 'win';
                $summary = $first->battingTeam?->short_name.' won by '.((int) $first->total_runs - (int) $second->total_runs).' runs';
            }
            $match->update(['winner_team_id' => $winner, 'result_type' => $resultType, 'result_summary' => $summary, 'result_submitted_at' => now(), 'result_submitted_by' => $actorId, 'status' => 'result_pending', 'revision' => $match->revision + 1, 'last_event_at' => now(), 'updated_by' => $actorId]);
            return $match->fresh(['winner', 'innings']);
        });
    }

    public function approve(CricketMatch $match, int $actorId): CricketMatch
    {
        return $this->database->transaction(function () use ($match, $actorId) {
            $match = CricketMatch::query()->lockForUpdate()->findOrFail($match->id);
            if ($match->status !== 'result_pending') $this->fail('match', 'Only a submitted result can be approved.');
            $match->update(['status' => 'approved', 'approved_at' => now(), 'result_approved_by' => $actorId, 'revision' => $match->revision + 1, 'last_event_at' => now(), 'updated_by' => $actorId]);
            $this->standings->rebuild($match->tournament_id);
            return $match->fresh(['winner']);
        });
    }

    private function fail(string $key, string $message): never
    {
        throw ValidationException::withMessages([$key => $message]);
    }
}
