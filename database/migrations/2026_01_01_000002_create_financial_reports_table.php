<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel financial_reports
 *
 * Menyimpan semua laporan arus keuangan (pemasukan & pengeluaran).
 * Menggunakan soft delete (deleted_at) untuk audit trail — data tidak dihapus permanen.
 * Kolom amount menggunakan DECIMAL(15,2) untuk mendukung nilai rupiah yang besar.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['pemasukan', 'pengeluaran']);
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);                  // nominal rupiah
            $table->string('screenshot')->nullable();           // path file screenshot
            $table->foreignId('created_by')->constrained('users');
            $table->date('report_date');                        // tanggal laporan (bukan created_at)
            $table->timestamps();
            $table->softDeletes();                              // soft delete untuk audit trail
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
