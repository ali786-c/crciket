<?php

namespace App\Http\Controllers\Player;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TournamentRegistrationController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        return view('player.tournaments', [
            'tournaments' => Tournament::query()
                ->where('is_public', true)
                ->whereIn('status', ['registration', 'ready'])
                ->where(function ($query): void {
                    $query->whereNull('registration_opens_at')->orWhere('registration_opens_at', '<=', now());
                })
                ->where(function ($query): void {
                    $query->whereNull('registration_closes_at')->orWhere('registration_closes_at', '>=', now());
                })
                ->latest()
                ->get(),
            'profile' => $user->playerProfile,
            'registrations' => $user->playerProfile?->tournamentRegistrations()->pluck('status', 'tournament_id') ?? collect(),
        ]);
    }

    public function store(Tournament $tournament): RedirectResponse
    {
        $profile = request()->user()->playerProfile;

        abort_unless($profile, 422, 'Complete your player profile before registering.');
        abort_unless($tournament->publiclyVisibleNow() && in_array($tournament->status, ['registration', 'ready'], true), 409, 'This tournament is not accepting registrations.');

        $tournament->tournamentPlayers()->updateOrCreate(
            ['player_profile_id' => $profile->id],
            ['status' => 'pending', 'reviewed_by' => null, 'reviewed_at' => null]
        );

        return back()->with('status', 'Registration submitted for admin approval.');
    }
}
