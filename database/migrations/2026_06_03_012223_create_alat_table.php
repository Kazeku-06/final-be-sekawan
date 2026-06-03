<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat', function (Blueprint $table) {
            $table->id('alat_id');
            $table->foreignId('alat_kategori_id')
                  ->constrained('kategori', 'kategori_id')
                  ->cascadeOnDelete();
            $table->string('alat_nama');
            $table->text('alat_deskripsi');
            $table->integer('alat_hargaperhari');
            $table->integer('alat_stok');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
