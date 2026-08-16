<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\CricketMatch;
use App\Models\Fixture;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;
use Laravel\Sanctum\PersonalAccessToken;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $now = now();
        $activeTokenQuery = PersonalAccessToken::query()->where(function ($query) use ($now) {
            $query->whereNull('expires_at')->orWhere('expires_at', '>', $now);
        });

        $roleCounts = collect(['super_admin', 'admin', 'captain', 'player'])
            ->mapWithKeys(fn (string $role) => [$role => User::role($role)->count()]);

        $tournamentStatuses = Tournament::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $matchStatuses = CricketMatch::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $fixtureStatuses = Fixture::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $failedJobCount = Schema::hasTable('failed_jobs') ? DB::table('failed_jobs')->count() : null;
        $pendingRegistrationCount = TournamentPlayer::query()->where('status', 'pending')->count();
        $expiredTokenCount = PersonalAccessToken::query()->whereNotNull('expires_at')->where('expires_at', '<=', $now)->count();

        $alerts = collect([
            $pendingRegistrationCount > 0 ? ['level' => 'warning', 'label' => 'Pending registrations', 'value' => $pendingRegistrationCount, 'href' => route('super-admin.tournaments.index', ['status' => 'registration'])] : null,
            ($tournamentStatuses['live'] ?? 0) > 0 && ($matchStatuses['live'] ?? 0) === 0 ? ['level' => 'info', 'label' => 'Live tournaments without live matches', 'value' => $tournamentStatuses['live'], 'href' => route('super-admin.tournaments.index', ['status' => 'live'])] : null,
            $expiredTokenCount > 0 ? ['level' => 'warning', 'label' => 'Expired API tokens retained', 'value' => $expiredTokenCount, 'href' => route('super-admin.api-sessions.index', ['status' => 'expired'])] : null,
            $failedJobCount !== null && $failedJobCount > 0 ? ['level' => 'danger', 'label' => 'Failed background jobs', 'value' => $failedJobCount, 'href' => route('super-admin.health')] : null,
        ])->filter()->values();

        return view('super-admin.dashboard', [
            'userCount' => User::query()->count(),
            'roleCounts' => $roleCounts,
            'tournamentCount' => Tournament::query()->count(),
            'tournamentStatuses' => $tournamentStatuses,
            'liveMatchCount' => $matchStatuses['live'] ?? 0,
            'matchStatuses' => $matchStatuses,
            'fixtureStatuses' => $fixtureStatuses,
            'apiClientCount' => ApiClient::query()->count(),
            'activeApiClientCount' => ApiClient::query()->where('is_active', true)->count(),
            'activeApiTokenCount' => $activeTokenQuery->count(),
            'expiredTokenCount' => $expiredTokenCount,
            'pendingRegistrationCount' => $pendingRegistrationCount,
            'failedJobCount' => $failedJobCount,
            'liveTournaments' => Tournament::query()->where('status', 'live')->withCount(['matches', 'teams', 'tournamentPlayers'])->with(['matches.fixture.homeTeam', 'matches.fixture.awayTeam'])->latest()->take(8)->get(),
            'liveMatches' => CricketMatch::query()->where('status', 'live')->with(['tournament', 'fixture.homeTeam', 'fixture.awayTeam'])->latest()->take(8)->get(),
            'recentAuditLogs' => AuditLog::query()->with(['user', 'tournament'])->latest()->take(12)->get(),
            'alerts' => $alerts,
        ]);
    }
}
