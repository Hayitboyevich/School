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
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
            $table->string('middle_name', 50)->nullable();
            $table->integer('gender')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('status')->default(1);
            $table->foreignId('city_id')->nullable()->index();
            $table->string('external_id', 50)->nullable();
            $table->string('external_source', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('middle_name');
            $table->dropColumn('gender');
            $table->dropColumn('birth_date');
            $table->dropColumn('status');
            $table->dropColumn('city_id');
            $table->dropColumn('external_id');
            $table->dropColumn('external_source');
        });
    }
};
