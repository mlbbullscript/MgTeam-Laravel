<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel users
 *
 * Menyimpan data semua pengguna sistem: SuperAdmin dan Rekan.
 * Kolom coin_saham menggunakan DECIMAL(5,2) agar mendukung nilai pecahan seperti 33.33.
 * Total coin_saham seluruh user tidak boleh melebihi 100 (enforced di aplikasi).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username', 50)->unique();
            $table->string('password');                         // selalu bcrypt hash
            $table->string('photo_profile')->nullable();        // path ke file foto
            $table->enum('role', ['superadmin', 'rekan'])->default('rekan');
            $table->decimal('coin_saham', 5, 2)->default(0);   // max total = 100.00
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
