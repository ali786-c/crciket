<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Modules\Draft\Services\DraftService;
use Illuminate\Http\Request;

class TournamentController extends Controller
{
    public function index(Request $request, DraftService $draftService)
    {
        $tournaments = Tournament::query()
            ->with(['draft.picks.team', 'teams', 'tournamentPlayers'])
            ->where('is_public', true)
            ->whereIn('status', ['registration', 'ready', 'live', 'completed'])
            ->where(function ($query): void {
                $query->whereIn('status', ['live', 'completed'])
                    ->orWhere(function ($registrationQuery): void {
                        $registrationQuery->whereIn('status', ['registration', 'ready'])
                            ->where(function ($windowQuery): void {
                                $windowQuery->whereNull('registration_opens_at')
                                    ->orWhere('registration_opens_at', '<=', now());
                            })
                            ->where(function ($windowQuery): void {
                                $windowQuery->whereNull('registration_closes_at')
                                    ->orWhere('registration_closes_at', '>=', now());
                            });
                    });
            })
            ->orderByRaw("FIELD(status, 'live', 'registration', 'ready', 'completed')")
            ->orderByDesc('starts_on')
            ->orderByDesc('id')
            ->get()
            ->map(function (Tournament $tournament) use ($draftService): array {
                $state = $tournament->draft ? $draftService->state($tournament->draft) : null;

                return [
                    'tournament' => $tournament,
                    'state' => $state,
                    'teams_count' => $tournament->teams->where('is_active', true)->count(),
                    'approved_players_count' => $tournament->tournamentPlayers->where('status', 'approved')->count(),
                ];
            });

        return view('public.tournaments.index', [
            'tournaments' => $tournaments,
            'liveTournaments' => $tournaments->where('tournament.status', 'live')->values(),
            'registrationTournaments' => $tournaments->whereIn('tournament.status', ['registration', 'ready'])->values(),
            'completedTournaments' => $tournaments->where('tournament.status', 'completed')->values(),
        ]);
    }
}
