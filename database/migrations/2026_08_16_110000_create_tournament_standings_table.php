<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournament_standings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('played')->default(0);
            $table->unsignedInteger('wins')->default(0);
            $table->unsignedInteger('losses')->default(0);
            $table->unsignedInteger('ties')->default(0);
            $table->unsignedInteger('no_results')->default(0);
            $table->integer('points')->default(0);
            $table->unsignedInteger('runs_for')->default(0);
            $table->unsignedInteger('balls_faced')->default(0);
            $table->unsignedInteger('runs_against')->default(0);
            $table->unsignedInteger('balls_bowled')->default(0);
            $table->decimal('net_run_rate', 8, 3)->default(0);
            $table->timestamps();
            $table->unique(['tournament_id', 'team_id']);
            $table->index(['tournament_id', 'points', 'net_run_rate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournament_standings');
    }
};
