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
        Schema::create('book_chapter_quiz', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_chapter_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('quiz_id')->constrained()->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_chapter_quiz');
    }
};
