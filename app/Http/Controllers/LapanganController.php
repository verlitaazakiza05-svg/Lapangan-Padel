<?php
// app/Http/Controllers/LapanganController.php

namespace App\Http\Controllers;

use App\Models\Lapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LapanganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Lapangan::query();

        // Search functionality
        if ($request->has('search')) {
            $query->where('nama_lapangan', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
        }

        // Filter by tipe
        if ($request->has('tipe') && $request->tipe != '') {
            $query->where('tipe_lapangan', $request->tipe);
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $lapangans = $query->orderBy('nama_lapangan')->paginate(10);

        return view('lapangans.index', compact('lapangans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('lapangans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_lapangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_lapangan' => 'required|in:indoor,outdoor',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,perbaikan,tidak_tersedia',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            $fotoPath = $request->file('foto')->store('lapangans', 'public');
            $validated['foto'] = $fotoPath;
        }

        Lapangan::create($validated);

        return redirect()->route('lapangans.index')
            ->with('success', 'Lapangan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Lapangan $lapangan)
    {
        $lapangan->load('bookings.pelanggan');
        return view('lapangans.show', compact('lapangan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lapangan $lapangan)
    {
        return view('lapangans.edit', compact('lapangan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Lapangan $lapangan)
    {
        $validated = $request->validate([
            'nama_lapangan' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'tipe_lapangan' => 'required|in:indoor,outdoor',
            'harga_per_jam' => 'required|numeric|min:0',
            'status' => 'required|in:tersedia,perbaikan,tidak_tersedia',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('foto')) {
            // Delete old foto
            if ($lapangan->foto) {
                Storage::disk('public')->delete($lapangan->foto);
            }

            $fotoPath = $request->file('foto')->store('lapangans', 'public');
            $validated['foto'] = $fotoPath;
        }

        $lapangan->update($validated);

        return redirect()->route('lapangans.index')
            ->with('success', 'Lapangan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lapangan $lapangan)
    {
        // Check if lapangan has bookings
        if ($lapangan->bookings()->whereNotIn('status', ['selesai', 'batal'])->exists()) {
            return redirect()->route('lapangans.index')
                ->with('error', 'Lapangan tidak dapat dihapus karena masih memiliki booking aktif.');
        }

        // Delete foto
        if ($lapangan->foto) {
            Storage::disk('public')->delete($lapangan->foto);
        }

        $lapangan->delete();

        return redirect()->route('lapangans.index')
            ->with('success', 'Lapangan berhasil dihapus.');
    }

    /**
     * Update status lapangan
     */
    public function updateStatus(Request $request, Lapangan $lapangan)
    {
        $validated = $request->validate([
            'status' => 'required|in:tersedia,perbaikan,tidak_tersedia'
        ]);

        $lapangan->update(['status' => $validated['status']]);

        return response()->json([
            'success' => true,
            'message' => 'Status lapangan berhasil diperbarui.'
        ]);
    }

    /**
     * Cek ketersediaan lapangan
     */
    public function cekKetersediaan(Request $request)
    {
        $validated = $request->validate([
            'lapangan_id' => 'required|exists:lapangans,id',
            'tanggal' => 'required|date',
            'waktu_mulai' => 'required|date_format:H:i',
            'waktu_selesai' => 'required|date_format:H:i|after:waktu_mulai'
        ]);

        $lapangan = Lapangan::find($validated['lapangan_id']);

        $tersedia = $lapangan->isTersedia(
            $validated['tanggal'],
            $validated['waktu_mulai'],
            $validated['waktu_selesai']
        );

        return response()->json([
            'tersedia' => $tersedia,
            'message' => $tersedia ? 'Lapangan tersedia' : 'Lapangan tidak tersedia pada waktu tersebut'
        ]);
    }
}
