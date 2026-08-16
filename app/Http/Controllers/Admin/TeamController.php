<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTeamRequest;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TeamController extends Controller
{
    public function index(Tournament $tournament): View
    {
        return view('admin.tournaments.teams', [
            'tournament' => $tournament->load('teams.activeCaptain.user'),
            'captainCandidates' => User::query()
                ->where(function ($query) use ($tournament) {
                    $query->whereHas('playerProfile.tournamentRegistrations', function ($q) use ($tournament) {
                        $q->where('tournament_id', $tournament->id)
                          ->where('status', 'approved');
                    })
                    ->orWhere(function ($q) use ($tournament) {
                        $q->whereHas('roles', function ($sub) {
                            $sub->where('name', 'captain');
                        })
                        ->whereDoesntHave('teamCaptainAssignments', function ($sub) use ($tournament) {
                            $sub->whereNull('revoked_at')
                                ->whereHas('team', function ($teamQuery) use ($tournament) {
                                    $teamQuery->where('tournament_id', '<>', $tournament->id);
                                });
                        });
                    });
                })
                ->orderBy('name')
                ->get(),
        ]);
    }

    public function store(StoreTeamRequest $request, Tournament $tournament): RedirectResponse
    {
        $tournament->teams()->create($request->validated());

        return back()->with('status', 'Team added successfully.');
    }

    public function assignCaptain(Request $request, Tournament $tournament, Team $team): RedirectResponse
    {
        abort_unless($team->tournament_id === $tournament->id, 404);
        abort_if($tournament->draft?->status !== null && $tournament->draft?->status !== 'setup', 409, 'Captain assignments cannot be changed after the draft starts.');

        $data = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id']]);
        $captain = User::query()
            ->where(function ($query) use ($tournament) {
                $query->whereHas('playerProfile.tournamentRegistrations', function ($q) use ($tournament) {
                    $q->where('tournament_id', $tournament->id)
                      ->where('status', 'approved');
                })
                ->orWhere(function ($q) use ($tournament) {
                    $q->whereHas('roles', function ($sub) {
                        $sub->where('name', 'captain');
                    })
                    ->whereDoesntHave('teamCaptainAssignments', function ($sub) use ($tournament) {
                        $sub->whereNull('revoked_at')
                            ->whereHas('team', function ($teamQuery) use ($tournament) {
                                $teamQuery->where('tournament_id', '<>', $tournament->id);
                            });
                    });
                });
            })
            ->findOrFail($data['user_id']);

        DB::transaction(function () use ($team, $captain) {
            $team->captainAssignments()->whereNull('revoked_at')->update(['revoked_at' => now()]);
            TeamCaptain::create([
                'team_id' => $team->id,
                'user_id' => $captain->id,
                'assigned_at' => now(),
            ]);

            if (! $captain->hasRole('captain')) {
                $captain->assignRole('captain');
            }
        });

        return back()->with('status', 'Captain assigned successfully.');
    }

    public function revokeCaptain(Tournament $tournament, Team $team): RedirectResponse
    {
        abort_unless($team->tournament_id === $tournament->id, 404);
        abort_if($tournament->draft?->status !== null && $tournament->draft?->status !== 'setup', 409, 'Captain assignments cannot be changed after the draft starts.');

        $team->captainAssignments()->whereNull('revoked_at')->update(['revoked_at' => now()]);

        return back()->with('status', 'Captain assignment revoked.');
    }

    public function destroy(Tournament $tournament, Team $team): RedirectResponse
    {
        abort_unless($team->tournament_id === $tournament->id, 404);
        abort_if($tournament->draft?->status !== null && $tournament->draft?->status !== 'setup', 409, 'Teams cannot be removed after the draft starts.');

        $team->delete();

        return back()->with('status', 'Team removed successfully.');
    }

    public function exportTournamentCaptains(Tournament $tournament): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $captains = User::query()
            ->whereHas('teamCaptainAssignments', function ($query) use ($tournament) {
                $query->whereNull('revoked_at')
                    ->whereHas('team', function ($sub) use ($tournament) {
                        $sub->where('tournament_id', $tournament->id);
                    });
            })
            ->get();

        $captainsData = [];

        foreach ($captains as $captain) {
            $newPassword = 'CD-' . rand(10000, 99999);
            $captain->update([
                'password' => \Illuminate\Support\Facades\Hash::make($newPassword)
            ]);
            $captainsData[] = [
                'name' => $captain->name,
                'email' => $captain->email,
                'password' => $newPassword,
            ];
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="tournament_' . $tournament->slug . '_captains.csv"',
        ];

        $callback = function () use ($captainsData) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Name', 'Email', 'Password']);

            foreach ($captainsData as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function pdf(Tournament $tournament): \Illuminate\Http\Response
    {
        $tournament->load('teams.activeCaptain.user');
        $html = view('reports.teams-pdf', [
            'tournament' => $tournament,
            'title' => 'Teams & Captains Report',
        ])->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . str()->slug($tournament->slug . '-teams') . '.pdf"',
        ]);
    }
}
