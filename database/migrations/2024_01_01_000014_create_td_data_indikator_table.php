<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Membuat tabel td_data_indikator — data nilai indikator per wilayah, tahun, dan jenis.
     */
    public function up(): void
    {
        Schema::create('td_data_indikator', function (Blueprint $table) {
            $table->id();
            $table->foreignId('indikator_id')->constrained('td_indikator')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->constrained('td_wilayah')->cascadeOnDelete();
            $table->unsignedSmallInteger('tahun');
            $table->string('jenis')->comment('Realisasi atau Target');
            $table->decimal('nilai', 20, 4)->nullable()->comment('Nilai data, null jika belum ada');
            $table->string('sumber')->nullable()->comment('Sumber data');
            $table->timestamps();

            // Constraint: satu kombinasi indikator + wilayah + tahun + jenis hanya boleh satu baris
            $table->unique(['indikator_id', 'wilayah_id', 'tahun', 'jenis'], 'td_data_indikator_unik');

            $table->index(['indikator_id', 'jenis', 'tahun']);
            $table->index('wilayah_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('td_data_indikator');
    }
};

