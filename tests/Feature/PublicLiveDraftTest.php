<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\DraftPick;
use App\Models\DraftRound;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLiveDraftTest extends TestCase
{
    use RefreshDatabase;

    public function test_live_center_lists_only_currently_live_tournaments(): void
    {
        $live = $this->createLiveTournament('Live Selector Cup');
        Tournament::create([
            'name' => 'Registration Cup',
            'slug' => 'registration-cup',
            'status' => 'registration',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        $this->get(route('public.live.center'))
            ->assertOk()
            ->assertSee('Live Selector Cup')
            ->assertSee(route('public.draft.show', $live))
            ->assertDontSee('Registration Cup');
    }

    public function test_selected_players_are_available_in_real_time_public_state(): void
    {
        [$tournament, $team, $registration] = $this->createLiveTournamentWithSelectedPlayer();
        $unselectedUser = User::factory()->create();
        $unselectedProfile = PlayerProfile::create([
            'user_id' => $unselectedUser->id,
            'full_name' => 'Private Unselected Player',
            'playing_role' => 'Bowler',
        ]);
        TournamentPlayer::create([
            'tournament_id' => $tournament->id,
            'player_profile_id' => $unselectedProfile->id,
            'status' => 'approved',
        ]);

        $this->get(route('public.draft.show', $tournament))
            ->assertOk()
            ->assertSee('Selected squads')
            ->assertSee('Who has been picked?')
            ->assertSee('teamSquads');

        $this->get(route('public.draft.state', $tournament))
            ->assertOk()
            ->assertJsonPath('summary.selected', 1)
            ->assertJsonPath('picks.0.status', 'selected')
            ->assertJsonPath('picks.0.team.name', $team->name)
            ->assertJsonPath('picks.0.player.full_name', $registration->playerProfile->full_name)
            ->assertJsonPath('available_players', [])
            ->assertJsonPath('remaining_players', [])
            ->assertDontSee('Private Unselected Player');
    }

    public function test_non_live_tournaments_cannot_open_public_live_board(): void
    {
        $tournament = Tournament::create([
            'name' => 'Completed Cup',
            'slug' => 'completed-cup',
            'status' => 'completed',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        $this->get(route('public.draft.show', $tournament))->assertNotFound();
    }

    private function createLiveTournament(string $name): Tournament
    {
        $tournament = Tournament::create([
            'name' => $name,
            'slug' => str($name)->slug().'-'.uniqid(),
            'status' => 'live',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        Draft::create([
            'tournament_id' => $tournament->id,
            'status' => 'live',
            'revision' => 1,
        ]);

        return $tournament;
    }

    private function createLiveTournamentWithSelectedPlayer(): array
    {
        $tournament = $this->createLiveTournament('Selected Squad Cup');
        $team = Team::create([
            'tournament_id' => $tournament->id,
            'name' => 'Selected Panthers',
            'short_name' => 'SP',
            'display_order' => 1,
        ]);
        $user = User::factory()->create();
        $profile = PlayerProfile::create([
            'user_id' => $user->id,
            'full_name' => 'Real Selected Player',
            'playing_role' => 'Batter',
        ]);
        $registration = TournamentPlayer::create([
            'tournament_id' => $tournament->id,
            'player_profile_id' => $profile->id,
            'status' => 'approved',
        ]);
        $draft = $tournament->draft;
        $round = DraftRound::create([
            'draft_id' => $draft->id,
            'round_number' => 1,
            'name' => 'Round 1',
            'status' => 'active',
        ]);
        DraftPick::create([
            'draft_id' => $draft->id,
            'draft_round_id' => $round->id,
            'team_id' => $team->id,
            'pick_number' => 1,
            'pick_duration' => 60,
            'status' => 'selected',
            'tournament_player_id' => $registration->id,
            'selected_at' => now(),
        ]);

        return [$tournament, $team, $registration];
    }
}
