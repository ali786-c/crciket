<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_deliveries', function (Blueprint $table): void {
            $table->string('local_uuid', 36)->nullable()->unique()->after('commentary');
            $table->timestamp('device_timestamp')->nullable()->after('local_uuid');
        });
    }

    public function down(): void
    {
        Schema::table('match_deliveries', function (Blueprint $table): void {
            $table->dropColumn(['local_uuid', 'device_timestamp']);
        });
    }
};
