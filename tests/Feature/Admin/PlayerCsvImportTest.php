<?php

namespace Tests\Feature\Admin;

use App\Models\PlayerProfile;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class PlayerCsvImportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_admin_can_download_the_csv_template(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)
            ->get(route('admin.players.import.template'))
            ->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_admin_can_import_players_as_accounts_profiles_and_approved_registrations(): void
    {
        [$admin, $tournament] = $this->adminAndTournament();
        $csv = $this->csv([[
            'Ahmed Raza', '03001234567', 'Lahore', 'Batter',
        ]]);

        $this->actingAs($admin)->post(route('admin.tournaments.players.import', $tournament), ['players_csv' => $csv])->assertRedirect()->assertSessionHas('status');

        $user = User::query()->whereHas('playerProfile', fn ($query) => $query->where('phone', '03001234567'))->firstOrFail();
        $this->assertTrue($user->hasRole('player'));
        $this->assertSame('Ahmed Raza', $user->playerProfile->full_name);
        $this->assertSame('Batter', $user->playerProfile->playing_role);
        $this->assertSame('Lahore', $user->playerProfile->city);
        $this->assertDatabaseHas('tournament_players', ['tournament_id' => $tournament->id, 'player_profile_id' => $user->playerProfile->id, 'status' => 'approved']);
        $this->assertDatabaseHas('audit_logs', ['tournament_id' => $tournament->id, 'action' => 'tournament.players_csv_imported']);
    }

    public function test_reimporting_same_email_does_not_duplicate_account_or_registration(): void
    {
        [$admin, $tournament] = $this->adminAndTournament();
        $csv = $this->csv([['Bilal Khan', '03007654321', 'Karachi', 'Bowler']]);
        $this->actingAs($admin)->post(route('admin.tournaments.players.import', $tournament), ['players_csv' => $csv]);
        $this->actingAs($admin)->post(route('admin.tournaments.players.import', $tournament), ['players_csv' => $csv])->assertRedirect();

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('player_profiles', 1);
        $this->assertDatabaseCount('tournament_players', 1);
    }

    public function test_invalid_csv_rows_are_rejected_before_any_records_are_created(): void
    {
        [$admin, $tournament] = $this->adminAndTournament();
        $csv = $this->csv([['Bad Role', 'short', '', 'Keeper']]);

        $this->actingAs($admin)->from(route('admin.tournaments.players.index', $tournament))
            ->post(route('admin.tournaments.players.import', $tournament), ['players_csv' => $csv])
            ->assertSessionHasErrors('csv');

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('player_profiles', 0);
        $this->assertDatabaseCount('tournament_players', 0);
    }

    private function adminAndTournament(): array
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $tournament = Tournament::create(['name' => 'Import Cup', 'slug' => 'import-cup-'.uniqid(), 'status' => 'draft', 'timezone' => 'Asia/Karachi', 'squad_size' => 3, 'default_pick_duration' => 60]);
        return [$admin, $tournament];
    }

    private function csv(array $rows): UploadedFile
    {
        $header = 'full_name,phone,location,playing_role';
        $content = $header."\n".implode("\n", array_map(fn ($row) => implode(',', array_map(fn ($value) => '"'.str_replace('"', '""', $value).'"', $row)), $rows))."\n";
        return UploadedFile::fake()->createWithContent('players.csv', $content);
    }
}
