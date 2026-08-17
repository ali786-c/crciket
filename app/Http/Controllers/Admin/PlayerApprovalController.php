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
        $query = $tournament->tournamentPlayers()
            ->with(['playerProfile.user'])
            ->select('tournament_players.*')
            ->join('player_profiles', 'tournament_players.player_profile_id', '=', 'player_profiles.id');

        // Search by name
        if (request()->filled('search')) {
            $search = request('search');
            $query->where('player_profiles.full_name', 'like', '%' . $search . '%');
        }

        // Filter by location (city)
        if (request()->filled('location')) {
            $location = request('location');
            $query->where('player_profiles.city', $location);
        }

        // Sorting
        $sort = request('sort', 'latest');
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('player_profiles.full_name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('player_profiles.full_name', 'desc');
                break;
            case 'location_asc':
                $query->orderBy('player_profiles.city', 'asc');
                break;
            case 'location_desc':
                $query->orderBy('player_profiles.city', 'desc');
                break;
            case 'latest':
            default:
                $query->orderBy('tournament_players.created_at', 'desc');
                break;
        }

        // Get unique locations of players registered in this tournament
        $locations = \App\Models\PlayerProfile::query()
            ->whereHas('tournamentRegistrations', function ($q) use ($tournament) {
                $q->where('tournament_id', $tournament->id);
            })
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->sort()
            ->values();

        // Paginate and preserve query string parameters
        $registrations = $query->paginate(20)->withQueryString();

        return view('admin.tournaments.players', [
            'tournament' => $tournament,
            'registrations' => $registrations,
            'locations' => $locations,
            'currentSearch' => request('search'),
            'currentLocation' => request('location'),
            'currentSort' => $sort,
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

        $logoDataUri = null;
        if ($tournament->logo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($tournament->logo_path)) {
            $mime = \Illuminate\Support\Facades\Storage::disk('public')->mimeType($tournament->logo_path) ?: 'image/png';
            $logoDataUri = 'data:'.$mime.';base64,'.base64_encode(\Illuminate\Support\Facades\Storage::disk('public')->get($tournament->logo_path));
        }

        $html = view('reports.players-pdf', [
            'tournament' => $tournament,
            'registrations' => $registrations,
            'logoDataUri' => $logoDataUri,
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
    public function store(Tournament $tournament): RedirectResponse
    {
        $data = request()->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'playing_role' => ['required', 'string', 'in:Batter,Bowler,All-rounder,Wicketkeeper'],
            'email' => ['nullable', 'email', 'max:255'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($tournament, $data) {
            $profile = \App\Models\PlayerProfile::query()->with('user')->where('phone', $data['phone'])->first();
            $user = $profile?->user;
            if ($user) {
                $user->update(['name' => $data['full_name']]);
                if (!empty($data['email']) && $user->email !== $data['email']) {
                    request()->validate(['email' => 'unique:users,email,' . $user->id]);
                    $user->update(['email' => $data['email']]);
                }
            } else {
                $email = $data['email'];
                if (!$email) {
                    $baseEmail = \Illuminate\Support\Str::slug($data['full_name'], '.');
                    $cleanPhone = preg_replace('/[^0-9]/', '', $data['phone']);
                    $phoneSuffix = substr($cleanPhone, -4);
                    if ($phoneSuffix !== '') {
                        $baseEmail .= '.' . $phoneSuffix;
                    }
                    $email = $baseEmail . '@cricketdraft.local';

                    $counter = 1;
                    while (\App\Models\User::query()->where('email', $email)->exists()) {
                        $email = $baseEmail . '.' . $counter . '@cricketdraft.local';
                        $counter++;
                    }
                } else {
                    request()->validate(['email' => 'unique:users,email']);
                }

                $user = \App\Models\User::create([
                    'name' => $data['full_name'],
                    'email' => $email,
                    'password' => \Illuminate\Support\Str::random(40),
                ]);
                $user->assignRole('player');
            }

            $profile = \App\Models\PlayerProfile::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'city' => $data['location'],
                    'playing_role' => $data['playing_role'],
                    'is_active' => true,
                ]
            );

            \App\Models\TournamentPlayer::updateOrCreate(
                ['tournament_id' => $tournament->id, 'player_profile_id' => $profile->id],
                [
                    'status' => 'approved',
                    'reviewed_by' => request()->user()->id,
                    'reviewed_at' => now(),
                    'review_notes' => 'Added manually by administrator.',
                ]
            );
        });

        return back()->with('status', 'Player created manually and added to approved pool.');
    }

    public function update(Tournament $tournament, TournamentPlayer $tournamentPlayer): RedirectResponse
    {
        $this->ensureBelongsToTournament($tournament, $tournamentPlayer);

        $data = request()->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'location' => ['required', 'string', 'max:255'],
            'playing_role' => ['required', 'string', 'in:Batter,Bowler,All-rounder,Wicketkeeper'],
            'email' => ['required', 'email', 'max:255'],
            'status' => ['required', 'string', 'in:approved,rejected,pending'],
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($tournamentPlayer, $data) {
            $profile = $tournamentPlayer->playerProfile;
            $user = $profile?->user;

            if ($user) {
                request()->validate([
                    'email' => 'unique:users,email,' . $user->id,
                ]);
                $user->update([
                    'name' => $data['full_name'],
                    'email' => $data['email'],
                ]);
            }

            if ($profile) {
                $profile->update([
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'],
                    'city' => $data['location'],
                    'playing_role' => $data['playing_role'],
                ]);
            }

            $tournamentPlayer->update([
                'status' => $data['status'],
                'reviewed_by' => request()->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return back()->with('status', 'Player profile and registration updated successfully.');
    }
}
