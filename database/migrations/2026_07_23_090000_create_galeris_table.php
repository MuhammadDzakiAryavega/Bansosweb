<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('galeris', function (Blueprint $table) {
            $table->id('id_galeri');
            // Petugas yang mencatat kegiatan ini; dibiarkan kosong bila akunnya dihapus.
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('judul_kegiatan', 150);
            $table->string('slug', 175)->unique(); // judul ter-slug + akhiran anti-bentrok
            $table->date('tgl_pelaksanaan');
            $table->text('deskripsi_singkat');
            $table->timestamps();
        });

        // Satu kegiatan bisa memiliki banyak foto dokumentasi.
        Schema::create('galeri_fotos', function (Blueprint $table) {
            $table->id('id_foto');
            $table->foreignId('galeri_id')->constrained('galeris', 'id_galeri')->cascadeOnDelete();
            $table->string('path', 150);
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->timestamps();

            $table->index(['galeri_id', 'urutan']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('galeri_fotos');
        Schema::dropIfExists('galeris');
    }
};
