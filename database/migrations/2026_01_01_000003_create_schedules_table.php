<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration tabel schedules
 *
 * Menyimpan jadwal kerja yang ditugaskan ke rekan/superadmin.
 * Jadwal yang sudah berlalu (schedule_date <= hari ini) berkontribusi ke poin kerja user.
 * Poin kerja TIDAK disimpan di tabel users — dihitung dinamis dari tabel ini (ADR-002).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('task_name', 100);
            $table->text('description')->nullable();
            $table->decimal('poin_kerja', 8, 2)->default(0);
            $table->foreignId('assigned_to')->constrained('users');  // rekan atau superadmin
            $table->date('schedule_date');                            // tanggal spesifik jadwal
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
