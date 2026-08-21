<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('draft_picks', function (Blueprint $table): void {
            $table->boolean('is_captain')->default(false)->after('tournament_player_id');
            $table->boolean('is_vice_captain')->default(false)->after('is_captain');
            $table->boolean('is_wicketkeeper')->default(false)->after('is_vice_captain');
        });
    }

    public function down(): void
    {
        Schema::table('draft_picks', function (Blueprint $table): void {
            $table->dropColumn(['is_captain', 'is_vice_captain', 'is_wicketkeeper']);
        });
    }
};
