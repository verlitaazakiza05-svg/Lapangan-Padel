<?php
// app/Models/Lapangan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lapangan extends Model
{
    use HasFactory;

    protected $table = 'lapangans';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nama_lapangan',
        'deskripsi',
        'tipe_lapangan',
        'harga_per_jam',
        'status',
        'foto'
    ];

    protected $casts = [
        'harga_per_jam' => 'integer'
    ];

    // Relasi dengan Booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Cek ketersediaan lapangan
    public function isTersedia($tanggal, $waktu_mulai, $waktu_selesai)
    {
        $existingBooking = $this->bookings()
            ->where('tanggal_booking', $tanggal)
            ->where('status', '!=', 'batal')
            ->where(function($query) use ($waktu_mulai, $waktu_selesai) {
                $query->whereBetween('waktu_mulai', [$waktu_mulai, $waktu_selesai])
                      ->orWhereBetween('waktu_selesai', [$waktu_mulai, $waktu_selesai])
                      ->orWhere(function($q) use ($waktu_mulai, $waktu_selesai) {
                          $q->where('waktu_mulai', '<=', $waktu_mulai)
                            ->where('waktu_selesai', '>=', $waktu_selesai);
                      });
            })
            ->exists();

        return !$existingBooking && $this->status === 'tersedia';
    }
}
