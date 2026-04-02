<?php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Pelanggan;
use App\Models\Booking;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Stat Cards
        $totalLapangan = Lapangan::count();
        $totalPelanggan = Pelanggan::count();
        $bookingHariIni = Booking::whereDate('tanggal_booking', now())->count();
        $pendapatanBulanIni = Pembayaran::where('status', 'sukses')
            ->whereMonth('created_at', now()->month)
            ->sum('jumlah');

        // Statistik Booking
        $totalBooking = Booking::count();
        $dikonfirmasi = Booking::where('status', 'dikonfirmasi')->count();
        $pending = Booking::where('status', 'pending')->count();
        $selesai = Booking::where('status', 'selesai')->count();

        // Recent Bookings (5 data terbaru)
        $recentBookings = Booking::with('pelanggan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Recent Payments (5 data terbaru)
        $recentPayments = Pembayaran::with('booking.pelanggan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalLapangan',
            'totalPelanggan',
            'bookingHariIni',
            'pendapatanBulanIni',
            'totalBooking',
            'dikonfirmasi',
            'pending',
            'selesai',
            'recentBookings',
            'recentPayments'
        ));
    }
}
