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
            $table->longText('photo_profile')->nullable()->change();
        });

        Schema::table('financial_reports', function (Blueprint $table) {
            $table->longText('screenshot')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('photo_profile', 255)->nullable()->change();
        });

        Schema::table('financial_reports', function (Blueprint $table) {
            $table->string('screenshot', 255)->nullable()->change();
        });
    }
};
