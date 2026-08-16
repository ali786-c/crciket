<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreDraftSetupRequest;
use App\Models\Draft;
use App\Models\DraftRound;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DraftSetupController extends Controller
{
    public function edit(Tournament $tournament): View
    {
        $draft = Draft::query()->firstOrCreate(
            ['tournament_id' => $tournament->id],
            ['status' => 'setup']
        );

        return view('admin.tournaments.draft-setup', [
            'tournament' => $tournament->load('teams'),
            'draft' => $draft->load('rounds.picks.team'),
            'isLocked' => $draft->status !== 'setup',
        ]);
    }

    public function update(StoreDraftSetupRequest $request, Tournament $tournament): RedirectResponse
    {
        $payload = $request->validated();
        $teamIds = $tournament->teams()->pluck('id')->all();
        $pickNumbers = [];

        foreach ($payload['rounds'] as $round) {
            foreach ($round['picks'] as $pick) {
                if (! in_array((int) $pick['team_id'], $teamIds, true)) {
                    throw ValidationException::withMessages([
                        'rounds' => 'Every assigned team must belong to this tournament.',
                    ]);
                }

                if (in_array($pick['pick_number'], $pickNumbers, true)) {
                    throw ValidationException::withMessages([
                        'rounds' => 'Pick numbers must be unique across the complete draft.',
                    ]);
                }

                $pickNumbers[] = $pick['pick_number'];
            }
        }

        DB::transaction(function () use ($payload, $tournament, $pickNumbers) {
            $draft = Draft::query()->firstOrCreate(
                ['tournament_id' => $tournament->id],
                ['status' => 'setup']
            );

            $draft = Draft::query()->lockForUpdate()->findOrFail($draft->id);

            if ($draft->status !== 'setup') {
                throw ValidationException::withMessages([
                    'draft' => 'Draft configuration cannot be changed after the draft starts.',
                ]);
            }

            $draft->picks()->delete();
            $draft->rounds()->delete();

            foreach ($payload['rounds'] as $roundPayload) {
                $round = $draft->rounds()->create([
                    'round_number' => $roundPayload['round_number'],
                    'name' => $roundPayload['name'] ?? null,
                    'status' => 'pending',
                ]);

                foreach ($roundPayload['picks'] as $pickPayload) {
                    $round->picks()->create([
                        'draft_id' => $draft->id,
                        'team_id' => $pickPayload['team_id'],
                        'pick_number' => $pickPayload['pick_number'],
                        'pick_duration' => $pickPayload['pick_duration'],
                        'status' => 'pending',
                    ]);
                }
            }

            $draft->update([
                'current_pick_number' => min($pickNumbers),
                'revision' => $draft->revision + 1,
            ]);
        });

        return redirect()
            ->route('admin.tournaments.show', $tournament)
            ->with('status', 'Draft rounds and pick assignments saved successfully.');
    }
}
