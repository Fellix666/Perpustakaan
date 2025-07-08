<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bukus', function (Blueprint $table) {
            $table->id();
            $table->string('kode_buku', 20)->unique();
            $table->string('isbn', 20)->nullable();
            $table->string('judul', 255);
            $table->string('pengarang', 255);
            $table->string('penerbit', 100);
            $table->year('tahun_terbit');
            $table->integer('jumlah_halaman')->nullable();
            $table->text('deskripsi')->nullable();
            $table->integer('stok_total')->default(1);
            $table->integer('stok_tersedia')->default(1);
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->foreignId('rak_id')->constrained('raks')->onDelete('cascade');
            $table->enum('status', ['tersedia', 'tidak-tersedia'])->default('tersedia');
            $table->string('cover')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bukus');
    }
};
