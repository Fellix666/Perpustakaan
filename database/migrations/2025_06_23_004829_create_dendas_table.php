<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('dendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peminjaman_id')->constrained('peminjamans')->onDelete('cascade');
            $table->integer('hari_terlambat');
            $table->integer('denda_per_hari')->default(1000);
            $table->integer('total_denda');
            $table->enum('status_bayar', ['belum-dibayar', 'dibayar'])->default('belum-dibayar');
            $table->date('tanggal_bayar')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('dendas');
    }
};
