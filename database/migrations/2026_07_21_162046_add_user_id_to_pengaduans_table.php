<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    // Kolom user_id kini sudah dibuat langsung pada migrasi pembuatan tabel,
    // jadi migrasi ini hanya berlaku untuk basis data lama yang belum memilikinya.
    if (Schema::hasColumn('pengaduans', 'user_id')) {
        return;
    }

    Schema::table('pengaduans', function (Blueprint $table) {
        $table->foreignId('user_id')->nullable()->after('id_pengaduan')->constrained()->nullOnDelete();
    });
}

public function down(): void
{
    if (! Schema::hasColumn('pengaduans', 'user_id')) {
        return;
    }

    Schema::table('pengaduans', function (Blueprint $table) {
        $table->dropConstrainedForeignId('user_id');
    });
}
};
