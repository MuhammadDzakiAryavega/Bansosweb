<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Alternatif = warga terdaftar yang didaftarkan admin sebagai calon penerima PKH.
        Schema::create('pkh_alternatif', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->unique('user_id'); // satu warga hanya sekali menjadi calon
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pkh_alternatif');
    }
};
