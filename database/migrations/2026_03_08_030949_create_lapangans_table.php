<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('lapangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lapangan');
            $table->text('deskripsi')->nullable();
            $table->string('tipe_lapangan'); // indoor/outdoor
            $table->integer('harga_per_jam');
            $table->enum('status', ['tersedia', 'perbaikan', 'tidak_tersedia'])->default('tersedia');
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('lapangans');
    }
};
