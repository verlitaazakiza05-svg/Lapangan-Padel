@extends('layouts.app')

@section('title', 'Detail Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pelanggan</h1>
        <div>
            <a href="{{ route('pelanggans.edit', $pelanggan->id) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('pelanggans.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Pelanggan</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                            <i class="bi bi-person-circle text-white fs-1"></i>
                        </div>
                        <h4 class="mt-2">{{ $pelanggan->nama }}</h4>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th width="120">Email</th>
                            <td>{{ $pelanggan->email }}</td>
                        </tr>
                        <tr>
                            <th>No. Telepon</th>
                            <td>{{ $pelanggan->no_telepon }}</td>
                        </tr>
                        <tr>
                            <th>Alamat</th>
                            <td>{{ $pelanggan->alamat ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bergabung</th>
                            <td>{{ $pelanggan->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Total Booking</th>
                            <td><span class="badge bg-info">{{ $pelanggan->bookings->count() }}</span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Statistik Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Statistik</h6>
                </div>
                <div class="card-body">
                    @php
                        $totalSpent = $pelanggan->bookings->where('status', 'selesai')->sum('total_harga');
                        $totalBookings = $pelanggan->bookings->count();
                        $completedBookings = $pelanggan->bookings->where('status', 'selesai')->count();
                        $cancelledBookings = $pelanggan->bookings->where('status', 'batal')->count();
                    @endphp

                    <div class="mb-3">
                        <label>Total Pengeluaran</label>
                        <h4 class="text-primary">Rp {{ number_format($totalSpent, 0, ',', '.') }}</h4>
                    </div>

                    <div class="mb-3">
                        <label>Rata-rata per Booking</label>
                        <h5>Rp {{ number_format($totalBookings > 0 ? $totalSpent / $totalBookings : 0, 0, ',', '.') }}</h5>
                    </div>

                    <div class="row text-center">
                        <div class="col-4">
                            <div class="h5 mb-0 fw-bold text-info">{{ $totalBookings }}</div>
                            <small>Total</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0 fw-bold text-success">{{ $completedBookings }}</div>
                            <small>Selesai</small>
                        </div>
                        <div class="col-4">
                            <div class="h5 mb-0 fw-bold text-danger">{{ $cancelledBookings }}</div>
                            <small>Batal</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-md-6 mb-4">
            <!-- Riwayat Booking -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Booking</h6>
                    <a href="{{ route('bookings.create', ['pelanggan_id' => $pelanggan->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-circle"></i> Booking Baru
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="bookingTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode Booking</th>
                                    <th>Tanggal</th>
                                    <th>Lapangan</th>
                                    <th>Waktu</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggan->bookings->sortByDesc('tanggal_booking') as $booking)
                                <tr>
                                    <td>{{ $booking->kode_booking }}</td>
                                    <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                                    <td>{{ $booking->lapangan->nama_lapangan }}</td>
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

            <!-- Riwayat Pembayaran -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembayaran</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="paymentTable" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>Kode Pembayaran</th>
                                    <th>Kode Booking</th>
                                    <th>Tanggal</th>
                                    <th>Jumlah</th>
                                    <th>Metode</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pelanggan->bookings->flatMap->pembayaran->filter() as $pembayaran)
                                <tr>
                                    <td>{{ $pembayaran->kode_pembayaran }}</td>
                                    <td>{{ $pembayaran->booking->kode_booking }}</td>
                                    <td>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y H:i') : '-' }}</td>
                                    <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $pembayaran->metode)) }}</td>
                                    <td>
                                        @php
                                            $paymentStatusClass = [
                                                'pending' => 'warning',
                                                'sukses' => 'success',
                                                'gagal' => 'danger',
                                                'refund' => 'info'
                                            ][$pembayaran->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $paymentStatusClass }}">{{ ucfirst($pembayaran->status) }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('pembayarans.show', $pembayaran->id) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada pembayaran</td>
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
        order: [[1, 'desc']]
    });

    $('#paymentTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        },
        order: [[2, 'desc']]
    });
});
</script>
@endpush
@endsection
