<?php
// app/Models/Booking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $table = 'bookings';
    protected $primaryKey = 'id';

    protected $fillable = [
        'kode_booking',
        'lapangan_id',
        'pelanggan_id',
        'tanggal_booking',
        'waktu_mulai',
        'waktu_selesai',
        'total_jam',
        'total_harga',
        'status',
        'catatan'
    ];

    protected $casts = [
        'tanggal_booking' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relasi dengan Lapangan
    public function lapangan()
    {
        return $this->belongsTo(Lapangan::class);
    }

    // Relasi dengan Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class);
    }

    // Relasi dengan Pembayaran
    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    // Generate kode booking otomatis
    public static function generateKodeBooking()
    {
        $date = date('Ymd');
        $lastBooking = self::whereDate('created_at', today())->latest()->first();

        if ($lastBooking && $lastBooking->kode_booking) {
            $lastNumber = (int) substr($lastBooking->kode_booking, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }

        return 'PDL' . $date . $newNumber;
    }

    // Hitung total harga
    public function hitungTotalHarga()
    {
        if ($this->lapangan) {
            $start = Carbon::parse($this->waktu_mulai);
            $end = Carbon::parse($this->waktu_selesai);
            $this->total_jam = ceil($end->diffInHours($start));
            $this->total_harga = $this->total_jam * $this->lapangan->harga_per_jam;
        }
        return $this->total_harga;
    }

    // Accessor untuk format waktu
    public function getWaktuMulaiFormattedAttribute()
    {
        return Carbon::parse($this->waktu_mulai)->format('H:i');
    }

    public function getWaktuSelesaiFormattedAttribute()
    {
        return Carbon::parse($this->waktu_selesai)->format('H:i');
    }

    // Scope untuk booking aktif
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['pending', 'dikonfirmasi']);
    }

    // Scope untuk booking user tertentu
    public function scopeForUser($query, $userId)
    {
        return $query->whereHas('pelanggan', function($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }
}
