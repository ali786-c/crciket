<?php

use App\Models\PlayerProfile;
use App\Models\Team;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Backfill teams
        $teams = Team::whereNull('unique_code')->get();
        foreach ($teams as $team) {
            do {
                $code = 'TEAM-' . strtoupper(Str::random(5));
            } while (Team::where('unique_code', $code)->exists());

            $team->update(['unique_code' => $code]);
        }

        // Backfill players
        $players = PlayerProfile::whereNull('unique_code')->get();
        foreach ($players as $player) {
            do {
                $code = 'PLR-' . strtoupper(Str::random(5));
            } while (PlayerProfile::where('unique_code', $code)->exists());

            $player->update(['unique_code' => $code]);
        }
    }

    public function down(): void
    {
        // Codes are nullable, so rolling back just clears them
        Team::query()->update(['unique_code' => null]);
        PlayerProfile::query()->update(['unique_code' => null]);
    }
};
