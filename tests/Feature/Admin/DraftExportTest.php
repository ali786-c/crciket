<?php

namespace Tests\Feature\Admin;

use App\Models\Draft;
use App\Models\DraftPick;
use App\Models\DraftRound;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DraftExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_download_draft_pick_history_as_csv(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $tournament = Tournament::create([
            'name' => 'Export Cup',
            'slug' => 'export-cup',
            'status' => 'completed',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);
        $draft = Draft::create(['tournament_id' => $tournament->id, 'status' => 'completed', 'revision' => 2]);
        $round = DraftRound::create(['draft_id' => $draft->id, 'round_number' => 1, 'name' => 'Opening round', 'status' => 'completed']);
        $team = Team::create(['tournament_id' => $tournament->id, 'name' => 'Falcons', 'short_name' => 'FAL', 'display_order' => 1, 'is_active' => true]);
        DraftPick::create([
            'draft_id' => $draft->id,
            'draft_round_id' => $round->id,
            'team_id' => $team->id,
            'pick_number' => 1,
            'pick_duration' => 60,
            'status' => 'skipped',
            'extension_count' => 1,
            'total_extension_seconds' => 30,
        ]);

        $this->travelTo(now()->startOfSecond());
        $expectedFilename = 'export-cup-draft-history-'.now()->format('Ymd-His').'.csv';
        $response = $this->actingAs($admin)->get(route('admin.tournaments.draft.history.export', $tournament));

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $response->assertDownload($expectedFilename);
        $this->assertStringContainsString('pick_number,round,team,status', $response->streamedContent());
        $this->assertStringContainsString('1,1,Falcons,skipped', $response->streamedContent());
    }

    public function test_non_admin_cannot_download_draft_history(): void
    {
        $player = User::factory()->create();
        $player->assignRole('player');
        $tournament = Tournament::create(['name' => 'Private Cup', 'slug' => 'private-cup', 'status' => 'completed', 'squad_size' => 3, 'default_pick_duration' => 60]);

        $this->actingAs($player)
            ->get(route('admin.tournaments.draft.history.export', $tournament))
            ->assertForbidden();
    }
}
