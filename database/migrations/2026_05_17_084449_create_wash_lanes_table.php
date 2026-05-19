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
        Schema::create('wash_lanes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Jalur A, Jalur B, etc.
            $table->string('type')->default('general'); // general, motor, mobil
            $table->boolean('is_active')->default(true);
            $table->integer('max_queue')->default(10); // Maksimal antrian per jalur
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wash_lanes');
    }
};