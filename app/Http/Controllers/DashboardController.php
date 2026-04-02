<?php
namespace App\Http\Controllers;

use App\Models\Lapangan;
use App\Models\Pelanggan;
use App\Models\Booking;
use App\Models\Pembayaran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil data dari database
        $totalLapangan = Lapangan::count();
        $totalPelanggan = Pelanggan::count();
        $bookingHariIni = Booking::whereDate('tanggal_booking', Carbon::today())->count();

        $pendapatanBulanIni = Pembayaran::where('status', 'sukses')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('jumlah');

        $totalBooking = Booking::count();
        $dikonfirmasi = Booking::where('status', 'dikonfirmasi')->count();
        $pending = Booking::where('status', 'pending')->count();
        $selesai = Booking::where('status', 'selesai')->count();
        $batal = Booking::where('status', 'batal')->count();

        $recentBookings = Booking::with(['lapangan', 'pelanggan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentPayments = Pembayaran::with(['booking.lapangan', 'booking.pelanggan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Kirim semua data ke view
        return view('dashboard', compact(
            'totalLapangan',
            'totalPelanggan',
            'bookingHariIni',
            'pendapatanBulanIni',
            'totalBooking',
            'dikonfirmasi',
            'pending',
            'selesai',
            'batal',
            'recentBookings',
            'recentPayments'
        ));
    }
}
