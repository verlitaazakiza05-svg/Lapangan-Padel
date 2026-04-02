<?php
// app/Models/Pelanggan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggans';
    protected $primaryKey = 'id';

   protected $fillable = [
    'user_id',
    'nama',
    'email',
    'no_telepon',
    'alamat'
];

// Relasi ke User
public function user()
{
    return $this->belongsTo(User::class);
}

    // Relasi dengan Booking
    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    // Scope untuk mencari pelanggan
    public function scopeCari($query, $keyword)
    {
        return $query->where('nama', 'like', "%{$keyword}%")
                     ->orWhere('email', 'like', "%{$keyword}%")
                     ->orWhere('no_telepon', 'like', "%{$keyword}%");
    }
}
