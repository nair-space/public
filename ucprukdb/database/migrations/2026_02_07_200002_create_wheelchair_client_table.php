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
        Schema::create('wheelchair_client', function (Blueprint $table) {
            $table->string('kursiroda_id', 10)->primary();
            $table->string('jenis_kursiroda');
            $table->string('client_id', 10);
            $table->string('nik', 20);
            $table->string('nama_lengkap');
            $table->timestamps();

            // Foreign key and indexes
            $table->foreign('client_id')->references('client_id')->on('client_bio')->onDelete('cascade');
            $table->index('client_id');
            $table->index('nik');
            $table->index('jenis_kursiroda');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wheelchair_client');
    }
};
