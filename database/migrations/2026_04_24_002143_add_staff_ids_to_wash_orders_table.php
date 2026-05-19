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
            // Add JSON field to store multiple staff IDs
            $table->json('staff_ids')->nullable()->after('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wash_orders', function (Blueprint $table) {
            $table->dropColumn('staff_ids');
        });
    }
};