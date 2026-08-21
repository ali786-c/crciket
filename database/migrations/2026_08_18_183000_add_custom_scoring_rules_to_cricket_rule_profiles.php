<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cricket_rule_profiles', function (Blueprint $table): void {
            $table->boolean('wide_runs_to_batsman')->default(false)->after('wide_runs');
            $table->boolean('noball_runs_to_batsman')->default(false)->after('no_ball_runs');
            $table->boolean('last_man_standing')->default(false)->after('maximum_wickets');
            $table->unsignedTinyInteger('max_balls_per_over')->nullable()->after('legal_balls_per_over');
            $table->unsignedTinyInteger('max_runs_per_over')->nullable()->after('max_balls_per_over');
        });
    }

    public function down(): void
    {
        Schema::table('cricket_rule_profiles', function (Blueprint $table): void {
            $table->dropColumn([
                'wide_runs_to_batsman',
                'noball_runs_to_batsman',
                'last_man_standing',
                'max_balls_per_over',
                'max_runs_per_over',
            ]);
        });
    }
};
