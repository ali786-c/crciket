<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tournaments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->string('timezone')->default('UTC');
            $table->date('starts_on')->nullable();
            $table->date('ends_on')->nullable();
            $table->enum('status', ['draft', 'registration', 'ready', 'live', 'completed', 'cancelled'])->default('draft');
            $table->unsignedSmallInteger('squad_size')->default(3);
            $table->unsignedSmallInteger('default_pick_duration')->default(60);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'starts_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tournaments');
    }
};
