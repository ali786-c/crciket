<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->string('ball_type')->default('leather')->after('timezone');
            $table->string('data_source')->default('verified')->after('ball_type');
        });
    }

    public function down(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropColumn(['ball_type', 'data_source']);
        });
    }
};
