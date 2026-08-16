<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fixtures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('home_team_id')->constrained('teams')->restrictOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->restrictOnDelete();
            $table->unsignedSmallInteger('round_number')->nullable();
            $table->string('round_name')->nullable();
            $table->unsignedSmallInteger('match_number')->nullable();
            $table->timestamp('scheduled_at');
            $table->string('venue')->nullable();
            $table->string('city')->nullable();
            $table->string('timezone')->default('UTC');
            $table->enum('status', ['scheduled', 'in_progress', 'postponed', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['tournament_id', 'scheduled_at']);
            $table->index(['tournament_id', 'status']);
            $table->unique(['tournament_id', 'match_number']);
        });

        Schema::table('matches', function (Blueprint $table) {
            $table->foreign('fixture_id')->references('id')->on('fixtures')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['fixture_id']);
        });
        Schema::dropIfExists('fixtures');
    }
};
