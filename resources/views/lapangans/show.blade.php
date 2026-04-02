@extends('layouts.app')

@section('title', 'Detail Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Lapangan</h1>
        <div>
            <a href="{{ route('lapangans.edit', $lapangan->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('lapangans.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Foto Lapangan</h6>
                </div>
                <div class="card-body text-center">
                    @if($lapangan->foto)
                        <img src="{{ asset('storage/' . $lapangan->foto) }}" alt="{{ $lapangan->nama_lapangan }}" class="img-fluid rounded" style="max-height: 300px;">
                    @else
                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                            <i class="bi bi-image text-white fs-1"></i>
                            <p class="text-white mt-2">Tidak ada foto</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Lapangan</h6>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="200">Nama Lapangan</th>
                            <td>{{ $lapangan->nama_lapangan }}</td>
                        </tr>
                        <tr>
                            <th>Tipe Lapangan</th>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($lapangan->tipe_lapangan) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Harga per Jam</th>
                            <td class="fw-bold text-primary">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @php
                                    $statusClass = [
                                        'tersedia' => 'success',
                                        'perbaikan' => 'warning',
                                        'tidak_tersedia' => 'danger'
                                    ][$lapangan->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($lapangan->status) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Deskripsi</th>
                            <td>{{ $lapangan->deskripsi ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat Pada</th>
                            <td>{{ $lapangan->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Terakhir Update</th>
                            <td>{{ $lapangan->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Booking History -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Booking</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="bookingTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Pelanggan</th>
                                    <th>Tanggal</th>
                                    <th>Waktu</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($lapangan->bookings as $booking)
                                <tr>
                                    <td>{{ $booking->kode_booking }}</td>
                                    <td>{{ $booking->pelanggan->nama }}</td>
                                    <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                                    <td>{{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}</td>
                                    <td>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                    <td>
                                        @php
                                            $statusClass = [
                                                'pending' => 'warning',
                                                'dikonfirmasi' => 'success',
                                                'selesai' => 'info',
                                                'batal' => 'danger'
                                            ][$booking->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $statusClass }}">{{ ucfirst($booking->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada booking</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('#bookingTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        order: [[2, 'desc']]
    });
});
</script>
@endpush
@endsection
