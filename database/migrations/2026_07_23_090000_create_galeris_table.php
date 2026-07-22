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
            $table->string('judul_kegiatan');
            $table->string('slug')->unique();
            $table->date('tgl_pelaksanaan');
            $table->text('deskripsi_singkat');
            $table->timestamps();
        });

        // Satu kegiatan bisa memiliki banyak foto dokumentasi.
        Schema::create('galeri_fotos', function (Blueprint $table) {
            $table->id('id_foto');
            $table->foreignId('galeri_id')->constrained('galeris', 'id_galeri')->cascadeOnDelete();
            $table->string('path');
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
