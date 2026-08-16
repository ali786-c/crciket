<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->string('season_name')->nullable()->after('name');
            $table->string('venue')->nullable()->after('location');
            $table->string('city')->nullable()->after('venue');
            $table->timestamp('registration_opens_at')->nullable()->after('ends_on');
            $table->timestamp('registration_closes_at')->nullable()->after('registration_opens_at');
            $table->boolean('is_public')->default(true)->after('status');
            $table->string('logo_path')->nullable()->after('is_public');
            $table->string('banner_path')->nullable()->after('logo_path');

            $table->index(['is_public', 'status']);
            $table->index(['registration_opens_at', 'registration_closes_at']);
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropIndex(['registration_opens_at', 'registration_closes_at']);
            $table->dropIndex(['is_public', 'status']);
            $table->dropColumn([
                'season_name',
                'venue',
                'city',
                'registration_opens_at',
                'registration_closes_at',
                'is_public',
                'logo_path',
                'banner_path',
            ]);
        });
    }
};
