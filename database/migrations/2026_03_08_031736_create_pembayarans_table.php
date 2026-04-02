<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->string('kode_pembayaran')->unique();
            $table->integer('jumlah');
            $table->enum('metode', ['tunai', 'transfer_bank', 'kartu_kredit', 'e_wallet'])->default('tunai');
            $table->enum('status', ['pending', 'sukses', 'gagal', 'refund'])->default('pending');
            $table->timestamp('tanggal_pembayaran')->nullable();
            $table->text('bukti_pembayaran')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayarans');
    }
};
