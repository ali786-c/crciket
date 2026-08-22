<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->string('unique_code', 15)->nullable()->after('id');
            $table->unique('unique_code');
        });

        Schema::table('player_profiles', function (Blueprint $table) {
            $table->string('unique_code', 15)->nullable()->after('id');
            $table->unique('unique_code');
        });
    }

    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('unique_code');
        });

        Schema::table('player_profiles', function (Blueprint $table) {
            $table->dropColumn('unique_code');
        });
    }
};
