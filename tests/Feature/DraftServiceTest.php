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
use App\Modules\Draft\Services\DraftService;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class DraftServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_start_a_configured_draft_and_captain_can_make_the_assigned_pick(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);

        $service->startDraft($draft, $admin);
        $selectedPick = $service->makePick($draft->fresh(), $captain, $player);

        $this->assertSame('selected', $selectedPick->status);
        $this->assertSame(1, $selectedPick->pick_number);
        $this->assertSame('paused', $draft->fresh()->status);
        $this->assertNull($draft->fresh()->current_pick_number);
        $this->assertSame('pending', $draft->fresh()->picks()->where('pick_number', 2)->first()->status);

        $state = $service->state($draft->fresh(), $captain);
        $this->assertSame(['total' => 2, 'selected' => 1, 'active' => 0, 'expired' => 0, 'skipped' => 0, 'pending' => 1], $state['summary']);
    }

    public function test_state_payload_changes_for_polling_clients_after_a_pick(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);

        $before = $service->state($draft->fresh());
        $service->makePick($draft->fresh(), $captain, $player);
        $after = $service->state($draft->fresh());
        $captainState = $service->state($draft->fresh(), $captain);

        $this->assertGreaterThan($before['revision'], $after['revision']);
        $this->assertSame(1, $after['summary']['selected']);
        $this->assertSame('selected', $after['picks'][0]['status']);
        $this->assertSame($player->playerProfile->full_name, $after['picks'][0]['player']['full_name']);
        $this->assertNull($after['current_pick_number']);
        $this->assertSame(2, $after['next_pick']['pick_number']);
        $this->assertTrue($after['can_start_next_pick']);
        $this->assertNull($after['timer']['expires_at']);
        $this->assertNotNull($after['timer']['server_now']);
        $this->assertSame(1, $after['team_squads'][0]['selected_count']);
        $this->assertNotNull($captainState['captain_team']);
        $captainSquad = collect($captainState['team_squads'])->firstWhere('id', $captainState['captain_team']['id']);
        $this->assertSame($player->playerProfile->full_name, $captainSquad['selected_players'][0]['full_name']);
        $this->assertSame($player->playerProfile->playing_role, $captainSquad['selected_players'][0]['playing_role']);
        $this->assertNotNull($captainSquad['selected_players'][0]['selected_at']);
        $this->assertSame(0, count($after['remaining_players']));
        $this->assertCount(1, $after['rounds']);
    }

    public function test_undo_restores_the_latest_pick_and_reactivates_its_slot(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);

        $service->startDraft($draft, $admin);
        $service->makePick($draft->fresh(), $captain, $player);
        $service->undoLatestPick($draft->fresh(), $admin);

        $this->assertSame('active', $draft->fresh()->picks()->where('pick_number', 1)->first()->status);
        $this->assertNull($draft->fresh()->picks()->where('pick_number', 1)->first()->tournament_player_id);
        $this->assertSame('pending', $draft->fresh()->picks()->where('pick_number', 2)->first()->status);
        $this->assertSame(1, $draft->fresh()->current_pick_number);
    }

    public function test_only_the_active_team_captain_can_make_the_pick(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $wrongCaptain = User::factory()->create();
        $wrongCaptain->assignRole('captain');
        $secondTeam = $draft->picks()->where('pick_number', 2)->first()->team;
        TeamCaptain::create(['team_id' => $secondTeam->id, 'user_id' => $wrongCaptain->id]);

        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);

        try {
            $service->makePick($draft->fresh(), $wrongCaptain, $player);
            $this->fail('A captain from another team must not be able to make the active pick.');
        } catch (ValidationException $exception) {
            $this->assertSame(["It is not your team's turn."], $exception->errors()['captain']);
        }

        $this->assertSame('active', $draft->fresh()->picks()->where('pick_number', 1)->first()->status);
        $this->assertNull($draft->fresh()->picks()->where('pick_number', 1)->first()->tournament_player_id);
    }

    public function test_captain_without_an_active_team_assignment_is_rejected(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $unassignedCaptain = User::factory()->create();
        $unassignedCaptain->assignRole('captain');

        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage("It is not your team's turn.");
        $service->makePick($draft->fresh(), $unassignedCaptain, $player);
    }

    public function test_paused_draft_without_an_active_pick_cannot_resume(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);
        $service->makePick($draft->fresh(), $captain, $player);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('There is no active pick to resume. Start the next pick instead.');
        $service->resumeDraft($draft->fresh(), $admin);
    }

    public function test_next_round_waits_for_admin_and_manual_start_activates_it(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $firstRound = $draft->picks()->where('pick_number', 1)->first()->round;
        $secondRound = DraftRound::create([
            'draft_id' => $draft->id,
            'round_number' => 2,
            'name' => 'Round 2',
            'status' => 'pending',
        ]);
        $draft->picks()->where('pick_number', 2)->update(['draft_round_id' => $secondRound->id]);

        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);
        $service->makePick($draft->fresh(), $captain, $player);

        $this->assertSame('paused', $draft->fresh()->status);
        $this->assertSame('completed', $firstRound->fresh()->status);
        $this->assertSame('pending', $secondRound->fresh()->status);
        $this->assertSame('pending', $draft->fresh()->picks()->where('pick_number', 2)->first()->status);

        $service->startRound($draft->fresh(), $admin);

        $this->assertSame('live', $draft->fresh()->status);
        $this->assertSame('active', $secondRound->fresh()->status);
        $this->assertSame('active', $draft->fresh()->picks()->where('pick_number', 2)->first()->status);
        $this->assertSame(2, $draft->fresh()->current_pick_number);
    }

    public function test_admin_can_manually_select_and_remove_a_player_with_audit_trail(): void
    {
        [$admin, , $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);

        $service->adminSelectPlayer($draft->fresh(), $admin, 1, $player);
        $selected = $draft->fresh()->picks()->where('pick_number', 1)->first();

        $this->assertSame('selected', $selected->status);
        $this->assertSame($admin->id, $selected->selected_by);
        $this->assertSame('paused', $draft->fresh()->status);

        $service->removeSelectedPlayer($draft->fresh(), $admin, 1);
        $removed = $draft->fresh()->picks()->where('pick_number', 1)->first();

        $this->assertSame('pending', $removed->status);
        $this->assertNull($removed->tournament_player_id);
        $this->assertSame('paused', $draft->fresh()->status);
        $this->assertSame(1, $service->state($draft->fresh(), $admin)['remaining_players']->count());
        $this->assertDatabaseHas('audit_logs', ['action' => 'draft.admin_player_selected']);
        $this->assertDatabaseHas('audit_logs', ['action' => 'draft.admin_player_removed']);
    }

    public function test_admin_can_reassign_a_selected_player_to_a_pending_pick(): void
    {
        [$admin, , $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);
        $service->adminSelectPlayer($draft->fresh(), $admin, 1, $player);

        $service->reassignPlayer($draft->fresh(), $admin, 1, 2);
        $source = $draft->fresh()->picks()->where('pick_number', 1)->first();
        $target = $draft->fresh()->picks()->where('pick_number', 2)->first();

        $this->assertSame('pending', $source->status);
        $this->assertNull($source->tournament_player_id);
        $this->assertSame('selected', $target->status);
        $this->assertSame($player->id, $target->tournament_player_id);
        $this->assertSame('paused', $draft->fresh()->status);
        $this->assertDatabaseHas('audit_logs', ['action' => 'draft.admin_player_reassigned']);
    }

    public function test_active_timer_extension_adds_to_current_remaining_time(): void
    {
        [$admin, , $draft] = $this->configuredDraft();
        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);

        $startedAt = now()->subSeconds(45);
        $activePick = $draft->fresh()->picks()->where('pick_number', 1)->first();
        $activePick->update(['started_at' => $startedAt, 'pick_duration' => 60]);
        $draft->fresh()->update(['pick_started_at' => $startedAt, 'pick_duration' => 60]);

        $service->extendTime($draft->fresh(), $admin, 30);
        $extendedPick = $draft->fresh()->picks()->where('pick_number', 1)->first();

        $this->assertSame(90, $extendedPick->pick_duration);
        $this->assertSame($startedAt->timestamp, $extendedPick->started_at->timestamp);
        $this->assertEqualsWithDelta(45, $service->state($draft->fresh())['timer']['remaining_seconds'], 1);
    }

    public function test_expired_pick_is_paused_for_admin_action(): void
    {
        [$admin, $captain, $draft, $player] = $this->configuredDraft();
        $service = app(DraftService::class);
        $service->startDraft($draft, $admin);

        $draft->fresh()->picks()->where('pick_number', 1)->update(['started_at' => now()->subSeconds(61)]);
        $this->expectException(ValidationException::class);

        try {
            $service->makePick($draft->fresh(), $captain, $player);
        } finally {
            $this->assertSame('expired', $draft->fresh()->status);
            $this->assertSame('expired', $draft->fresh()->picks()->where('pick_number', 1)->first()->status);
        }
    }

    private function configuredDraft(): array
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $captain = User::factory()->create();
        $captain->assignRole('captain');

        $playerUser = User::factory()->create();
        $playerUser->assignRole('player');
        $profile = PlayerProfile::create([
            'user_id' => $playerUser->id,
            'full_name' => 'Salman Khan',
            'playing_role' => 'All-rounder',
        ]);

        $tournament = Tournament::create([
            'name' => 'Test Cricket Cup',
            'slug' => 'test-cricket-cup',
            'timezone' => 'Asia/Karachi',
            'status' => 'ready',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        $team = Team::create([
            'tournament_id' => $tournament->id,
            'name' => 'Ali Panthers',
            'short_name' => 'AP',
            'display_order' => 1,
        ]);
        TeamCaptain::create(['team_id' => $team->id, 'user_id' => $captain->id]);

        $secondTeam = Team::create([
            'tournament_id' => $tournament->id,
            'name' => 'Lahore Lions',
            'short_name' => 'LL',
            'display_order' => 2,
        ]);

        $registration = TournamentPlayer::create([
            'tournament_id' => $tournament->id,
            'player_profile_id' => $profile->id,
            'status' => 'approved',
        ]);

        $draft = Draft::create([
            'tournament_id' => $tournament->id,
            'status' => 'setup',
        ]);
        $round = DraftRound::create([
            'draft_id' => $draft->id,
            'round_number' => 1,
            'name' => 'Round 1',
            'status' => 'pending',
        ]);
        DraftPick::create([
            'draft_id' => $draft->id,
            'draft_round_id' => $round->id,
            'team_id' => $team->id,
            'pick_number' => 1,
            'pick_duration' => 60,
            'status' => 'pending',
        ]);
        DraftPick::create([
            'draft_id' => $draft->id,
            'draft_round_id' => $round->id,
            'team_id' => $secondTeam->id,
            'pick_number' => 2,
            'pick_duration' => 60,
            'status' => 'pending',
        ]);

        return [$admin, $captain, $draft, $registration];
    }
}
