<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('draft_picks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_id')->constrained()->cascadeOnDelete();
            $table->foreignId('draft_round_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('pick_number');
            $table->unsignedSmallInteger('pick_duration')->default(60);
            $table->enum('status', ['pending', 'active', 'selected', 'expired', 'skipped'])->default('pending');
            $table->foreignId('tournament_player_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('selected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamp('selected_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->unsignedSmallInteger('extension_count')->default(0);
            $table->unsignedSmallInteger('total_extension_seconds')->default(0);
            $table->timestamps();

            $table->unique(['draft_id', 'pick_number']);
            $table->unique(['draft_id', 'tournament_player_id']);
            $table->index(['draft_id', 'status']);
            $table->index(['draft_round_id', 'pick_number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('draft_picks');
    }
};
