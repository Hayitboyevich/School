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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->date('start');
            $table->date('end');
            $table->date('quarter1_start_date')->nullable();
            $table->date('quarter1_end_date')->nullable();
            $table->date('quarter2_start_date')->nullable();
            $table->date('quarter2_end_date')->nullable();
            $table->date('quarter3_start_date')->nullable();
            $table->date('quarter3_end_date')->nullable();
            $table->date('quarter4_start_date')->nullable();
            $table->date('quarter4_end_date')->nullable();
            $table->unsignedBigInteger('external_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
    }
};
