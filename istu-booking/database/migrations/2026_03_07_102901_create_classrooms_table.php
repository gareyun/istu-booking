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
        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('classroom_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('room', 50);
            $table->string('description', 255);
            $table->string('equipment', 255);
            $table->integer('capacity');
            $table->string('google_calendar_id', 100);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classrooms');
    }
};
