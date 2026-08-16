<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TournamentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Tournament::query()
            ->withCount(['teams', 'tournamentPlayers', 'matches', 'fixtures', 'auditLogs'])
            ->latest();

        if ($search = trim((string) $request->string('search'))) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('season_name', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('venue', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        return view('super-admin.tournaments.index', [
            'tournaments' => $query->paginate(20)->withQueryString(),
            'search' => $request->string('search')->toString(),
            'selectedStatus' => $request->string('status')->toString(),
            'statuses' => ['draft', 'registration', 'ready', 'live', 'completed', 'cancelled'],
            'statusCounts' => Tournament::query()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status'),
        ]);
    }

    public function show(Tournament $tournament): View
    {
        $tournament->loadCount(['teams', 'tournamentPlayers', 'matches', 'fixtures', 'auditLogs']);
        $tournament->load([
            'teams.activeCaptain.user',
            'matches' => fn ($query) => $query->with(['fixture.homeTeam', 'fixture.awayTeam'])->latest()->take(12),
            'fixtures' => fn ($query) => $query->with(['homeTeam', 'awayTeam'])->latest()->take(12),
            'auditLogs' => fn ($query) => $query->with('user')->latest()->take(15),
        ]);

        return view('super-admin.tournaments.show', compact('tournament'));
    }
}
