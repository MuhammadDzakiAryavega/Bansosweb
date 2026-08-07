<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beritas', function (Blueprint $table) {
            $table->id('id_berita');
            // Petugas yang mencatat berita ini; dibiarkan kosong bila akunnya dihapus.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul_berita', 150);
            $table->string('slug', 175)->unique(); // judul ter-slug + akhiran anti-bentrok
            $table->enum('kategori', [
                'Pengumuman',
                'Kegiatan',
                'Penyaluran Bantuan',
                'Sosialisasi',
                'Informasi Program',
            ])->default('Pengumuman');
            $table->enum('penulis', ['Admin Utama', 'Editor'])->default('Admin Utama');
            $table->enum('status_berita', ['Draft', 'Published'])->default('Draft');
            $table->string('gambar_cover', 150)->nullable();
            $table->text('isi_artikel');
            $table->timestamp('tanggal_publikasi')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beritas');
    }
};
