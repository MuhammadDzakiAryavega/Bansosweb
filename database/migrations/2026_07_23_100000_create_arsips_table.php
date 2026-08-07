<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('arsips', function (Blueprint $table) {
            $table->id('id_arsip');
            // Petugas yang mengunggah arsip ini; dibiarkan kosong bila akunnya dihapus.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nomor_arsip', 60)->unique();
            $table->date('tgl_dokumen');
            $table->string('judul_arsip', 150);
            // Sengaja string, bukan enum: admin boleh menambah klasifikasi baru dari formulir.
            $table->string('klasifikasi', 60);
            $table->text('deskripsi_tambahan')->nullable();
            // Lampiran disimpan pada disk privat, diunduh lewat rute khusus admin.
            $table->string('lampiran', 150)->nullable();
            $table->string('lampiran_nama', 150)->nullable(); // nama asli berkas, dipangkas bila lebih panjang
            $table->unsignedBigInteger('lampiran_ukuran')->nullable();
            $table->enum('status_publikasi', ['Draft', 'Published'])->default('Draft');
            $table->timestamp('tanggal_publikasi')->nullable();
            $table->timestamps();

            $table->index('klasifikasi');
            $table->index('tgl_dokumen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('arsips');
    }
};
