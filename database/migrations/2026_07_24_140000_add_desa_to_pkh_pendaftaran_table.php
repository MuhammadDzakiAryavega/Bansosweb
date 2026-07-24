<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Desa/kelurahan pengaju di wilayah Kecamatan Teramang Jaya.
        Schema::table('pkh_pendaftaran', function (Blueprint $table) {
            $table->string('desa', 60)->nullable()->after('alamat');
        });
    }

    public function down(): void
    {
        Schema::table('pkh_pendaftaran', function (Blueprint $table) {
            $table->dropColumn('desa');
        });
    }
};
