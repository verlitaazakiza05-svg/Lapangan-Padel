<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Pelanggan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    // ==================== USER METHODS ====================

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
            $validated = $request->validate([
                'lapangan_id'    => 'required|exists:lapangans,id',
                'tanggal_booking'=> 'required|date',
                'waktu_mulai'    => 'required',
                'waktu_selesai'  => 'required',
                'total_jam'      => 'required|integer|min:1',
            ]);

            $lapangan = Lapangan::find($validated['lapangan_id']);

            $pelanggan = Pelanggan::where('user_id', Auth::id())->first();
            if (!$pelanggan) {
                $pelanggan = Pelanggan::create([
                    'user_id' => Auth::id(),
                    'nama'    => Auth::user()->name,
                    'email'   => Auth::user()->email,
                ]);
            }

            $kodeBooking = 'PDL' . date('Ymd') . rand(100, 999);

            $booking = Booking::create([
                'kode_booking'   => $kodeBooking,
                'lapangan_id'    => $validated['lapangan_id'],
                'pelanggan_id'   => $pelanggan->id,
                'tanggal_booking'=> $validated['tanggal_booking'],
                'waktu_mulai'    => $validated['waktu_mulai'],
                'waktu_selesai'  => $validated['waktu_selesai'],
                'total_jam'      => $validated['total_jam'],
                'total_harga'    => $lapangan->harga_per_jam * $validated['total_jam'],
                'status'         => 'pending',
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

        $lapangans  = Lapangan::where('status', 'tersedia')->get();
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
            'lapangan_id'    => 'required|exists:lapangans,id',
            'pelanggan_id'   => 'required|exists:pelanggans,id',
            'tanggal_booking'=> 'required|date|after_or_equal:today',
            'waktu_mulai'    => 'required',
            'waktu_selesai'  => 'required|after:waktu_mulai',
            'catatan'        => 'nullable|string',
        ]);

        $lapangan = Lapangan::find($validated['lapangan_id']);

        $start    = \Carbon\Carbon::parse($validated['waktu_mulai']);
        $end      = \Carbon\Carbon::parse($validated['waktu_selesai']);
        $totalJam = $end->diffInHours($start);

        $booking->update([
            'lapangan_id'    => $validated['lapangan_id'],
            'pelanggan_id'   => $validated['pelanggan_id'],
            'tanggal_booking'=> $validated['tanggal_booking'],
            'waktu_mulai'    => $validated['waktu_mulai'],
            'waktu_selesai'  => $validated['waktu_selesai'],
            'total_jam'      => $totalJam,
            'total_harga'    => $totalJam * $lapangan->harga_per_jam,
            'catatan'        => $validated['catatan'] ?? null,
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

    /**
     * Mengembalikan slot waktu yang tersedia untuk lapangan & tanggal tertentu.
     */
    public function getAvailableTimes(Request $request)
    {
        $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal'     => 'required|date',
        ]);

        $bookedSlots = Booking::where('lapangan_id', $request->lapangan_id)
            ->whereDate('tanggal_booking', $request->tanggal)
            ->whereNotIn('status', ['batal'])
            ->get(['waktu_mulai', 'waktu_selesai']);

        // Generate slot per jam dari 07:00 – 22:00
        $slots = [];
        for ($hour = 7; $hour < 22; $hour++) {
            $start = sprintf('%02d:00', $hour);
            $end   = sprintf('%02d:00', $hour + 1);

            $tersedia = true;
            foreach ($bookedSlots as $booked) {
                $bookedStart = substr($booked->waktu_mulai, 0, 5);
                $bookedEnd   = substr($booked->waktu_selesai, 0, 5);
                if ($start < $bookedEnd && $end > $bookedStart) {
                    $tersedia = false;
                    break;
                }
            }

            $slots[] = [
                'waktu_mulai'  => $start,
                'waktu_selesai'=> $end,
                'tersedia'     => $tersedia,
            ];
        }

        return response()->json(['success' => true, 'data' => $slots]);
    }

    /**
     * Export data booking ke CSV (tersedia untuk semua user login).
     */
    public function exportCSV(Request $request)
    {
        $query = Booking::with(['lapangan', 'pelanggan']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->tanggal) {
            $query->whereDate('tanggal_booking', $request->tanggal);
        }

        $bookings = $query->orderBy('tanggal_booking', 'desc')->get();

        $filename = 'bookings_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $file = fopen('php://output', 'w');

            // BOM agar Excel bisa baca UTF-8
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'Kode Booking', 'Lapangan', 'Pelanggan',
                'Tanggal', 'Waktu Mulai', 'Waktu Selesai',
                'Total Jam', 'Total Harga', 'Status',
            ]);

            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->kode_booking,
                    $booking->lapangan->nama_lapangan ?? '-',
                    $booking->pelanggan->nama ?? '-',
                    $booking->tanggal_booking->format('d/m/Y'),
                    substr($booking->waktu_mulai, 0, 5),
                    substr($booking->waktu_selesai, 0, 5),
                    $booking->total_jam,
                    $booking->total_harga,
                    $booking->status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ==================== ADMIN METHODS ====================

    public function adminIndex(Request $request)
    {
        $query = Booking::with(['lapangan', 'pelanggan', 'pembayaran']);

        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->tanggal) {
            $query->whereDate('tanggal_booking', $request->tanggal);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_booking', 'like', "%{$search}%")
                  ->orWhereHas('pelanggan', fn($q2) => $q2->where('nama', 'like', "%{$search}%"));
            });
        }

        $bookings = $query->orderBy('tanggal_booking', 'desc')
            ->orderBy('waktu_mulai')
            ->paginate(15);

        return view('admin.bookings.index', compact('bookings'));
    }

    public function adminShow(Booking $booking)
    {
        $booking->load(['lapangan', 'pelanggan', 'pembayaran']);
        return view('admin.bookings.show', compact('booking'));
    }

    public function adminUpdate(Request $request, Booking $booking)
    {
        $validated = $request->validate([
            'status'  => 'required|in:pending,dikonfirmasi,selesai,batal',
            'catatan' => 'nullable|string',
        ]);

        $booking->update($validated);

        return redirect()->route('admin.bookings.show', $booking->id)
            ->with('success', 'Status booking berhasil diperbarui.');
    }
}
