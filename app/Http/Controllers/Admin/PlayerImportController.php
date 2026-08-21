<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Tournament;
use App\Modules\Tournament\Services\PlayerCsvImportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PlayerImportController extends Controller
{
    public function __construct(private readonly PlayerCsvImportService $importer)
    {
    }

    public function template(): BinaryFileResponse
    {
        return response()->download(storage_path('app/player-import-template.csv'), 'player-import-template.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        $validated = $request->validate([
            'players_csv' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
        ]);
        $result = $this->importer->import($tournament, $validated['players_csv'], (int) $request->user()->id);
        AuditLog::create([
            'user_id' => $request->user()->id,
            'tournament_id' => $tournament->id,
            'action' => 'tournament.players_csv_imported',
            'metadata' => ['original_filename' => $validated['players_csv']->getClientOriginalName(), ...$result],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
        return back()->with('status', "CSV import complete: {$result['registrations']} new tournament registrations, {$result['created']} new accounts, {$result['updated']} existing accounts updated. All imported players are approved.");
    }
}
