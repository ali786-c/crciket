<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->foreignId('winner_team_id')->nullable()->after('toss_decision')->constrained('teams')->nullOnDelete();
            $table->enum('result_type', ['win', 'tie', 'no_result', 'abandoned'])->nullable()->after('winner_team_id');
            $table->string('result_summary')->nullable()->after('result_type');
            $table->timestamp('result_submitted_at')->nullable()->after('completed_at');
            $table->foreignId('result_submitted_by')->nullable()->after('result_submitted_at')->constrained('users')->nullOnDelete();
            $table->foreignId('result_approved_by')->nullable()->after('approved_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('matches', function (Blueprint $table) {
            $table->dropForeign(['winner_team_id']);
            $table->dropForeign(['result_submitted_by']);
            $table->dropForeign(['result_approved_by']);
            $table->dropColumn(['winner_team_id', 'result_type', 'result_summary', 'result_submitted_at', 'result_submitted_by', 'result_approved_by']);
        });
    }
};
