<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\Tournament;
use App\Modules\Tournament\Services\FixtureService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FixtureController extends Controller
{
    public function __construct(private readonly FixtureService $fixtures)
    {
    }

    public function index(Tournament $tournament): View
    {
        return view('admin.fixtures.index', [
            'tournament' => $tournament,
            'fixtures' => $tournament->fixtures()->with(['homeTeam', 'awayTeam', 'match'])->paginate(20),
        ]);
    }

    public function create(Tournament $tournament): View
    {
        return view('admin.fixtures.create', [
            'tournament' => $tournament,
            'teams' => $tournament->teams()->where('is_active', true)->orderBy('display_order')->get(),
            'fixture' => new Fixture(['timezone' => $tournament->timezone ?: config('app.timezone', 'UTC')]),
        ]);
    }

    public function store(Request $request, Tournament $tournament): RedirectResponse
    {
        $fixture = $this->fixtures->create($tournament, $this->validated($request), (int) $request->user()->id);
        return redirect()->route('admin.tournaments.fixtures.index', $tournament)->with('status', "Fixture {$fixture->match_number} created successfully.");
    }

    public function edit(Tournament $tournament, Fixture $fixture): View
    {
        $this->ensureBelongsToTournament($tournament, $fixture);
        return view('admin.fixtures.edit', [
            'tournament' => $tournament,
            'teams' => $tournament->teams()->where('is_active', true)->orderBy('display_order')->get(),
            'fixture' => $fixture->load(['homeTeam', 'awayTeam', 'match']),
        ]);
    }

    public function update(Request $request, Tournament $tournament, Fixture $fixture): RedirectResponse
    {
        $this->ensureBelongsToTournament($tournament, $fixture);
        $this->fixtures->update($fixture, $this->validated($request), (int) $request->user()->id);
        return redirect()->route('admin.tournaments.fixtures.index', $tournament)->with('status', 'Fixture updated successfully.');
    }

    public function transition(Request $request, Tournament $tournament, Fixture $fixture): RedirectResponse
    {
        $this->ensureBelongsToTournament($tournament, $fixture);
        $validated = $request->validate(['status' => ['required', 'in:scheduled,in_progress,postponed,completed,cancelled']]);
        $this->fixtures->transition($fixture, $validated['status'], (int) $request->user()->id);
        return back()->with('status', 'Fixture status updated.');
    }

    public function createMatch(Request $request, Tournament $tournament, Fixture $fixture): RedirectResponse
    {
        $this->ensureBelongsToTournament($tournament, $fixture);
        $match = $this->fixtures->createMatch($fixture, (int) $request->user()->id);
        return redirect()->route('admin.tournaments.matches.show', [$tournament, $match])->with('status', 'Operational match created from this fixture.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'home_team_id' => ['required', 'integer'],
            'away_team_id' => ['required', 'integer', 'different:home_team_id'],
            'round_number' => ['nullable', 'integer', 'min:1', 'max:999'],
            'round_name' => ['nullable', 'string', 'max:100'],
            'match_number' => ['nullable', 'integer', 'min:1', 'max:9999'],
            'scheduled_at' => ['required', 'date'],
            'venue' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'timezone' => ['required', 'timezone'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
    }

    private function ensureBelongsToTournament(Tournament $tournament, Fixture $fixture): void
    {
        abort_unless($fixture->tournament_id === $tournament->id, 404);
    }

    public function pdf(Tournament $tournament): \Illuminate\Http\Response
    {
        $fixtures = $tournament->fixtures()->with(['homeTeam', 'awayTeam', 'match'])->get();

        $logoDataUri = null;
        if ($tournament->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($tournament->logo_path)) {
            $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($tournament->logo_path) ?: 'image/png';
            $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($tournament->logo_path));
        }

        $html = view('reports.fixtures-pdf', [
            'tournament' => $tournament,
            'fixtures' => $fixtures,
            'logoDataUri' => $logoDataUri,
            'title' => 'Fixtures & Schedule Report',
        ])->render();

        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . str()->slug($tournament->slug . '-fixtures') . '.pdf"',
        ]);
    }
}
