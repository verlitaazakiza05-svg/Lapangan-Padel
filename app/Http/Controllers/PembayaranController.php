<?php
// app/Http/Controllers/PembayaranController.php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pembayaran::with(['booking.lapangan', 'booking.pelanggan']);

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filter by metode
        if ($request->has('metode') && $request->metode != '') {
            $query->where('metode', $request->metode);
        }

        // Search by kode pembayaran or booking code
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('kode_pembayaran', 'like', "%{$search}%")
                  ->orWhereHas('booking', function($query) use ($search) {
                      $query->where('kode_booking', 'like', "%{$search}%");
                  });
            });
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('pembayarans.index', compact('pembayarans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $bookingId = $request->get('booking_id');
        $booking = null;

        if ($bookingId) {
            $booking = Booking::with(['lapangan', 'pelanggan'])->find($bookingId);

            // Cek apakah sudah ada pembayaran
            if ($booking->pembayaran) {
                return redirect()->route('bookings.show', $bookingId)
                    ->with('error', 'Booking ini sudah memiliki pembayaran.');
            }
        }

        $bookings = Booking::with(['lapangan', 'pelanggan'])
            ->whereDoesntHave('pembayaran')
            ->where('status', 'dikonfirmasi')
            ->orderBy('tanggal_booking', 'desc')
            ->get();

        return view('pembayarans.create', compact('bookings', 'booking'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|in:tunai,transfer_bank,kartu_kredit,e_wallet',
            'status' => 'required|in:pending,sukses,gagal',
            'tanggal_pembayaran' => 'nullable|date',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string'
        ]);

        // Cek apakah booking sudah memiliki pembayaran
        $existingPembayaran = Pembayaran::where('booking_id', $validated['booking_id'])->first();
        if ($existingPembayaran) {
            return back()->withInput()
                ->withErrors(['booking_id' => 'Booking ini sudah memiliki pembayaran.']);
        }

        // Cek jumlah pembayaran
        $booking = Booking::find($validated['booking_id']);
        if ($validated['jumlah'] > $booking->total_harga) {
            return back()->withInput()
                ->withErrors(['jumlah' => 'Jumlah pembayaran melebihi total harga booking.']);
        }

        if ($request->hasFile('bukti_pembayaran')) {
            $buktiPath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
            $validated['bukti_pembayaran'] = $buktiPath;
        }

        // Generate kode pembayaran
        $validated['kode_pembayaran'] = Pembayaran::generateKodePembayaran();

        $pembayaran = Pembayaran::create($validated);

        // Jika status sukses, update status booking
        if ($validated['status'] == 'sukses') {
            $booking->update(['status' => 'dikonfirmasi']);
        }

        return redirect()->route('pembayarans.show', $pembayaran->id)
            ->with('success', 'Pembayaran berhasil ditambahkan. Kode: ' . $pembayaran->kode_pembayaran);
    }

    /**
     * Display the specified resource.
     */
    public function show(Pembayaran $pembayaran)
    {
        $pembayaran->load(['booking.lapangan', 'booking.pelanggan']);
        return view('pembayarans.show', compact('pembayaran'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pembayaran $pembayaran)
    {
        // Cek apakah pembayaran bisa diedit
        if (!in_array($pembayaran->status, ['pending', 'gagal'])) {
            return redirect()->route('pembayarans.index')
                ->with('error', 'Pembayaran tidak dapat diedit karena sudah ' . $pembayaran->status);
        }

        $bookings = Booking::with(['lapangan', 'pelanggan'])->get();
        return view('pembayarans.edit', compact('pembayaran', 'bookings'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pembayaran $pembayaran)
    {
        // Cek apakah pembayaran bisa diupdate
        if (!in_array($pembayaran->status, ['pending', 'gagal'])) {
            return redirect()->route('pembayarans.index')
                ->with('error', 'Pembayaran tidak dapat diubah karena sudah ' . $pembayaran->status);
        }

        $validated = $request->validate([
            'booking_id' => 'required|exists:bookings,id',
            'jumlah' => 'required|numeric|min:0',
            'metode' => 'required|in:tunai,transfer_bank,kartu_kredit,e_wallet',
            'status' => 'required|in:pending,sukses,gagal,refund',
            'tanggal_pembayaran' => 'nullable|date',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'keterangan' => 'nullable|string'
        ]);

        // Cek jumlah pembayaran
        $booking = Booking::find($validated['booking_id']);
        if ($validated['jumlah'] > $booking->total_harga) {
            return back()->withInput()
                ->withErrors(['jumlah' => 'Jumlah pembayaran melebihi total harga booking.']);
        }

        if ($request->hasFile('bukti_pembayaran')) {
            // Delete old bukti
            if ($pembayaran->bukti_pembayaran) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            $buktiPath = $request->file('bukti_pembayaran')->store('bukti-pembayaran', 'public');
            $validated['bukti_pembayaran'] = $buktiPath;
        }

        $oldStatus = $pembayaran->status;
        $pembayaran->update($validated);

        // Update status booking berdasarkan perubahan status pembayaran
        if ($oldStatus != $validated['status']) {
            if ($validated['status'] == 'sukses') {
                $booking->update(['status' => 'dikonfirmasi']);
            } elseif ($validated['status'] == 'gagal' || $validated['status'] == 'refund') {
                $booking->update(['status' => 'pending']);
            }
        }

        return redirect()->route('pembayarans.show', $pembayaran->id)
            ->with('success', 'Pembayaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pembayaran $pembayaran)
    {
        // Cek apakah pembayaran bisa dihapus
        if ($pembayaran->status == 'sukses') {
            return redirect()->route('pembayarans.index')
                ->with('error', 'Pembayaran sukses tidak dapat dihapus.');
        }

        // Delete bukti pembayaran
        if ($pembayaran->bukti_pembayaran) {
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $pembayaran->delete();

        return redirect()->route('pembayarans.index')
            ->with('success', 'Pembayaran berhasil dihapus.');
    }

    /**
     * Update status pembayaran
     */
    public function updateStatus(Request $request, Pembayaran $pembayaran)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,sukses,gagal,refund'
        ]);

        $oldStatus = $pembayaran->status;
        $pembayaran->update(['status' => $validated['status']]);

        // Update status booking
        $booking = $pembayaran->booking;
        if ($validated['status'] == 'sukses' && $oldStatus != 'sukses') {
            $booking->update(['status' => 'dikonfirmasi']);
        } elseif ($validated['status'] == 'gagal' && $oldStatus == 'sukses') {
            $booking->update(['status' => 'pending']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Status pembayaran berhasil diperbarui menjadi ' . $validated['status']
        ]);
    }

    /**
     * Verifikasi pembayaran
     */
    public function verify(Pembayaran $pembayaran)
    {
        if ($pembayaran->status != 'pending') {
            return redirect()->route('pembayarans.show', $pembayaran->id)
                ->with('error', 'Pembayaran sudah diverifikasi.');
        }

        $pembayaran->update([
            'status' => 'sukses',
            'tanggal_pembayaran' => now()
        ]);

        // Update status booking
        $pembayaran->booking->update(['status' => 'dikonfirmasi']);

        return redirect()->route('pembayarans.show', $pembayaran->id)
            ->with('success', 'Pembayaran berhasil diverifikasi.');
    }

    /**
     * Tolak pembayaran
     */
    public function reject(Request $request, Pembayaran $pembayaran)
    {
        if ($pembayaran->status != 'pending') {
            return redirect()->route('pembayarans.show', $pembayaran->id)
                ->with('error', 'Pembayaran sudah diproses.');
        }

        $validated = $request->validate([
            'keterangan' => 'required|string'
        ]);

        $pembayaran->update([
            'status' => 'gagal',
            'keterangan' => $validated['keterangan']
        ]);

        return redirect()->route('pembayarans.show', $pembayaran->id)
            ->with('success', 'Pembayaran ditolak.');
    }

    /**
     * Get payment statistics
     */
    public function getStatistics()
    {
        $totalPayments = Pembayaran::sum('jumlah');
        $successPayments = Pembayaran::where('status', 'sukses')->sum('jumlah');
        $pendingPayments = Pembayaran::where('status', 'pending')->sum('jumlah');
        $failedPayments = Pembayaran::where('status', 'gagal')->sum('jumlah');

        $countSuccess = Pembayaran::where('status', 'sukses')->count();
        $countPending = Pembayaran::where('status', 'pending')->count();
        $countFailed = Pembayaran::where('status', 'gagal')->count();

        $todayPayments = Pembayaran::whereDate('created_at', today())->sum('jumlah');
        $thisMonthPayments = Pembayaran::whereMonth('created_at', now()->month)->sum('jumlah');

        return response()->json([
            'success' => true,
            'data' => [
                'total_payments' => $totalPayments,
                'success_payments' => $successPayments,
                'pending_payments' => $pendingPayments,
                'failed_payments' => $failedPayments,
                'count_success' => $countSuccess,
                'count_pending' => $countPending,
                'count_failed' => $countFailed,
                'today_payments' => $todayPayments,
                'this_month_payments' => $thisMonthPayments
            ]
        ]);
    }

    /**
     * Export payments to CSV
     */
    public function exportCSV(Request $request)
    {
        $query = Pembayaran::with(['booking.lapangan', 'booking.pelanggan']);

        // Apply filters
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->get();

        $filename = 'pembayaran_export_' . date('Ymd_His') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Add CSV headers
        fputcsv($handle, [
            'Kode Pembayaran',
            'Kode Booking',
            'Pelanggan',
            'Lapangan',
            'Jumlah',
            'Metode',
            'Status',
            'Tanggal Pembayaran',
            'Keterangan',
            'Dibuat Pada'
        ]);

        // Add data rows
        foreach ($pembayarans as $pembayaran) {
            fputcsv($handle, [
                $pembayaran->kode_pembayaran,
                $pembayaran->booking->kode_booking,
                $pembayaran->booking->pelanggan->nama,
                $pembayaran->booking->lapangan->nama_lapangan,
                'Rp ' . number_format($pembayaran->jumlah, 0, ',', '.'),
                ucfirst(str_replace('_', ' ', $pembayaran->metode)),
                ucfirst($pembayaran->status),
                $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y H:i') : '-',
                $pembayaran->keterangan ?? '-',
                $pembayaran->created_at->format('d/m/Y H:i')
            ]);
        }

        fclose($handle);
        exit;
    }
}
