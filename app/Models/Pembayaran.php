<?php
// app/Models/Pembayaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayarans';
    protected $primaryKey = 'id';

    protected $fillable = [
        'booking_id',
        'kode_pembayaran',
        'jumlah',
        'metode',
        'status',
        'tanggal_pembayaran',
        'bukti_pembayaran',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'jumlah' => 'integer'
    ];

    // Relasi dengan Booking
    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    // Generate kode pembayaran otomatis
   public static function generateKodePembayaran()
{
    $prefix = 'PMB';
    $date = date('Ymd');

    // Cari pembayaran terakhir dengan prefix dan tanggal yang sama
    $lastPayment = self::where('kode_pembayaran', 'like', $prefix . $date . '%')
        ->orderBy('kode_pembayaran', 'desc')
        ->first();

    if ($lastPayment) {
        // Ambil 4 digit terakhir dari kode pembayaran terakhir
        $lastNumber = intval(substr($lastPayment->kode_pembayaran, -4));
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    } else {
        $newNumber = '0001';
    }

    // Cek apakah kode baru sudah ada (antisipasi race condition)
    $newKode = $prefix . $date . $newNumber;
    $exists = self::where('kode_pembayaran', $newKode)->exists();

    // Jika sudah ada, increment lagi
    while ($exists) {
        $newNumber = str_pad(intval($newNumber) + 1, 4, '0', STR_PAD_LEFT);
        $newKode = $prefix . $date . $newNumber;
        $exists = self::where('kode_pembayaran', $newKode)->exists();
    }

    return $newKode;
}
}
