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
        Schema::create('wash_order_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wash_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained()->onDelete('cascade');
            $table->decimal('commission_percentage', 5, 2)->default(0); // Individual commission percentage for this order
            $table->decimal('commission_amount', 10, 2)->default(0); // Calculated commission amount
            $table->timestamps();

            $table->unique(['wash_order_id', 'staff_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wash_order_staff');
    }
};