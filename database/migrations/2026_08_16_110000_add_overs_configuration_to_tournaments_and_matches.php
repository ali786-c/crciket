<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tournaments', function (Blueprint $table): void {
            $table->unsignedSmallInteger('default_overs_per_innings')->nullable()->after('cricket_rule_profile_id');
        });

        Schema::table('matches', function (Blueprint $table): void {
            $table->unsignedSmallInteger('overs_per_innings')->nullable()->after('rule_profile_version');
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table): void {
            $table->dropColumn('overs_per_innings');
        });

        Schema::table('tournaments', function (Blueprint $table): void {
            $table->dropColumn('default_overs_per_innings');
        });
    }
};
