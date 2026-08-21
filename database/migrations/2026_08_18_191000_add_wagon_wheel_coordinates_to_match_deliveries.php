<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_deliveries', function (Blueprint $table): void {
            $table->decimal('wagon_x', 5, 2)->nullable()->after('device_timestamp');
            $table->decimal('wagon_y', 5, 2)->nullable()->after('wagon_x');
        });
    }

    public function down(): void
    {
        Schema::table('match_deliveries', function (Blueprint $table): void {
            $table->dropColumn(['wagon_x', 'wagon_y']);
        });
    }
};
