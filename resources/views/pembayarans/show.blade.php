@extends('layouts.app')

@section('title', 'Detail Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail Pembayaran</h1>
        <div>
            @if(in_array($pembayaran->status, ['pending', 'gagal']))
                <a href="{{ route('pembayarans.edit', $pembayaran->id) }}" class="btn btn-warning">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif

            @if($pembayaran->status == 'pending')
                <button type="button" class="btn btn-success" onclick="verifyPayment({{ $pembayaran->id }})">
                    <i class="bi bi-check-circle"></i> Verifikasi
                </button>
                <button type="button" class="btn btn-danger" onclick="rejectPayment({{ $pembayaran->id }})">
                    <i class="bi bi-x-circle"></i> Tolak
                </button>
            @endif

            <a href="{{ route('pembayarans.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-4 col-md-6 mb-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status Pembayaran</h6>
                </div>
                <div class="card-body">
                    @php
                        $statusClass = [
                            'pending' => 'warning',
                            'sukses' => 'success',
                            'gagal' => 'danger',
                            'refund' => 'info'
                        ][$pembayaran->status] ?? 'secondary';
                    @endphp

                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $statusClass }} p-3 fs-6">{{ strtoupper($pembayaran->status) }}</span>
                    </div>

                    <table class="table table-bordered">
                        <tr>
                            <th width="120">Kode</th>
                            <td class="fw-bold">{{ $pembayaran->kode_pembayaran }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td class="fw-bold text-primary">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Metode</th>
                            <td>{{ ucfirst(str_replace('_', ' ', $pembayaran->metode)) }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal Bayar</th>
                            <td>{{ $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('d/m/Y H:i') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td>{{ $pembayaran->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Bukti Pembayaran -->
            @if($pembayaran->bukti_pembayaran)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Bukti Pembayaran</h6>
                </div>
                <div class="card-body text-center">
                    <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank">
                        <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 300px;">
                    </a>
                </div>
            </div>
            @endif
        </div>

        <div class="col-xl-8 col-md-6 mb-4">
            <!-- Informasi Booking -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
                    <a href="{{ route('bookings.show', $pembayaran->booking->id) }}" class="btn btn-sm btn-info">
                        <i class="bi bi-eye"></i> Detail Booking
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr>
                            <th width="150">Kode Booking</th>
                            <td>{{ $pembayaran->booking->kode_booking }}</td>
                        </tr>
                        <tr>
                            <th>Pelanggan</th>
                            <td>
                                {{ $pembayaran->booking->pelanggan->nama }}<br>
                                <small>{{ $pembayaran->booking->pelanggan->email }} | {{ $pembayaran->booking->pelanggan->no_telepon }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Lapangan</th>
                            <td>{{ $pembayaran->booking->lapangan->nama_lapangan }}</td>
                        </tr>
                        <tr>
                            <th>Tanggal & Waktu</th>
                            <td>
                                {{ $pembayaran->booking->tanggal_booking->format('d/m/Y') }}
                                {{ substr($pembayaran->booking->waktu_mulai, 0, 5) }} - {{ substr($pembayaran->booking->waktu_selesai, 0, 5) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Total Jam</th>
                            <td>{{ $pembayaran->booking->total_jam }} jam</td>
                        </tr>
                        <tr>
                            <th>Total Harga</th>
                            <td class="fw-bold text-primary">Rp {{ number_format($pembayaran->booking->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Status Booking</th>
                            <td>
                                @php
                                    $bookingStatusClass = [
                                        'pending' => 'warning',
                                        'dikonfirmasi' => 'success',
                                        'selesai' => 'info',
                                        'batal' => 'danger'
                                    ][$pembayaran->booking->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $bookingStatusClass }}">{{ ucfirst($pembayaran->booking->status) }}</span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Keterangan -->
            @if($pembayaran->keterangan)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Keterangan</h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $pembayaran->keterangan }}</p>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Timeline</h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-calendar-check text-primary"></i>
                            <strong>Dibuat:</strong> {{ $pembayaran->created_at->format('d/m/Y H:i:s') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-pencil text-warning"></i>
                            <strong>Terakhir Update:</strong> {{ $pembayaran->updated_at->format('d/m/Y H:i:s') }}
                        </li>
                        @if($pembayaran->tanggal_pembayaran)
                        <li class="mb-2">
                            <i class="bi bi-cash text-success"></i>
                            <strong>Dibayar:</strong> {{ $pembayaran->tanggal_pembayaran->format('d/m/Y H:i:s') }}
                        </li>
                        @endif
                    </ul>
                </div>
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
                <p>Apakah Anda yakin ingin memverifikasi pembayaran dengan kode <strong>{{ $pembayaran->kode_pembayaran }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('pembayarans.verify', $pembayaran->id) }}" method="POST">
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
            <form action="{{ route('pembayarans.reject', $pembayaran->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Apakah Anda yakin ingin menolak pembayaran dengan kode <strong>{{ $pembayaran->kode_pembayaran }}</strong>?</p>
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
    $('#verifyModal').modal('show');
}

function rejectPayment(id) {
    $('#rejectModal').modal('show');
}
</script>
@endpush
@endsection
