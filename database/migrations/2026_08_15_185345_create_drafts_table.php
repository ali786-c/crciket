<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('drafts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tournament_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['setup', 'live', 'expired', 'paused', 'completed', 'cancelled'])->default('setup');
            $table->unsignedInteger('current_pick_number')->nullable();
            $table->timestamp('pick_started_at')->nullable();
            $table->unsignedSmallInteger('pick_duration')->nullable();
            $table->unsignedBigInteger('revision')->default(0);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paused_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique('tournament_id');
            $table->index(['status', 'revision']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('drafts');
    }
};
