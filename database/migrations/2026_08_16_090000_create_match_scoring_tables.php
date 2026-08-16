<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('match_innings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->unsignedTinyInteger('innings_number');
            $table->foreignId('batting_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('bowling_team_id')->constrained('teams')->restrictOnDelete();
            $table->enum('status', ['pending', 'live', 'break', 'completed', 'declared', 'forfeited'])->default('pending');
            $table->unsignedInteger('target_runs')->nullable();
            $table->unsignedSmallInteger('maximum_overs');
            $table->unsignedInteger('total_runs')->default(0);
            $table->unsignedTinyInteger('wickets')->default(0);
            $table->unsignedInteger('legal_balls')->default(0);
            $table->enum('completed_reason', ['all_out', 'overs_complete', 'target_reached', 'declaration', 'chase_ended', 'admin_end'])->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['match_id', 'innings_number']);
            $table->index(['match_id', 'status']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('current_innings_id')->references('id')->on('match_innings')->nullOnDelete();
        });

        Schema::create('match_deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('innings_id')->constrained('match_innings')->cascadeOnDelete();
            $table->unsignedSmallInteger('over_number');
            $table->unsignedTinyInteger('ball_number');
            $table->unsignedInteger('sequence_number');
            $table->foreignId('striker_id')->constrained('match_players')->restrictOnDelete();
            $table->foreignId('non_striker_id')->constrained('match_players')->restrictOnDelete();
            $table->foreignId('bowler_id')->constrained('match_players')->restrictOnDelete();
            $table->unsignedTinyInteger('runs_off_bat')->default(0);
            $table->unsignedTinyInteger('wides')->default(0);
            $table->unsignedTinyInteger('no_balls')->default(0);
            $table->unsignedTinyInteger('byes')->default(0);
            $table->unsignedTinyInteger('leg_byes')->default(0);
            $table->unsignedTinyInteger('penalty_runs')->default(0);
            $table->unsignedTinyInteger('total_runs')->default(0);
            $table->boolean('is_legal_delivery')->default(true);
            $table->unsignedBigInteger('wicket_id')->nullable()->index();
            $table->text('commentary')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at');
            $table->unsignedInteger('revision')->default(1);
            $table->timestamp('voided_at')->nullable();
            $table->string('void_reason')->nullable();
            $table->timestamps();

            $table->unique(['innings_id', 'sequence_number']);
            $table->index(['match_id', 'innings_id', 'voided_at']);
        });

        Schema::create('match_wickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('delivery_id')->constrained('match_deliveries')->cascadeOnDelete();
            $table->foreignId('dismissed_player_id')->constrained('match_players')->restrictOnDelete();
            $table->string('dismissal_type');
            $table->foreignId('credited_bowler_id')->nullable()->constrained('match_players')->nullOnDelete();
            $table->foreignId('fielder_id')->nullable()->constrained('match_players')->nullOnDelete();
            $table->unsignedTinyInteger('runs_completed')->default(0);
            $table->boolean('is_valid_wicket')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::table('match_deliveries', function (Blueprint $table) {
            $table->foreign('wicket_id')->references('id')->on('match_wickets')->nullOnDelete();
        });

        Schema::create('innings_batting_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('innings_id')->constrained('match_innings')->cascadeOnDelete();
            $table->foreignId('match_player_id')->constrained('match_players')->cascadeOnDelete();
            $table->unsignedTinyInteger('batting_position')->nullable();
            $table->unsignedInteger('runs')->default(0);
            $table->unsignedInteger('balls')->default(0);
            $table->unsignedSmallInteger('fours')->default(0);
            $table->unsignedSmallInteger('sixes')->default(0);
            $table->decimal('strike_rate', 7, 2)->default(0);
            $table->string('dismissal_type')->nullable();
            $table->foreignId('dismissed_by')->nullable()->constrained('match_players')->nullOnDelete();
            $table->foreignId('fielder_id')->nullable()->constrained('match_players')->nullOnDelete();
            $table->string('status')->default('did_not_bat');
            $table->timestamps();
            $table->unique(['innings_id', 'match_player_id']);
        });

        Schema::create('innings_bowling_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('innings_id')->constrained('match_innings')->cascadeOnDelete();
            $table->foreignId('match_player_id')->constrained('match_players')->cascadeOnDelete();
            $table->unsignedSmallInteger('legal_balls')->default(0);
            $table->unsignedSmallInteger('maidens')->default(0);
            $table->unsignedInteger('runs_conceded')->default(0);
            $table->unsignedSmallInteger('wickets')->default(0);
            $table->unsignedSmallInteger('no_balls')->default(0);
            $table->unsignedSmallInteger('wides')->default(0);
            $table->decimal('economy', 7, 2)->default(0);
            $table->timestamps();
            $table->unique(['innings_id', 'match_player_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('innings_bowling_stats');
        Schema::dropIfExists('innings_batting_stats');
        Schema::table('match_deliveries', function (Blueprint $table) {
            $table->dropForeign(['wicket_id']);
        });
        Schema::dropIfExists('match_wickets');
        Schema::dropIfExists('match_deliveries');
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['current_innings_id']);
        });
        Schema::dropIfExists('match_innings');
    }
};
