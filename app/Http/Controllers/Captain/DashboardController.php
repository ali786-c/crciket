<?php

namespace App\Http\Controllers\Captain;

use App\Http\Controllers\Controller;
use App\Models\TeamCaptain;
use Illuminate\Support\Collection;
use App\Services\ReportService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(ReportService $reportService): View
    {
        $assignments = TeamCaptain::query()
            ->with(['team.tournament.draft'])
            ->where('user_id', request()->user()->id)
            ->whereNull('revoked_at')
            ->latest()
            ->get();

        $tournaments = $assignments
            ->map(fn (TeamCaptain $assignment) => $assignment->team?->tournament)
            ->filter()
            ->unique('id')
            ->values();

        $reports = $tournaments->mapWithKeys(fn ($tournament) => [
            $tournament->id => $reportService->build($tournament, 'captain', request()->user()),
        ]);

        return view('captain.dashboard', compact('assignments', 'tournaments', 'reports'));
    }
}
