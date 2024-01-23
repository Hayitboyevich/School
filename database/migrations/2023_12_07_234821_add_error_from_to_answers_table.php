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
        Schema::table('answers', function (Blueprint $table) {
            $table->boolean('is_correct')->default(0)->change();
            $table->float('error_from')->nullable();
            $table->float('error_to')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('answers', function (Blueprint $table) {
            $table->boolean('is_correct')->default(null)->change();
            $table->dropColumn('error_from');
            $table->dropColumn('error_to');
        });
    }
};
