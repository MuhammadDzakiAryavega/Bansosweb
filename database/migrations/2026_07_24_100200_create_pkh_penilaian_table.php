<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Penilaian: sub-kriteria yang dipilih untuk satu alternatif pada satu kriteria.
        Schema::create('pkh_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alternatif_id')->constrained('pkh_alternatif')->cascadeOnDelete();
            $table->string('kriteria', 40);
            $table->foreignId('sub_kriteria_id')->constrained('pkh_sub_kriteria')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['alternatif_id', 'kriteria']); // satu nilai per kriteria per alternatif
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkh_penilaian');
    }
};
