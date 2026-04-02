<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Lapangan;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Statistik khusus user
        $totalBookingSaya = Booking::where('pelanggan_id', $userId)->count();
        $bookingAktif = Booking::where('pelanggan_id', $userId)
            ->whereIn('status', ['pending', 'dikonfirmasi'])
            ->count();
        $riwayatBooking = Booking::where('pelanggan_id', $userId)
            ->with('lapangan')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('user.dashboard', compact(
            'totalBookingSaya',
            'bookingAktif',
            'riwayatBooking'
        ));
    }
}
