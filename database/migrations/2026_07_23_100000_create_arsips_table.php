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
            $table->string('nomor_arsip', 60)->unique();
            $table->date('tgl_dokumen');
            $table->string('judul_arsip');
            // Sengaja string, bukan enum: admin boleh menambah klasifikasi baru dari formulir.
            $table->string('klasifikasi', 60);
            $table->text('deskripsi_tambahan')->nullable();
            // Lampiran disimpan pada disk privat, diunduh lewat rute khusus admin.
            $table->string('lampiran')->nullable();
            $table->string('lampiran_nama')->nullable();
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
