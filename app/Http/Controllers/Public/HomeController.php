<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Modules\Draft\Services\DraftService;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __invoke(Request $request, DraftService $draftService)
    {
        $tournaments = Tournament::query()
            ->with([
                'draft.picks.team',
                'teams',
                'tournamentPlayers',
            ])
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
            ->get();

        $operationalTournaments = $tournaments->map(function (Tournament $tournament) use ($draftService) {
            $state = null;

            if ($tournament->draft) {
                $state = $draftService->state($tournament->draft);
            }

            return [
                'tournament' => $tournament,
                'state' => $state,
                'teams_count' => $tournament->teams->where('is_active', true)->count(),
                'approved_players_count' => $tournament->tournamentPlayers->where('status', 'approved')->count(),
            ];
        });

        $liveTournaments = $operationalTournaments
            ->filter(fn (array $item) => $item['tournament']->status === 'live' && $item['state'] !== null)
            ->values();

        $registrationTournaments = $operationalTournaments
            ->filter(fn (array $item) => in_array($item['tournament']->status, ['registration', 'ready'], true))
            ->values();

        $featured = $liveTournaments->first()
            ?? $registrationTournaments->first()
            ?? $operationalTournaments->first();

        return view('welcome', [
            'featured' => $featured,
            'liveTournaments' => $liveTournaments,
            'registrationTournaments' => $registrationTournaments,
            'operationalTournaments' => $operationalTournaments,
            'homepageStats' => [
                'total' => $operationalTournaments->count(),
                'live' => $liveTournaments->count(),
                'registration' => $registrationTournaments->count(),
                'teams' => $operationalTournaments->sum('teams_count'),
            ],
        ]);
    }
}
