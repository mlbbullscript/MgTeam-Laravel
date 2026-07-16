<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel overtime_tasks (Tugas Lembur)
 *
 * Menyimpan tugas lembur yang bisa diambil rekan (first come first served).
 * Proteksi race condition dilakukan di aplikasi menggunakan DB::transaction + lockForUpdate().
 * Status: 'tersedia' = belum diambil, 'diambil' = sudah ada yang mengambil.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('overtime_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->decimal('poin_kerja', 8, 2)->default(0);
            $table->enum('status', ['tersedia', 'diambil'])->default('tersedia');
            $table->foreignId('taken_by')->nullable()->constrained('users'); // null jika belum diambil
            $table->timestamp('taken_at')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('overtime_tasks');
    }
};
