<?php

namespace Tests\Feature\Admin;

use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_create_a_captain_account(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->post(route('admin.users.store'), [
            'name' => 'New Captain',
            'email' => 'newcaptain@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect();

        $captain = User::query()->where('email', 'newcaptain@example.test')->firstOrFail();
        $this->assertTrue($captain->hasRole('captain'));
    }

    public function test_admin_can_promote_and_revoke_a_player_captain_role(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $player = User::factory()->create();
        $player->assignRole('player');
        $tournament = Tournament::create([
            'name' => 'Assignment Cup',
            'slug' => 'assignment-cup',
            'status' => 'registration',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);
        $team = Team::create([
            'tournament_id' => $tournament->id,
            'name' => 'Assignment Team',
            'short_name' => 'AT',
            'display_order' => 1,
        ]);
        TeamCaptain::create([
            'team_id' => $team->id,
            'user_id' => $player->id,
            'assigned_at' => now(),
        ]);

        $this->actingAs($admin)->post(route('admin.users.promote-captain', $player))->assertRedirect();
        $this->assertTrue($player->fresh()->hasRole('captain'));
        $this->assertFalse($player->fresh()->hasRole('player'));

        $this->actingAs($admin)->delete(route('admin.users.revoke-captain', $player))->assertRedirect();
        $this->assertFalse($player->fresh()->hasRole('captain'));
        $this->assertTrue($player->fresh()->hasRole('player'));
        $this->assertDatabaseHas('team_captains', [
            'team_id' => $team->id,
            'user_id' => $player->id,
        ]);
        $this->assertNull($player->fresh()->teamCaptainAssignments()->whereNull('revoked_at')->first());
        $this->assertNotNull($player->fresh()->teamCaptainAssignments()->whereNotNull('revoked_at')->first());
    }

    public function test_non_admin_cannot_manage_users(): void
    {
        $player = User::factory()->create();
        $player->assignRole('player');

        $this->actingAs($player)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }
}
