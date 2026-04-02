@extends('layouts.app')

@section('title', 'Daftar Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pembayaran</h1>
        <a href="{{ route('pembayarans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Input Pembayaran
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pembayarans.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Kode pembayaran atau kode booking...">
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="sukses" {{ request('status') == 'sukses' ? 'selected' : '' }}>Sukses</option>
                        <option value="gagal" {{ request('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                        <option value="refund" {{ request('status') == 'refund' ? 'selected' : '' }}>Refund</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="metode" class="form-label">Metode</label>
                    <select class="form-select" id="metode" name="metode">
                        <option value="">Semua</option>
                        <option value="tunai" {{ request('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                        <option value="transfer_bank" {{ request('metode') == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="kartu_kredit" {{ request('metode') == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                        <option value="e_wallet" {{ request('metode') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tanggal" class="form-label">Tanggal</label>
                    <input type="date" class="form-control" id="tanggal" name="tanggal" value="{{ request('tanggal') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
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
                    <a href="{{ route('pembayarans.export-csv', request()->query()) }}" class="btn btn-success w-100">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export ke CSV
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Payments List -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="paymentsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Pembayaran</th>
                            <th>Kode Booking</th>
                            <th>Pelanggan</th>
                            <th>Jumlah</th>
                            <th>Metode</th>
                            <th>Status</th>
                            <th>Tanggal Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pembayarans as $index => $pembayaran)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="{{ route('pembayarans.show', $pembayaran->id) }}" class="fw-bold">
                                    {{ $pembayaran->kode_pembayaran }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('bookings.show', $pembayaran->booking->id) }}">
                                    {{ $pembayaran->booking->kode_booking }}
                                </a>
                            </td>
                            <td>{{ $pembayaran->booking->pelanggan->nama }}</td>
                            <td>Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $pembayaran->metode)) }}</td>
                            <td>
                                @php
                                    $statusClass = [
                                        'pending' => 'warning',
                                        'sukses' => 'success',
                                        'gagal' => 'danger',
                                        'refund' => 'info'
                                    ][$pembayaran->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusClass }}">{{ ucfirst($pembayaran->status) }}</span>
                            </td>
                            <td>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y H:i') : '-' }}</td>
                            <td>
                                <a href="{{ route('pembayarans.show', $pembayaran->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array($pembayaran->status, ['pending', 'gagal']))
                                <a href="{{ route('pembayarans.edit', $pembayaran->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                @endif
                                @if($pembayaran->status == 'pending')
                                <button type="button" class="btn btn-sm btn-success" onclick="verifyPayment({{ $pembayaran->id }})">
                                    <i class="bi bi-check-circle"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-danger" onclick="rejectPayment({{ $pembayaran->id }})">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data pembayaran</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $pembayarans->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Verify Modal -->
<div class="modal fade" id="verifyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Verifikasi Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin memverifikasi pembayaran ini?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="verifyForm" method="POST" style="display: inline;">
                    @csrf
                    <button type="submit" class="btn btn-success">Ya, Verifikasi</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tolak Pembayaran</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pembayaran ini?</p>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Alasan Penolakan</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function verifyPayment(id) {
    $('#verifyForm').attr('action', '/pembayarans/' + id + '/verify');
    $('#verifyModal').modal('show');
}

function rejectPayment(id) {
    $('#rejectForm').attr('action', '/pembayarans/' + id + '/reject');
    $('#rejectModal').modal('show');
}

$(document).ready(function() {
    $('#paymentsTable').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });
});
</script>
@endpush
@endsection
