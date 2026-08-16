<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PlayerApprovalController extends Controller
{
    public function index(Tournament $tournament): View
    {
        return view('admin.tournaments.players', [
            'tournament' => $tournament,
            'registrations' => $tournament->tournamentPlayers()->with('playerProfile.user')->latest()->paginate(20),
        ]);
    }

    public function approve(Tournament $tournament, TournamentPlayer $tournamentPlayer): RedirectResponse
    {
        $this->ensureBelongsToTournament($tournament, $tournamentPlayer);
        $tournamentPlayer->update([
            'status' => 'approved',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
            'review_notes' => null,
        ]);

        return back()->with('status', 'Player approved for this tournament.');
    }

    public function reject(Tournament $tournament, TournamentPlayer $tournamentPlayer): RedirectResponse
    {
        $this->ensureBelongsToTournament($tournament, $tournamentPlayer);
        $tournamentPlayer->update([
            'status' => 'rejected',
            'reviewed_by' => request()->user()->id,
            'reviewed_at' => now(),
            'review_notes' => request('review_notes'),
        ]);

        return back()->with('status', 'Player registration rejected.');
    }

    private function ensureBelongsToTournament(Tournament $tournament, TournamentPlayer $registration): void
    {
        abort_unless($registration->tournament_id === $tournament->id, 404);
    }

    public function pdf(Tournament $tournament): \Illuminate\Http\Response
    {
        $registrations = $tournament->tournamentPlayers()->with('playerProfile.user')->latest()->get();
        $html = view('reports.players-pdf', [
            'tournament' => $tournament,
            'registrations' => $registrations,
            'title' => 'Player Registrations Report',
        ])->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . str()->slug($tournament->slug . '-players') . '.pdf"',
        ]);
    }
}
