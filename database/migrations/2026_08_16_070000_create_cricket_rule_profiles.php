<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cricket_rule_profiles', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('format', 30)->default('t20');
            $table->unsignedTinyInteger('innings_per_side')->default(1);
            $table->unsignedSmallInteger('overs_per_innings')->default(20);
            $table->unsignedTinyInteger('playing_xi_size')->default(11);
            $table->unsignedTinyInteger('maximum_wickets')->default(10);
            $table->unsignedTinyInteger('legal_balls_per_over')->default(6);
            $table->unsignedTinyInteger('max_overs_per_bowler')->nullable();
            $table->unsignedTinyInteger('no_ball_runs')->default(1);
            $table->unsignedTinyInteger('wide_runs')->default(1);
            $table->unsignedTinyInteger('win_points')->default(2);
            $table->unsignedTinyInteger('tie_points')->default(1);
            $table->unsignedTinyInteger('no_result_points')->default(1);
            $table->unsignedTinyInteger('loss_points')->default(0);
            $table->string('tie_method', 30)->default('points');
            $table->unsignedSmallInteger('version')->default(1);
            $table->boolean('is_system')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['format', 'is_active']);
        });

        Schema::table('tournaments', function (Blueprint $table): void {
            $table->foreignId('cricket_rule_profile_id')
                ->nullable()
                ->after('default_pick_duration')
                ->constrained('cricket_rule_profiles')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('cricket_rule_profile_id');
        });

        Schema::dropIfExists('cricket_rule_profiles');
    }
};
