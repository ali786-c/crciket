<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tournament;
use Illuminate\View\View;

class AuditLogController extends Controller
{
    public function index(Tournament $tournament): View
    {
        return view('admin.tournaments.audit-logs', [
            'tournament' => $tournament,
            'logs' => AuditLog::query()
                ->with('user')
                ->where('tournament_id', $tournament->id)
                ->latest()
                ->paginate(25),
        ]);
    }
}
