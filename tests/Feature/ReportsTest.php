<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_view_complete_reports_and_download_a_pdf(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $tournament = $this->tournament();

        $this->actingAs($admin)
            ->get(route('admin.tournaments.reports.index', $tournament))
            ->assertOk()
            ->assertSee('Operational reports')
            ->assertSee('Player registration report')
            ->assertSee('Audit report');

        $this->actingAs($admin)
            ->get(route('admin.tournaments.reports.pdf', [$tournament, 'summary']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_captain_receives_team_scoped_reports_without_admin_reports(): void
    {
        $captain = User::factory()->create();
        $captain->assignRole('captain');
        $tournament = $this->tournament();
        $team = Team::create([
            'tournament_id' => $tournament->id,
            'name' => 'Captain Team',
            'short_name' => 'CT',
            'display_order' => 1,
        ]);
        TeamCaptain::create(['team_id' => $team->id, 'user_id' => $captain->id, 'assigned_at' => now()]);

        $this->actingAs($captain)
            ->get(route('captain.reports.index', $tournament))
            ->assertOk()
            ->assertSee('Captain reports')
            ->assertDontSee('Audit report')
            ->assertDontSee('Player registration report');

        $this->actingAs($captain)
            ->get(route('captain.reports.pdf', [$tournament, 'squads']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_public_reports_are_available_only_for_public_live_tournaments_and_are_redacted(): void
    {
        $tournament = $this->tournament();

        $this->get(route('public.reports.show', $tournament))
            ->assertOk()
            ->assertSee('Public reports')
            ->assertDontSee('Audit report')
            ->assertDontSee('Player registration report')
            ->assertDontSee('127.0.0.1');

        $this->get(route('public.reports.pdf', [$tournament, 'history']))
            ->assertOk()
            ->assertHeader('Content-Type', 'application/pdf');

        $tournament->update(['is_public' => false]);
        $this->get(route('public.reports.show', $tournament))->assertNotFound();
    }

    private function tournament(): Tournament
    {
        $tournament = Tournament::create([
            'name' => 'Reports Cup',
            'slug' => 'reports-cup-'.uniqid(),
            'season_name' => '2026 Season',
            'status' => 'live',
            'is_public' => true,
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        Draft::create([
            'tournament_id' => $tournament->id,
            'status' => 'paused',
            'current_pick_number' => null,
            'pick_duration' => 60,
            'revision' => 1,
        ]);

        return $tournament;
    }
}
