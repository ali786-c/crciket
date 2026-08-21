<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('organizations', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('logo_path')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('seasons', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations')->onDelete('cascade');
            $table->string('name');
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('tournaments', function (Blueprint $table): void {
            $table->foreignId('organization_id')->nullable()->after('id')->constrained('organizations')->onDelete('set null');
            $table->foreignId('season_id')->nullable()->after('organization_id')->constrained('seasons')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropForeign(['organization_id']);
            $table->dropForeign(['season_id']);
            $table->dropColumn(['organization_id', 'season_id']);
        });

        Schema::dropIfExists('seasons');
        Schema::dropIfExists('organizations');
    }
};
