<?php

namespace Tests\Feature\Admin;

use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeamCaptainTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_assign_and_revoke_a_captain(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $captain = User::factory()->create();
        $captain->assignRole('captain');
        $tournament = Tournament::create(['name' => 'Captain Cup', 'slug' => 'captain-cup', 'timezone' => 'Asia/Karachi']);
        $team = Team::create(['tournament_id' => $tournament->id, 'name' => 'Green XI', 'display_order' => 1]);

        $response = $this->actingAs($admin)->post(route('admin.tournaments.teams.captain.assign', [$tournament, $team]), ['user_id' => $captain->id]);
        $response->assertRedirect();
        $this->assertDatabaseHas('team_captains', ['team_id' => $team->id, 'user_id' => $captain->id, 'revoked_at' => null]);

        $this->actingAs($admin)->delete(route('admin.tournaments.teams.captain.revoke', [$tournament, $team]))->assertRedirect();
        $this->assertNotNull(TeamCaptain::query()->where('team_id', $team->id)->first()->revoked_at);
    }

    public function test_a_captain_can_be_assigned_to_two_teams_in_one_tournament(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $captain = User::factory()->create();
        $captain->assignRole('captain');
        $tournament = Tournament::create(['name' => 'Conflict Cup', 'slug' => 'conflict-cup', 'timezone' => 'Asia/Karachi']);
        $firstTeam = Team::create(['tournament_id' => $tournament->id, 'name' => 'First XI', 'display_order' => 1]);
        $secondTeam = Team::create(['tournament_id' => $tournament->id, 'name' => 'Second XI', 'display_order' => 2]);

        $this->actingAs($admin)->post(route('admin.tournaments.teams.captain.assign', [$tournament, $firstTeam]), ['user_id' => $captain->id])->assertRedirect();
        $response = $this->actingAs($admin)->post(route('admin.tournaments.teams.captain.assign', [$tournament, $secondTeam]), ['user_id' => $captain->id]);

        $response->assertRedirect();
        $this->assertDatabaseHas('team_captains', ['team_id' => $secondTeam->id, 'user_id' => $captain->id, 'revoked_at' => null]);
    }
}
