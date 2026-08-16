<?php

namespace Tests\Feature;

use App\Models\CricketMatch;
use App\Models\CricketRuleProfile;
use App\Models\Fixture;
use App\Models\MatchInnings;
use App\Models\Team;
use App\Models\Tournament;
use Database\Seeders\CricketRuleProfileSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicMatchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(CricketRuleProfileSeeder::class);
    }

    public function test_public_match_center_lists_scheduled_fixture_and_live_scorecard_link(): void
    {
        [$tournament, $fixture, $match] = $this->publicLiveMatch();

        $this->get(route('public.matches.index', $tournament))
            ->assertOk()
            ->assertSee('Match center')
            ->assertSee('Ali Panthers')
            ->assertSee('Lahore Lions')
            ->assertSee(route('public.matches.show', $match));
    }

    public function test_public_live_state_returns_revision_and_scorecard_collections(): void
    {
        [$tournament, $fixture, $match] = $this->publicLiveMatch();

        $this->getJson(route('public.matches.state', $match))
            ->assertOk()
            ->assertJsonPath('revision', 1)
            ->assertJsonPath('status', 'live')
            ->assertJsonPath('overs_per_innings', 12)
            ->assertJsonPath('innings.0.maximum_overs', 12)
            ->assertJsonPath('innings.0.team', 'AP')
            ->assertJsonPath('innings.0.runs', 0)
            ->assertJsonPath('innings.0.wickets', 0)
            ->assertJsonStructure(['innings' => [['batting', 'bowling', 'recent']]]);
    }

    private function publicLiveMatch(): array
    {
        $profile = CricketRuleProfile::query()->where('slug', 't20-standard')->firstOrFail();
        $tournament = Tournament::create(['name' => 'Public Match Cup', 'slug' => 'public-match-cup-'.uniqid(), 'timezone' => 'Asia/Karachi', 'status' => 'live', 'is_public' => true, 'squad_size' => 2, 'default_pick_duration' => 60, 'cricket_rule_profile_id' => $profile->id]);
        $home = Team::create(['tournament_id' => $tournament->id, 'name' => 'Ali Panthers', 'short_name' => 'AP', 'display_order' => 1, 'is_active' => true]);
        $away = Team::create(['tournament_id' => $tournament->id, 'name' => 'Lahore Lions', 'short_name' => 'LL', 'display_order' => 2, 'is_active' => true]);
        $fixture = Fixture::create(['tournament_id' => $tournament->id, 'home_team_id' => $home->id, 'away_team_id' => $away->id, 'scheduled_at' => now(), 'timezone' => 'Asia/Karachi', 'status' => 'in_progress']);
        $match = CricketMatch::create(['fixture_id' => $fixture->id, 'tournament_id' => $tournament->id, 'rule_profile_id' => $profile->id, 'rule_profile_version' => $profile->version, 'overs_per_innings' => 12, 'status' => 'live', 'revision' => 1]);
        MatchInnings::create(['match_id' => $match->id, 'innings_number' => 1, 'batting_team_id' => $home->id, 'bowling_team_id' => $away->id, 'status' => 'live', 'maximum_overs' => 12, 'started_at' => now()]);
        return [$tournament, $fixture, $match];
    }
}
