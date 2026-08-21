<?php

namespace App\Modules\Analytics\Services;

use App\Models\Draft;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class ReportService
{
    public function build(Tournament $tournament, string $audience = 'admin', ?User $viewer = null): array
    {
        $draft = $tournament->draft()->with([
            'picks.team',
            'picks.round',
            'picks.tournamentPlayer.playerProfile',
            'picks.selectedBy',
            'rounds.picks',
        ])->first();
        $teams = $tournament->teams()->with('activeCaptain')->orderBy('display_order')->get();
        $picks = $draft?->picks ?? collect();
        $selectedPicks = $picks->where('status', 'selected')->values();

        $history = $picks->map(fn ($pick): array => [
            'pick_number' => $pick->pick_number,
            'round' => $pick->round?->round_number,
            'team' => $pick->team?->name,
            'player' => $pick->tournamentPlayer?->playerProfile?->full_name,
            'playing_role' => $pick->tournamentPlayer?->playerProfile?->playing_role,
            'status' => $pick->status,
            'selected_at' => $pick->selected_at?->toIso8601String(),
            'selected_by' => $audience === 'admin' ? $pick->selectedBy?->name : null,
        ])->values();

        $teamSquads = $teams->map(function ($team) use ($selectedPicks): array {
            $captainUserId = $team->activeCaptain?->user_id;

            $players = $selectedPicks
                ->where('team_id', $team->id)
                ->map(fn ($pick): array => [
                    'pick_number' => $pick->pick_number,
                    'player' => $pick->tournamentPlayer?->playerProfile?->full_name,
                    'playing_role' => $pick->tournamentPlayer?->playerProfile?->playing_role,
                    'is_captain' => $pick->tournamentPlayer?->playerProfile?->user_id === $captainUserId,
                    'selected_at' => $pick->selected_at?->toIso8601String(),
                ])->values();

            return [
                'team_id' => $team->id,
                'team' => $team->name,
                'short_name' => $team->short_name,
                'selected_count' => $players->count(),
                'players' => $players,
            ];
        })->values();

        $summary = [
            'teams' => $teams->where('is_active', true)->count(),
            'registered_players' => $tournament->tournamentPlayers()->count(),
            'approved_players' => $tournament->tournamentPlayers()->where('status', 'approved')->count(),
            'selected_players' => $selectedPicks->count(),
            'total_picks' => $picks->count(),
            'completed_picks' => $picks->whereIn('status', ['selected', 'skipped'])->count(),
            'pending_picks' => $picks->where('status', 'pending')->count(),
            'skipped_picks' => $picks->where('status', 'skipped')->count(),
            'rounds' => $draft?->rounds->count() ?? 0,
            'draft_status' => $draft?->status ?? 'not_configured',
            'draft_started_at' => $draft?->created_at?->toIso8601String(),
            'draft_completed_at' => $draft?->completed_at?->toIso8601String(),
        ];

        $timer = [
            'extension_count' => $picks->sum('extension_count'),
            'extended_seconds' => $picks->sum('total_extension_seconds'),
            'expired_picks' => $picks->whereNotNull('expired_at')->count(),
            'skipped_picks' => $picks->where('status', 'skipped')->count(),
        ];

        $logoDataUri = null;
        if ($tournament->logo_path && Storage::disk('public')->exists($tournament->logo_path)) {
            $mime = Storage::disk('public')->mimeType($tournament->logo_path) ?: 'image/png';
            $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(Storage::disk('public')->get($tournament->logo_path));
        }

        $report = [
            'audience' => $audience,
            'logo_data_uri' => $logoDataUri,
            'tournament' => $tournament,
            'summary' => $summary,
            'team_squads' => $teamSquads,
            'history' => $history,
            'timer' => $timer,
            'registrations' => collect(),
            'audit_logs' => collect(),
            'captain_team' => null,
        ];

        if ($audience === 'admin') {
            $report['registrations'] = $tournament->tournamentPlayers()
                ->with(['playerProfile.user', 'reviewer'])
                ->latest()
                ->get()
                ->map(fn ($registration): array => [
                    'player' => $registration->playerProfile?->full_name,
                    'role' => $registration->playerProfile?->playing_role,
                    'email' => $registration->playerProfile?->user?->email,
                    'status' => $registration->status,
                    'reviewed_by' => $registration->reviewer?->name,
                    'reviewed_at' => $registration->reviewed_at?->toIso8601String(),
                ])->values();
            $report['audit_logs'] = $tournament->auditLogs()
                ->with('user')
                ->latest()
                ->limit(500)
                ->get()
                ->map(fn ($log): array => [
                    'action' => $log->action,
                    'user' => $log->user?->name,
                    'created_at' => $log->created_at?->toIso8601String(),
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                ])->values();
        }

        if ($audience === 'captain' && $viewer) {
            $team = $teams->first(fn ($candidate) => $candidate->activeCaptain?->user_id === $viewer->id);
            $report['captain_team'] = $team?->name;
            $report['team_squads'] = $teamSquads->filter(fn (array $squad) => $squad['team_id'] === $team?->id)->values();
            $report['history'] = $history->filter(fn (array $pick) => $pick['team'] === $team?->name)->values();
        }

        if ($audience === 'public') {
            $report['registrations'] = collect();
            $report['audit_logs'] = collect();
            $report['history'] = $history->map(function (array $pick): array {
                unset($pick['selected_by']);
                return $pick;
            });
        }

        return $report;
    }

    public function availableReportTypes(string $audience): array
    {
        return match ($audience) {
            'admin' => [
                'summary' => 'Tournament summary',
                'history' => 'Draft history',
                'squads' => 'Team squad report',
                'registrations' => 'Player registration report',
                'timer' => 'Timer report',
                'audit' => 'Audit report',
            ],
            'captain' => [
                'summary' => 'Tournament summary',
                'history' => 'My team draft history',
                'squads' => 'My team squad report',
                'timer' => 'Draft timer summary',
            ],
            default => [
                'summary' => 'Public tournament summary',
                'history' => 'Public draft history',
                'squads' => 'Public team squads',
                'timer' => 'Public draft status',
            ],
        };
    }
}
