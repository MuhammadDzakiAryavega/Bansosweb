<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Himpunan/sub-kriteria tiap kriteria SAW: label kategori + nilai crisp-nya.
        // Kriteria induk (C1..C5) beserta bobot & jenisnya disimpan sebagai konstanta
        // pada App\Http\Controllers\KelolaPkhController, jadi cukup dirujuk lewat slug.
        Schema::create('pkh_sub_kriteria', function (Blueprint $table) {
            $table->id();
            $table->string('kriteria', 40);       // slug: penghasilan, jumlah-tanggungan, ...
            $table->string('nama', 120);          // mis. "< Rp1.000.000"
            $table->unsignedTinyInteger('nilai'); // nilai crisp, mis. 1..5
            $table->timestamps();

            $table->index('kriteria');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkh_sub_kriteria');
    }
};
