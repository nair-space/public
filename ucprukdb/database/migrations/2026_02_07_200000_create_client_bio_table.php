<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('client_bio', function (Blueprint $table) {
            $table->string('client_id', 10)->primary();
            $table->date('tanggal_daftar');
            $table->string('source_id', 10);
            $table->text('nik');
            $table->string('nama_depan');
            $table->string('nama_belakang');
            $table->string('nama_lengkap');
            $table->string('nama_panggilan');
            $table->enum('jenis_kelamin', ['laki-laki', 'perempuan']);
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('dusun');
            $table->string('kecamatan');
            $table->string('kelurahan');
            $table->string('provinsi');
            $table->enum('status_asuransi', ['ya', 'tidak'])->default('tidak');
            $table->string('nama_asuransi')->nullable();
            $table->string('nomor_asuransi', 30)->nullable();
            $table->enum('status_bpjs', ['ya', 'tidak'])->default('tidak');
            $table->enum('status_difabel', ['ya', 'tidak'])->default('tidak');
            $table->string('dari_klinik')->nullable();
            $table->string('jenis_disabilitas');
            $table->string('status_aktivitas');
            $table->string('jenis_sekolah')->nullable();
            $table->enum('ada_foto', ['ya', 'tidak'])->default('tidak');
            $table->enum('salinan_kk', ['ya', 'tidak'])->default('tidak');
            $table->enum('salinan_ktp', ['ya', 'tidak'])->default('tidak');
            $table->enum('salinan_tagihanlistrik', ['ya', 'tidak'])->default('tidak');
            $table->enum('salinan_slipgaji', ['ya', 'tidak'])->default('tidak');
            $table->text('info_tambahan')->nullable();
            $table->timestamps();

            // Indexes for optimization
            $table->index('tanggal_daftar');
            $table->index('provinsi');
            $table->index('kecamatan');
            $table->index('jenis_disabilitas');
            $table->index('status_difabel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_bio');
    }
};
