<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel profit_distributions
 *
 * Menyimpan riwayat distribusi profit yang dilakukan SuperAdmin.
 * Menyimpan snapshot persentase saat distribusi agar riwayat tidak berubah
 * jika SuperAdmin mengubah pct_saham/pct_kerja di kemudian hari.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profit_distributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('distributed_by')->constrained('users');
            $table->decimal('laba_bersih', 15, 2);
            $table->decimal('pct_saham_used', 5, 2);           // snapshot persentase saat distribusi
            $table->decimal('pct_kerja_used', 5, 2);           // snapshot persentase saat distribusi
            $table->decimal('total_pool_saham', 15, 2);
            $table->decimal('total_pool_kerja', 15, 2);
            $table->text('notes')->nullable();
            $table->timestamp('distributed_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profit_distributions');
    }
};
