<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $query = Booking::with(['lapangan', 'pelanggan']);

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('tanggal') && $request->tanggal != '') {
            $query->whereDate('tanggal_booking', $request->tanggal);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                    ->orWhereHas('pelanggan', function ($query) use ($search) {
                        $query->where('nama', 'like', "%{$search}%");
                    });
            });
        }

        $bookings = $query->orderBy('tanggal_booking', 'desc')
            ->orderBy('waktu_mulai')
            ->paginate(10);

        return view('bookings.index', compact('bookings'));
    }

    public function create()
    {
        $lapangans = Lapangan::where('status', 'tersedia')->get();
        $pelanggans = Pelanggan::all();
        return view('bookings.create', compact('lapangans', 'pelanggans'));
    }

    public function store(Request $request)
    {
        try {
            // Validasi
            $validated = $request->validate([
                'lapangan_id' => 'required|exists:lapangans,id',
                'tanggal_booking' => 'required|date',
                'waktu_mulai' => 'required',
                'waktu_selesai' => 'required',
                'total_jam' => 'required|integer|min:1'
            ]);

            $lapangan = Lapangan::find($validated['lapangan_id']);

            // Cari pelanggan berdasarkan user login
            $pelanggan = Pelanggan::where('user_id', Auth::id())->first();
            if (!$pelanggan) {
                $pelanggan = Pelanggan::create([
                    'user_id' => Auth::id(),
                    'nama' => Auth::user()->name,
                    'email' => Auth::user()->email,
                ]);
            }

            // Generate kode booking
            $kodeBooking = 'PDL' . date('Ymd') . rand(100, 999);

            // Simpan booking
            $booking = Booking::create([
                'kode_booking' => $kodeBooking,
                'lapangan_id' => $validated['lapangan_id'],
                'pelanggan_id' => $pelanggan->id,
                'tanggal_booking' => $validated['tanggal_booking'],
                'waktu_mulai' => $validated['waktu_mulai'],
                'waktu_selesai' => $validated['waktu_selesai'],
                'total_jam' => $validated['total_jam'],
                'total_harga' => $lapangan->harga_per_jam * $validated['total_jam'],
                'status' => 'pending'
            ]);

            return redirect()->route('user.dashboard')
                ->with('success', 'Booking berhasil! Kode: ' . $kodeBooking);

        } catch (\Exception $e) {
            return redirect()->route('user.dashboard')
                ->with('error', 'Gagal booking: ' . $e->getMessage());
        }
    }

    public function show(Booking $booking)
    {
        $booking->load(['lapangan', 'pelanggan', 'pembayaran']);
        return view('bookings.show', compact('booking'));
    }

    public function edit(Booking $booking)
    {
        if (!in_array($booking->status, ['pending'])) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking tidak dapat diedit karena sudah ' . $booking->status);
        }

        $lapangans = Lapangan::where('status', 'tersedia')->get();
        $pelanggans = Pelanggan::all();
        return view('bookings.edit', compact('booking', 'lapangans', 'pelanggans'));
    }

    public function update(Request $request, Booking $booking)
    {
        if (!in_array($booking->status, ['pending'])) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking tidak dapat diubah karena sudah ' . $booking->status);
        }

        $validated = $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'pelanggan_id' => 'required|exists:pelanggans,id',
            'tanggal_booking' => 'required|date|after_or_equal:today',
            'waktu_mulai' => 'required',
            'waktu_selesai' => 'required|after:waktu_mulai',
            'catatan' => 'nullable|string'
        ]);

        $lapangan = Lapangan::find($validated['lapangan_id']);

        $start = \Carbon\Carbon::parse($validated['waktu_mulai']);
        $end = \Carbon\Carbon::parse($validated['waktu_selesai']);
        $totalJam = $end->diffInHours($start);

        $booking->update([
            'lapangan_id' => $validated['lapangan_id'],
            'pelanggan_id' => $validated['pelanggan_id'],
            'tanggal_booking' => $validated['tanggal_booking'],
            'waktu_mulai' => $validated['waktu_mulai'],
            'waktu_selesai' => $validated['waktu_selesai'],
            'total_jam' => $totalJam,
            'total_harga' => $totalJam * $lapangan->harga_per_jam,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking berhasil diperbarui.');
    }

    public function destroy(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'batal'])) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking tidak dapat dihapus karena sudah ' . $booking->status);
        }

        $booking->delete();
        return redirect()->route('bookings.index')
            ->with('success', 'Booking berhasil dihapus.');
    }

    public function cancel(Booking $booking)
    {
        if (!in_array($booking->status, ['pending', 'dikonfirmasi'])) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Booking tidak dapat dibatalkan.');
        }

        $booking->update(['status' => 'batal']);
        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking berhasil dibatalkan.');
    }

    public function printInvoice(Booking $booking)
    {
        $booking->load(['lapangan', 'pelanggan', 'pembayaran']);
        return view('bookings.invoice', compact('booking'));
    }
}
