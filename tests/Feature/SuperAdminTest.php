<?php

namespace Tests\Feature;

use App\Models\ApiClient;
use App\Models\AuditLog;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_super_admin_can_open_control_plane_and_regular_admin_cannot(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $admin = $this->userWithRole('admin');

        $this->actingAs($superAdmin)->get(route('super-admin.dashboard'))->assertOk()->assertSee('Super Admin Control Plane');
        $this->actingAs($admin)->get(route('super-admin.dashboard'))->assertForbidden();
    }

    public function test_super_admin_can_register_and_disable_an_api_client(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $payload = ['name' => 'Android App', 'slug' => 'android-app', 'platform' => 'android', 'version' => '1.0.0', 'rate_limit_per_minute' => 240, 'notes' => 'Mobile client'];

        $this->actingAs($superAdmin)->post(route('super-admin.api-clients.store'), $payload)->assertRedirect(route('super-admin.api-clients.index'));
        $client = ApiClient::query()->firstOrFail();
        $this->assertTrue($client->is_active);
        $this->assertDatabaseHas('audit_logs', ['action' => 'api_client.created', 'auditable_id' => $client->id]);

        $this->actingAs($superAdmin)->post(route('super-admin.api-clients.toggle', $client))->assertRedirect();
        $this->assertFalse($client->fresh()->is_active);
    }

    public function test_dashboard_exposes_platform_governance_modules_and_metrics(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $this->userWithRole('player');
        Tournament::create(['name' => 'Fleet Cup', 'slug' => 'fleet-cup', 'status' => 'live', 'is_public' => true, 'timezone' => 'Asia/Karachi']);

        $this->actingAs($superAdmin)->get(route('super-admin.dashboard'))
            ->assertOk()
            ->assertSee('Tournament status map')
            ->assertSee('Identity distribution')
            ->assertSee('Governance modules')
            ->assertSee(route('super-admin.users.index'))
            ->assertSee(route('super-admin.tournaments.index'));
    }

    public function test_super_admin_can_govern_user_roles_and_revoke_all_user_sessions(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $target = $this->userWithRole('player');
        $target->createToken('phone')->accessToken;

        $this->actingAs($superAdmin)->get(route('super-admin.users.index', ['role' => 'player']))->assertOk()->assertSee($target->email);
        $this->actingAs($superAdmin)->post(route('super-admin.users.role.update', $target), ['role' => 'captain'])->assertRedirect();
        $this->assertTrue($target->fresh()->hasRole('captain'));
        $this->actingAs($superAdmin)->post(route('super-admin.users.sessions.revoke', $target))->assertRedirect();
        $this->assertDatabaseMissing('personal_access_tokens', ['tokenable_id' => $target->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'super_admin.user_role_changed', 'auditable_id' => $target->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'super_admin.user_sessions_revoked', 'auditable_id' => $target->id]);
    }

    public function test_super_admin_cannot_remove_the_last_super_admin_role(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $this->actingAs($superAdmin)->post(route('super-admin.users.role.update', $superAdmin), ['role' => 'admin'])
            ->assertSessionHasErrors('role');
        $this->assertTrue($superAdmin->fresh()->hasRole('super_admin'));
    }

    public function test_super_admin_can_filter_tournament_fleet_and_open_oversight_detail(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $tournament = Tournament::create(['name' => 'Fleet Oversight Cup', 'slug' => 'fleet-oversight-cup', 'season_name' => '2026', 'status' => 'completed', 'is_public' => true, 'timezone' => 'Asia/Karachi']);

        $this->actingAs($superAdmin)->get(route('super-admin.tournaments.index', ['status' => 'completed']))
            ->assertOk()->assertSee('Fleet Oversight Cup');
        $this->actingAs($superAdmin)->get(route('super-admin.tournaments.show', $tournament))
            ->assertOk()->assertSee('Operational profile')->assertSee('Tournament audit activity');
    }

    public function test_super_admin_can_filter_and_export_audit_logs_and_view_diagnostics(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        AuditLog::create(['user_id' => $superAdmin->id, 'action' => 'verification.special_event', 'metadata' => ['source' => 'test']]);

        $this->actingAs($superAdmin)->get(route('super-admin.audit-logs.index', ['search' => 'verification.special_event']))
            ->assertOk()->assertSee('verification.special_event');
        $this->actingAs($superAdmin)->get(route('super-admin.audit-logs.export', ['search' => 'verification.special_event']))
            ->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->actingAs($superAdmin)->get(route('super-admin.health'))
            ->assertOk()->assertSee('Runtime profile')->assertSee('Production checklist')->assertSee('Database');
    }

    public function test_super_admin_can_revoke_an_api_session_and_view_governance_pages(): void
    {
        $superAdmin = $this->userWithRole('super_admin');
        $mobileUser = $this->userWithRole('captain');
        $token = $mobileUser->createToken('android-device')->accessToken;

        $this->actingAs($superAdmin)->get(route('super-admin.api-sessions.index'))->assertOk()->assertSee('android-device');
        $this->actingAs($superAdmin)->delete(route('super-admin.api-sessions.revoke', $token))->assertRedirect();
        $this->assertDatabaseMissing('personal_access_tokens', ['id' => $token->id]);
        $this->actingAs($superAdmin)->get(route('super-admin.audit-logs.index'))->assertOk();
        $this->actingAs($superAdmin)->get(route('super-admin.health'))->assertOk()->assertSee('System health');
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);
        return $user;
    }
}
