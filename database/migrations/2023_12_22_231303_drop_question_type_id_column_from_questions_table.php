<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropForeign(['question_type_id']);
            $table->dropColumn('question_type_id');
        });

        Schema::table('attempt_questions', function (Blueprint $table) {
            $table->dropForeign(['question_type_id']);
            $table->dropColumn('question_type_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->foreignId('question_type_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
        });

        Schema::table('attempt_questions', function (Blueprint $table) {
            $table->foreignId('question_type_id')->nullable()->constrained()->cascadeOnUpdate()->restrictOnDelete();
        });
    }
};
