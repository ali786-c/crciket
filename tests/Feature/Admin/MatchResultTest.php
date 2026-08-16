<?php

namespace Tests\Feature\Admin;

use App\Models\CricketMatch;
use App\Models\CricketRuleProfile;
use App\Models\MatchInnings;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\CricketRuleProfileSeeder;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MatchResultTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolePermissionSeeder::class, CricketRuleProfileSeeder::class]);
    }

    public function test_completed_match_result_is_submitted_then_approved_into_standings(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $profile = CricketRuleProfile::query()->where('slug', 't20-standard')->firstOrFail();
        $tournament = Tournament::create(['name' => 'Results Cup', 'slug' => 'results-cup-'.uniqid(), 'status' => 'live', 'is_public' => true, 'timezone' => 'Asia/Karachi', 'squad_size' => 2, 'default_pick_duration' => 60, 'cricket_rule_profile_id' => $profile->id]);
        $firstTeam = Team::create(['tournament_id' => $tournament->id, 'name' => 'First XI', 'short_name' => 'FST', 'display_order' => 1]);
        $secondTeam = Team::create(['tournament_id' => $tournament->id, 'name' => 'Second XI', 'short_name' => 'SND', 'display_order' => 2]);
        $match = CricketMatch::create(['tournament_id' => $tournament->id, 'rule_profile_id' => $profile->id, 'rule_profile_version' => 1, 'status' => 'completed', 'completed_at' => now(), 'revision' => 4, 'created_by' => $admin->id]);
        $first = MatchInnings::create(['match_id' => $match->id, 'innings_number' => 1, 'batting_team_id' => $firstTeam->id, 'bowling_team_id' => $secondTeam->id, 'status' => 'completed', 'maximum_overs' => 20, 'total_runs' => 100, 'legal_balls' => 120, 'completed_reason' => 'overs_complete', 'completed_at' => now()]);
        MatchInnings::create(['match_id' => $match->id, 'innings_number' => 2, 'batting_team_id' => $secondTeam->id, 'bowling_team_id' => $firstTeam->id, 'status' => 'completed', 'target_runs' => 101, 'maximum_overs' => 20, 'total_runs' => 80, 'legal_balls' => 120, 'completed_reason' => 'overs_complete', 'completed_at' => now()]);
        $match->update(['current_innings_id' => $first->id]);

        $this->actingAs($admin)->post(route('admin.matches.result.submit', $match))->assertRedirect();
        $this->assertSame('result_pending', $match->fresh()->status);
        $this->assertSame($firstTeam->id, $match->fresh()->winner_team_id);

        $this->actingAs($admin)->post(route('admin.matches.result.approve', $match))->assertRedirect();
        $standing = $tournament->standings()->where('team_id', $firstTeam->id)->firstOrFail();
        $this->assertSame(2, $standing->points);
        $this->assertSame(1, $standing->wins);
        $this->assertSame(1, $standing->played);
        $this->assertSame(100, (int) $standing->runs_for);
    }
}
