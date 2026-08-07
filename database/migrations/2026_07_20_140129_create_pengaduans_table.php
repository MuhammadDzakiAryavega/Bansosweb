<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id('id_pengaduan');
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('nama_pengadu', 100);
            $table->string('email_pengadu', 100);
            $table->string('no_hp_pengadu', 20)->nullable();
            $table->text('alamat_pengadu');
            $table->string('judul_pengaduan', 150);
            $table->text('isi_pengaduan');
            $table->timestamp('tanggal_pengaduan')->useCurrent();
            $table->enum('status_pengaduan', ['Baru', 'Pending', 'Dalam Proses', 'Selesai', 'Decline'])->default('Baru');
            $table->string('url_lampiran', 150)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};