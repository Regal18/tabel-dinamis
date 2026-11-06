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
        Schema::create('td_riwayat_impor', function (Blueprint $table) {
            $table->id();
            $table->string('nama_berkas');
            $table->unsignedInteger('ukuran_kb')->default(0);
            $table->string('diunggah_oleh')->nullable();
            $table->unsignedInteger('total_baris')->default(0);
            $table->string('status', 20); // 'SUKSES' atau 'GAGAL'
            $table->text('pesan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('td_riwayat_impor');
    }
};

