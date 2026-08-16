<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\DraftPick;
use App\Models\DraftRound;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CaptainDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_captain_dashboard_lists_active_tournament_assignment(): void
    {
        $captain = User::factory()->create();
        $captain->assignRole('captain');
        $tournament = Tournament::create([
            'name' => 'Captain Cup',
            'slug' => 'captain-cup',
            'status' => 'ready',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);
        $team = Team::create([
            'tournament_id' => $tournament->id,
            'name' => 'Falcons',
            'short_name' => 'FAL',
            'display_order' => 1,
            'is_active' => true,
        ]);
        TeamCaptain::create(['team_id' => $team->id, 'user_id' => $captain->id, 'assigned_at' => now()]);

        $this->actingAs($captain)
            ->get(route('captain.dashboard'))
            ->assertOk()
            ->assertSee('Captain Cup')
            ->assertSee('Falcons');
    }

    public function test_captain_dashboard_shows_selected_squad_and_team_pdf_link(): void
    {
        $captain = User::factory()->create();
        $captain->assignRole('captain');
        $playerUser = User::factory()->create();
        $playerProfile = PlayerProfile::create(['user_id' => $playerUser->id, 'full_name' => 'Selected Player', 'playing_role' => 'Batter']);
        $tournament = Tournament::create([
            'name' => 'Squad Cup',
            'slug' => 'squad-cup',
            'status' => 'ready',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);
        $team = Team::create(['tournament_id' => $tournament->id, 'name' => 'Falcons', 'short_name' => 'FAL', 'display_order' => 1, 'is_active' => true]);
        TeamCaptain::create(['team_id' => $team->id, 'user_id' => $captain->id, 'assigned_at' => now()]);
        $registration = TournamentPlayer::create(['tournament_id' => $tournament->id, 'player_profile_id' => $playerProfile->id, 'status' => 'approved']);
        $draft = Draft::create(['tournament_id' => $tournament->id, 'status' => 'paused', 'revision' => 1]);
        $round = DraftRound::create(['draft_id' => $draft->id, 'round_number' => 1, 'status' => 'active']);
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

        $this->actingAs($captain)
            ->get(route('captain.dashboard'))
            ->assertOk()
            ->assertSee('Selected Player')
            ->assertSee('Download team PDF')
            ->assertSee(route('captain.reports.pdf', [$tournament, 'squads']), false);
    }

    public function test_player_workspace_route_is_role_protected(): void
    {
        $player = User::factory()->create();
        $player->assignRole('player');

        $this->actingAs($player)
            ->get(route('player.dashboard'))
            ->assertRedirect(route('player.tournaments.index'));
    }
}
