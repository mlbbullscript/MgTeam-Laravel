<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel profit_distribution_details
 *
 * Menyimpan detail per-user dari setiap distribusi profit.
 * Menyimpan snapshot coin_saham dan poin_kerja saat distribusi dilakukan
 * agar perubahan data di masa depan tidak mempengaruhi riwayat distribusi.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_distribution_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distribution_id')->constrained('profit_distributions')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('coin_saham', 5, 2);               // snapshot saham saat distribusi
            $table->decimal('poin_kerja', 8, 2);               // snapshot poin kerja saat distribusi
            $table->decimal('bagian_saham', 15, 2);
            $table->decimal('bagian_kerja', 15, 2);
            $table->decimal('total', 15, 2);
            $table->enum('status', ['pending', 'ditransfer'])->default('pending');
            $table->timestamp('transferred_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_distribution_details');
    }
};
