<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel td_indikator — daftar indikator dan sub-indikator.
     */
    public function up(): void
    {
        Schema::create('td_indikator', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique()->comment('Kode unik indikator');
            $table->foreignId('opd_id')->constrained('td_opd')->cascadeOnDelete();
            $table->foreignId('subjek_id')->constrained('td_subjek')->cascadeOnDelete();
            $table->string('nama')->comment('Nama indikator');
            $table->string('sub_indikator')->nullable()->comment('Nama sub-indikator (opsional)');
            $table->string('satuan')->nullable()->comment('Satuan ukuran, misal: persen, unit');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->index(['opd_id', 'subjek_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('td_indikator');
    }
};

