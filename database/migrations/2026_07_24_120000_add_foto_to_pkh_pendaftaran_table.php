<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bukti kondisi rumah: 4 foto (depan, belakang, ruang tamu, WC).
        // Disimpan pada disk privat; hanya dapat dilihat lewat rute admin.
        Schema::table('pkh_pendaftaran', function (Blueprint $table) {
            $table->string('foto_depan', 150)->nullable()->after('kepemilikan_aset');
            $table->string('foto_belakang', 150)->nullable()->after('foto_depan');
            $table->string('foto_ruang_tamu', 150)->nullable()->after('foto_belakang');
            $table->string('foto_wc', 150)->nullable()->after('foto_ruang_tamu');
        });
    }

    public function down(): void
    {
        Schema::table('pkh_pendaftaran', function (Blueprint $table) {
            $table->dropColumn(['foto_depan', 'foto_belakang', 'foto_ruang_tamu', 'foto_wc']);
        });
    }
};
