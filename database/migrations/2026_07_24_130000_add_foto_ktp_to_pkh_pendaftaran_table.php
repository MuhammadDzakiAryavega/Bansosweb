<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Foto diri pengaju sambil memegang KTP (verifikasi identitas).
        Schema::table('pkh_pendaftaran', function (Blueprint $table) {
            $table->string('foto_ktp', 150)->nullable()->after('foto_wc');
        });
    }

    public function down(): void
    {
        Schema::table('pkh_pendaftaran', function (Blueprint $table) {
            $table->dropColumn('foto_ktp');
        });
    }
};
