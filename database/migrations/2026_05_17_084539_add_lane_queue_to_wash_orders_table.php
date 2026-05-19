<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('wash_orders', function (Blueprint $table) {
            $table->foreignId('wash_lane_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('queue_position')->nullable();
            $table->timestamp('queued_at')->nullable();
            $table->timestamp('lane_started_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wash_orders', function (Blueprint $table) {
            $table->dropForeign(['wash_lane_id']);
            $table->dropColumn(['wash_lane_id', 'queue_position', 'queued_at', 'lane_started_at']);
        });
    }
};