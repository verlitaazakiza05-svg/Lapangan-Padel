<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Booking;
use App\Models\Pembayaran;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Data statistik
        $totalBookingSaya = Booking::where('pelanggan_id', $user->id)->count();
        $bookingAktif = Booking::where('pelanggan_id', $user->id)
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->count();
        $bookingSelesai = Booking::where('pelanggan_id', $user->id)
            ->where('status', 'selesai')
            ->count();
        $totalPengeluaran = Pembayaran::whereHas('booking', function($query) use ($user) {
            $query->where('pelanggan_id', $user->id);
        })->where('status', 'sukses')->sum('jumlah');

        // Booking terbaru
        $recentBookings = Booking::where('pelanggan_id', $user->id)
            ->with('lapangan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // PERBAIKAN: Ambil lapangan dengan kolom yang benar
        // Admin pake 'nama_lapangan', user harus pake yang sama
        $lapanganTersedia = Lapangan::where('status', 'tersedia')
            ->select('id', 'nama_lapangan as nama', 'harga_per_jam', 'tipe_lapangan', 'foto')
            ->limit(6)
            ->get();

        // Promo / Event
        $promos = [
            ['title' => 'Diskon 20%', 'desc' => 'Untuk booking 3 jam', 'image' => '🎯'],
            ['title' => 'Gratis Air Mineral', 'desc' => 'Setiap booking', 'image' => '🥤'],
            ['title' => 'Loyalty Point', 'desc' => 'Dapatkan poin', 'image' => '⭐'],
        ];

        return view('user.dashboard', compact(
            'totalBookingSaya',
            'bookingAktif',
            'bookingSelesai',
            'totalPengeluaran',
            'recentBookings',
            'lapanganTersedia',
            'promos'
        ));
    }

    public function mybookings(Request $request)
{
    $user = Auth::user();

    // Cari pelanggan berdasarkan user_id
    $pelanggan = Pelanggan::where('user_id', $user->id)->first();

    if (!$pelanggan) {
        return view('user.mybookings', ['bookings' => collect([])]);
    }

    $query = Booking::where('pelanggan_id', $pelanggan->id)
        ->with('lapangan')
        ->orderBy('created_at', 'desc');

    // Filter status
    if ($request->has('status') && $request->status != '') {
        $query->where('status', $request->status);
    }

    // Filter tanggal
    if ($request->has('start_date') && $request->start_date != '') {
        $query->whereDate('tanggal_booking', '>=', $request->start_date);
    }
    if ($request->has('end_date') && $request->end_date != '') {
        $query->whereDate('tanggal_booking', '<=', $request->end_date);
    }

    $bookings = $query->paginate(6);

    return view('user.mybookings', compact('bookings'));
}

}
