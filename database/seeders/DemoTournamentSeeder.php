<?php

namespace Database\Seeders;

use App\Models\CricketRuleProfile;
use App\Models\Draft;
use App\Models\DraftPick;
use App\Models\DraftRound;
use App\Models\PlayerProfile;
use App\Models\Team;
use App\Models\TeamCaptain;
use App\Models\Tournament;
use App\Models\TournamentPlayer;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoTournamentSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([RolePermissionSeeder::class, CricketRuleProfileSeeder::class]);
        $t20Rules = CricketRuleProfile::query()->where('slug', 't20-standard')->firstOrFail();

        $admin = User::firstOrCreate(
            ['email' => 'admin@cricketdraft.test'],
            ['name' => 'System Admin', 'password' => Hash::make('password')]
        );
        $admin->assignRole('admin');

        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@cricketdraft.test'],
            ['name' => 'Platform Super Admin', 'password' => Hash::make('password')]
        );
        $superAdmin->assignRole('super_admin');

        $captainOne = User::firstOrCreate(
            ['email' => 'captain@cricketdraft.test'],
            ['name' => 'Ali Captain', 'password' => Hash::make('password')]
        );
        $captainOne->assignRole('captain');

        $captainTwo = User::firstOrCreate(
            ['email' => 'captain2@cricketdraft.test'],
            ['name' => 'Lahore Captain', 'password' => Hash::make('password')]
        );
        $captainTwo->assignRole('captain');

        $tournament = Tournament::updateOrCreate(
            ['slug' => 'preview-cup'],
            [
                'name' => 'Preview Cricket Cup',
                'season_name' => '2026 Preview Season',
                'description' => 'A complete two-team preview tournament for testing the live draft experience.',
                'location' => 'Punjab, Pakistan',
                'venue' => 'Gaddafi Stadium',
                'city' => 'Lahore',
                'timezone' => 'Asia/Karachi',
                'starts_on' => now()->toDateString(),
                'ends_on' => now()->addDays(7)->toDateString(),
                'status' => 'live',
                'is_public' => true,
                'squad_size' => 3,
                'default_pick_duration' => 600,
                'cricket_rule_profile_id' => $t20Rules->id,
                'published_at' => now(),
            ]
        );

        $teamOne = Team::updateOrCreate(
            ['tournament_id' => $tournament->id, 'name' => 'Ali Panthers'],
            ['short_name' => 'AP', 'display_order' => 1, 'is_active' => true]
        );
        $teamTwo = Team::updateOrCreate(
            ['tournament_id' => $tournament->id, 'name' => 'Lahore Lions'],
            ['short_name' => 'LL', 'display_order' => 2, 'is_active' => true]
        );

        TeamCaptain::query()
            ->where('team_id', $teamOne->id)
            ->where('user_id', '!=', $captainOne->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
        TeamCaptain::updateOrCreate(
            ['team_id' => $teamOne->id, 'user_id' => $captainOne->id],
            ['assigned_at' => now(), 'revoked_at' => null]
        );

        TeamCaptain::query()
            ->where('team_id', $teamTwo->id)
            ->where('user_id', '!=', $captainTwo->id)
            ->whereNull('revoked_at')
            ->update(['revoked_at' => now()]);
        TeamCaptain::updateOrCreate(
            ['team_id' => $teamTwo->id, 'user_id' => $captainTwo->id],
            ['assigned_at' => now(), 'revoked_at' => null]
        );

        $players = [
            ['email' => 'salman@cricketdraft.test', 'name' => 'Salman Player', 'full_name' => 'Salman Khan', 'role' => 'All-rounder', 'city' => 'Lahore'],
            ['email' => 'hamza@cricketdraft.test', 'name' => 'Hamza Player', 'full_name' => 'Hamza Ali', 'role' => 'Batter', 'city' => 'Lahore'],
            ['email' => 'usman@cricketdraft.test', 'name' => 'Usman Player', 'full_name' => 'Usman Raza', 'role' => 'Bowler', 'city' => 'Gujranwala'],
            ['email' => 'bilal@cricketdraft.test', 'name' => 'Bilal Player', 'full_name' => 'Bilal Ahmed', 'role' => 'Wicketkeeper', 'city' => 'Islamabad'],
            ['email' => 'ahsan@cricketdraft.test', 'name' => 'Ahsan Player', 'full_name' => 'Ahsan Malik', 'role' => 'Batter', 'city' => 'Karachi'],
            ['email' => 'fahad@cricketdraft.test', 'name' => 'Fahad Player', 'full_name' => 'Fahad Iqbal', 'role' => 'Bowler', 'city' => 'Multan'],
        ];

        $registrations = [];
        foreach ($players as $playerData) {
            $playerUser = User::firstOrCreate(
                ['email' => $playerData['email']],
                ['name' => $playerData['name'], 'password' => Hash::make('password')]
            );
            $playerUser->assignRole('player');

            $profile = PlayerProfile::updateOrCreate(
                ['user_id' => $playerUser->id],
                [
                    'full_name' => $playerData['full_name'],
                    'playing_role' => $playerData['role'],
                    'city' => $playerData['city'],
                ]
            );

            $registrations[] = TournamentPlayer::updateOrCreate(
                ['tournament_id' => $tournament->id, 'player_profile_id' => $profile->id],
                ['status' => 'approved']
            );
        }

        $draft = Draft::updateOrCreate(
            ['tournament_id' => $tournament->id],
            [
                'status' => 'live',
                'current_pick_number' => 1,
                'pick_started_at' => now(),
                'pick_duration' => 600,
                'started_at' => now(),
                'paused_at' => null,
                'completed_at' => null,
                'revision' => 1,
            ]
        );

        DraftPick::query()->where('draft_id', $draft->id)->delete();
        DraftRound::query()->where('draft_id', $draft->id)->delete();

        $roundOne = DraftRound::create([
            'draft_id' => $draft->id,
            'round_number' => 1,
            'name' => 'Opening round',
            'status' => 'active',
            'started_at' => now(),
        ]);
        $roundTwo = DraftRound::create([
            'draft_id' => $draft->id,
            'round_number' => 2,
            'name' => 'Second round',
            'status' => 'pending',
        ]);

        $assignments = [
            [$roundOne, $teamOne, $registrations[0]],
            [$roundOne, $teamTwo, $registrations[1]],
            [$roundOne, $teamOne, $registrations[2]],
            [$roundTwo, $teamTwo, $registrations[3]],
            [$roundTwo, $teamOne, $registrations[4]],
            [$roundTwo, $teamTwo, $registrations[5]],
        ];

        foreach ($assignments as $index => [$round, $team, $registration]) {
            DraftPick::create([
                'draft_id' => $draft->id,
                'draft_round_id' => $round->id,
                'team_id' => $team->id,
                'pick_number' => $index + 1,
                'pick_duration' => 600,
                'status' => $index === 0 ? 'active' : 'pending',
                'started_at' => $index === 0 ? now() : null,
            ]);
        }
    }
}
