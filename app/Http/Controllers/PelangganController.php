<?php
// app/Http/Controllers/PelangganController.php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use Illuminate\Http\Request;

class PelangganController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Pelanggan::query();

        // Search functionality
        if ($request->has('search')) {
            $query->cari($request->search);
        }

        $pelanggans = $query->orderBy('nama')->paginate(10);

        return view('pelanggans.index', compact('pelanggans'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('pelanggans.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pelanggans,email',
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        Pelanggan::create($validated);

        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Pelanggan $pelanggan)
    {
        $pelanggan->load('bookings.lapangan', 'bookings.pembayaran');
        return view('pelanggans.show', compact('pelanggan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Pelanggan $pelanggan)
    {
        return view('pelanggans.edit', compact('pelanggan'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Pelanggan $pelanggan)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|unique:pelanggans,email,' . $pelanggan->id,
            'no_telepon' => 'required|string|max:20',
            'alamat' => 'nullable|string'
        ]);

        $pelanggan->update($validated);

        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Pelanggan $pelanggan)
    {
        // Check if pelanggan has bookings
        if ($pelanggan->bookings()->exists()) {
            return redirect()->route('pelanggans.index')
                ->with('error', 'Pelanggan tidak dapat dihapus karena memiliki riwayat booking.');
        }

        $pelanggan->delete();

        return redirect()->route('pelanggans.index')
            ->with('success', 'Pelanggan berhasil dihapus.');
    }

    /**
     * Cari pelanggan untuk select2
     */
    public function cariForSelect(Request $request)
    {
        $term = $request->get('q');

        $pelanggans = Pelanggan::cari($term)
            ->limit(10)
            ->get(['id', 'nama', 'email', 'no_telepon']);

        return response()->json($pelanggans);
    }
}
