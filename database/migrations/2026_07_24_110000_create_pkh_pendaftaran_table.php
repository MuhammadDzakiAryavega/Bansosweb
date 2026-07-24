<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Pengajuan warga untuk menjadi calon penerima PKH. Ditinjau admin, lalu
        // (bila diverifikasi) warga tersebut ditambahkan sebagai alternatif SAW.
        Schema::create('pkh_pendaftaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama');
            $table->string('nik', 16);
            $table->text('alamat');
            $table->string('no_hp', 20)->nullable();
            // Data kondisi ekonomi (selaras kriteria SAW), diisi mandiri oleh warga.
            $table->string('penghasilan', 60);
            $table->unsignedSmallInteger('jumlah_tanggungan');
            $table->string('kondisi_rumah', 60);
            $table->string('status_pekerjaan', 60);
            $table->string('kepemilikan_aset', 60);
            $table->enum('status', ['Baru', 'Diverifikasi', 'Ditolak'])->default('Baru');
            $table->text('catatan_admin')->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkh_pendaftaran');
    }
};
