@extends('layouts.app')

@section('title', 'Daftar Booking')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Booking</h1>
        <a href="{{ route('bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Booking Baru
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('bookings.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Kode booking atau nama pelanggan...">
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Export Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Export Data</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <a href="{{ route('bookings.export-csv', request()->query()) }}" class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export ke CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bookings List -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="bookingsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Booking</th>
                            <th>Tanggal</th>
                            <th>Lapangan</th>
                            <th>Pelanggan</th>
                            <th>Waktu</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('bookings.show', $booking->id) }}" class="fw-bold">
                                    {{ $booking->kode_booking }}
                                </a>
                            </td>
                            <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                            <td>{{ $booking->lapangan->nama_lapangan ?? '-' }}</td>
                            <td>{{ $booking->pelanggan->nama ?? '-' }}</td>
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
                                @if($booking->pembayaran)
                                    @php
                                        $paymentStatusClass = [
                                            'pending' => 'warning',
                                            'sukses' => 'success',
                                            'gagal' => 'danger',
                                            'refund' => 'info'
                                        ][$booking->pembayaran->status] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $paymentStatusClass }}">{{ ucfirst($booking->pembayaran->status) }}</span>
                                @else
                                    <span class="badge bg-secondary">Belum Bayar</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-info" title="Detail">
                                    <i class="bi bi-eye"></i>
                                </a>

                                @if(in_array($booking->status, ['pending']))
                                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif

                                @if(in_array($booking->status, ['pending', 'dikonfirmasi']))
                                <button type="button" class="btn btn-sm btn-danger" onclick="cancelBooking({{ $booking->id }})" title="Batalkan">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data booking</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if(method_exists($bookings, 'links'))
            <div class="d-flex justify-content-center mt-3">
                {{ $bookings->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Cancel Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Pembatalan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin membatalkan booking ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <form id="cancelForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-danger">Batalkan Booking</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelBooking(id) {
    $('#cancelForm').attr('action', '/bookings/' + id + '/cancel');
    $('#cancelModal').modal('show');
}

$(document).ready(function() {
    $('#bookingsTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/2.2.2/i18n/id.json' // VERSI TERBARU
        }
    });
});
</script>
@endpush
@endsection
