<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('raks', function (Blueprint $table) {
            $table->id();
            $table->string('kode_rak', 10)->unique();
            $table->string('nama_rak', 100);
            $table->string('lokasi', 100);
            $table->integer('kapasitas')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('raks');
    }
};