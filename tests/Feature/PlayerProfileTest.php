<?php

namespace Tests\Feature;

use App\Models\PlayerProfile;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlayerProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolePermissionSeeder::class);
    }

    public function test_player_profile_form_exposes_playing_role_dropdown(): void
    {
        $player = User::factory()->create();
        $player->assignRole('player');

        $this->actingAs($player)
            ->get(route('player.profile.edit'))
            ->assertOk()
            ->assertSee('name="playing_role"', false)
            ->assertSee('Batter')
            ->assertSee('Bowler')
            ->assertSee('All-rounder')
            ->assertSee('Wicketkeeper');
    }

    public function test_player_can_save_a_supported_playing_role(): void
    {
        $player = User::factory()->create();
        $player->assignRole('player');

        $this->actingAs($player)
            ->put(route('player.profile.update'), [
                'full_name' => 'Test Batter',
                'playing_role' => 'Batter',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('player_profiles', [
            'user_id' => $player->id,
            'full_name' => 'Test Batter',
            'playing_role' => 'Batter',
        ]);
    }

    public function test_player_cannot_save_an_unsupported_playing_role(): void
    {
        $player = User::factory()->create();
        $player->assignRole('player');
        PlayerProfile::create(['user_id' => $player->id, 'full_name' => 'Existing Player', 'playing_role' => 'Bowler']);

        $this->actingAs($player)
            ->from(route('player.profile.edit'))
            ->put(route('player.profile.update'), [
                'full_name' => 'Existing Player',
                'playing_role' => 'Keeper-Batter',
            ])
            ->assertRedirect(route('player.profile.edit'))
            ->assertSessionHasErrors('playing_role');

        $this->assertDatabaseHas('player_profiles', [
            'user_id' => $player->id,
            'playing_role' => 'Bowler',
        ]);
    }
}
