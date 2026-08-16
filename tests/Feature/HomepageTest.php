<?php

namespace Tests\Feature;

use App\Models\Draft;
use App\Models\Tournament;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_renders_real_live_tournament_state(): void
    {
        $tournament = Tournament::create([
            'name' => 'Database Live Cup',
            'slug' => 'database-live-cup',
            'description' => 'A real live tournament.',
            'location' => 'Lahore',
            'timezone' => 'Asia/Karachi',
            'status' => 'live',
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        Draft::create([
            'tournament_id' => $tournament->id,
            'status' => 'live',
            'current_pick_number' => null,
            'pick_duration' => 60,
            'revision' => 4,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Database Live Cup')
            ->assertSee('Real tournaments on the board.')
            ->assertSee('No placeholder results')
            ->assertDontSee('Lahore Falcons')
            ->assertDontSee('Current pick 07');
    }

    public function test_homepage_renders_real_registration_tournaments(): void
    {
        $tournament = Tournament::create([
            'name' => 'Open Registration Cup',
            'slug' => 'open-registration-cup',
            'status' => 'registration',
            'squad_size' => 5,
            'default_pick_duration' => 90,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Open Registration Cup')
            ->assertSee('Find an open tournament.')
            ->assertSee('Register interest');
    }

    public function test_public_saas_pages_and_tournament_directory_render(): void
    {
        Tournament::create([
            'name' => 'Directory Cup',
            'slug' => 'directory-cup',
            'status' => 'registration',
            'squad_size' => 4,
            'default_pick_duration' => 60,
        ]);

        $this->get(route('public.features'))
            ->assertOk()
            ->assertSee('Everything your tournament needs to run cleanly.');

        $this->get(route('public.how-it-works'))
            ->assertOk()
            ->assertSee('One clean operating flow from registration to result.');

        $this->get(route('public.tournaments.index'))
            ->assertOk()
            ->assertSee('Directory Cup')
            ->assertSee('Open registration');
    }

    public function test_security_headers_are_added_to_web_responses(): void
    {
        $this->get(route('home'))
            ->assertHeader('X-Content-Type-Options', 'nosniff')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin')
            ->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()')
            ->assertHeader('Cross-Origin-Opener-Policy', 'same-origin')
            ->assertHeader('Cross-Origin-Resource-Policy', 'same-origin');
    }

    public function test_system_check_is_restricted_to_authenticated_administrators(): void
    {
        $this->get(route('system.check'))->assertRedirect(route('login'));

        $this->seed(RolePermissionSeeder::class);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin)->get(route('system.check'))->assertOk();
    }

    public function test_private_and_closed_registration_tournaments_are_hidden_publicly(): void
    {
        Tournament::create([
            'name' => 'Private Cup',
            'slug' => 'private-cup',
            'status' => 'registration',
            'is_public' => false,
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);
        Tournament::create([
            'name' => 'Closed Cup',
            'slug' => 'closed-cup',
            'status' => 'registration',
            'is_public' => true,
            'registration_closes_at' => now()->subMinute(),
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);
        Tournament::create([
            'name' => 'Open Cup',
            'slug' => 'open-cup',
            'status' => 'registration',
            'is_public' => true,
            'registration_opens_at' => now()->subMinute(),
            'registration_closes_at' => now()->addMinute(),
            'squad_size' => 3,
            'default_pick_duration' => 60,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Open Cup')
            ->assertDontSee('Private Cup')
            ->assertDontSee('Closed Cup');
    }

    public function test_homepage_has_safe_empty_states_without_tournament_data(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('No tournament is live right now')
            ->assertSee('No registration window is open')
            ->assertSee('Waiting for the first tournament')
            ->assertSee('0 public events');
    }
}
