<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fixture_id')->nullable()->index();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('rule_profile_id')->constrained('cricket_rule_profiles')->restrictOnDelete();
            $table->unsignedInteger('rule_profile_version')->default(1);
            $table->enum('status', [
                'scheduled', 'squad_selection', 'lineup_pending', 'toss_pending', 'live',
                'innings_break', 'completed', 'result_pending', 'approved', 'rejected',
                'abandoned', 'cancelled',
            ])->default('scheduled');
            $table->foreignId('toss_winner_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->enum('toss_decision', ['bat', 'field'])->nullable();
            $table->timestamp('toss_recorded_at')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->unsignedBigInteger('current_innings_id')->nullable()->index();
            $table->unsignedInteger('revision')->default(0);
            $table->timestamp('last_event_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tournament_id', 'status']);
        });

        Schema::create('match_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('match_id')->constrained('matches')->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tournament_player_id')->constrained()->restrictOnDelete();
            $table->foreignId('draft_pick_id')->nullable()->constrained()->nullOnDelete();
            $table->string('player_name_snapshot');
            $table->string('player_role_snapshot')->nullable();
            $table->enum('selection_type', ['squad', 'playing_xi', 'substitute', 'reserve'])->default('squad');
            $table->unsignedTinyInteger('batting_order')->nullable();
            $table->boolean('is_captain')->default(false);
            $table->boolean('is_wicketkeeper')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['match_id', 'tournament_player_id']);
            $table->index(['match_id', 'team_id', 'selection_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('match_players');
        Schema::dropIfExists('matches');
    }
};
