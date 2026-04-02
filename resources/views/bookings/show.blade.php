@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
@php
    // Status booking
    $statusClass = [
        'pending' => 'warning',
        'dikonfirmasi' => 'success',
        'selesai' => 'info',
        'batal' => 'danger'
    ][$booking->status] ?? 'secondary';

    // Status pembayaran (jika ada)
    $paymentStatusClass = 'secondary';
    if ($booking->pembayaran) {
        $paymentStatusClass = [
            'pending' => 'warning',
            'sukses' => 'success',
            'gagal' => 'danger',
            'refund' => 'info'
        ][$booking->pembayaran->status] ?? 'secondary';
    }
@endphp

<div class="container-fluid">
    <!-- Header Sederhana -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="bi bi-calendar-check me-2"></i> Detail Booking
        </h1>
        <div>
            @if(in_array($booking->status, ['pending']))
                <a href="{{ route('bookings.edit', $booking->id) }}" class="btn btn-warning btn-sm me-1">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endif

            @if(in_array($booking->status, ['pending', 'dikonfirmasi']))
                <button type="button" class="btn btn-danger btn-sm me-1" onclick="cancelBooking({{ $booking->id }})">
                    <i class="bi bi-x-circle"></i> Batalkan
                </button>
            @endif

            @if(!$booking->pembayaran && in_array($booking->status, ['pending', 'dikonfirmasi']))
                <a href="{{ route('pembayarans.create', ['booking_id' => $booking->id]) }}" class="btn btn-success btn-sm me-1">
                    <i class="bi bi-cash"></i> Pembayaran
                </a>
            @endif

            <a href="{{ route('bookings.print', $booking->id) }}" class="btn btn-info btn-sm me-1" target="_blank">
                <i class="bi bi-printer"></i> Invoice
            </a>

            <a href="{{ route('bookings.index') }}" class="btn btn-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Status Banner yang Lebih Soft -->
    <div class="alert alert-{{ $statusClass }} alert-light d-flex align-items-center mb-4" role="alert" style="border-left: 4px solid currentColor; background-color: #f8f9fc;">
        <i class="bi bi-info-circle-fill fs-3 me-3 text-{{ $statusClass }}"></i>
        <div>
            <strong>Status Booking:</strong>
            <span class="badge bg-{{ $statusClass }} ms-2">{{ strtoupper($booking->status) }}</span>
            <br>
            <small class="text-muted">Kode Booking: <strong>{{ $booking->kode_booking }}</strong></small>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-xl-4 col-md-6 mb-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-clock-history me-2"></i>Informasi Booking
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <th width="100">Kode</th>
                            <td><span class="badge bg-primary">{{ $booking->kode_booking }}</span></td>
                        </tr>
                        <tr>
                            <th>Tanggal</th>
                            <td><i class="bi bi-calendar3 me-2 text-primary"></i>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <th>Waktu</th>
                            <td><i class="bi bi-clock me-2 text-success"></i>{{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}</td>
                        </tr>
                        <tr>
                            <th>Durasi</th>
                            <td><span class="badge bg-info">{{ $booking->total_jam }} jam</span></td>
                        </tr>
                        <tr>
                            <th>Total</th>
                            <td class="fw-bold text-primary">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Dibuat</th>
                            <td><small class="text-muted">{{ $booking->created_at->format('d/m/Y H:i') }}</small></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Payment Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-cash-stack me-2"></i>Status Pembayaran
                    </h6>
                </div>
                <div class="card-body">
                    @if($booking->pembayaran)
                        <div class="text-center mb-3">
                            <span class="badge bg-{{ $paymentStatusClass }} p-2">
                                <i class="bi bi-{{ $booking->pembayaran->status == 'sukses' ? 'check-circle' : 'exclamation-circle' }} me-2"></i>
                                {{ strtoupper($booking->pembayaran->status) }}
                            </span>
                        </div>
                        <table class="table table-sm table-borderless">
                            <tr>
                                <td class="text-muted">Kode:</td>
                                <td class="text-end">{{ $booking->pembayaran->kode_pembayaran }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jumlah:</td>
                                <td class="text-end fw-bold text-primary">Rp {{ number_format($booking->pembayaran->jumlah, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Metode:</td>
                                <td class="text-end">{{ ucfirst(str_replace('_', ' ', $booking->pembayaran->metode)) }}</td>
                            </tr>
                            @if($booking->pembayaran->tanggal_pembayaran)
                            <tr>
                                <td class="text-muted">Tanggal:</td>
                                <td class="text-end"><small>{{ $booking->pembayaran->tanggal_pembayaran->format('d/m/Y H:i') }}</small></td>
                            </tr>
                            @endif
                        </table>
                        <a href="{{ route('pembayarans.show', $booking->pembayaran->id) }}" class="btn btn-outline-primary btn-sm w-100 mt-2">
                            <i class="bi bi-eye"></i> Detail Pembayaran
                        </a>
                    @else
                        <div class="text-center py-3">
                            <i class="bi bi-exclamation-circle fs-1 text-warning"></i>
                            <p class="mt-2 mb-1">Belum Ada Pembayaran</p>
                            @if(in_array($booking->status, ['pending', 'dikonfirmasi']))
                            <a href="{{ route('pembayarans.create', ['booking_id' => $booking->id]) }}" class="btn btn-success btn-sm">
                                <i class="bi bi-cash"></i> Input Pembayaran
                            </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div class="col-xl-8 col-md-6 mb-4">
            <!-- Informasi Pelanggan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-person-circle me-2"></i>Informasi Pelanggan
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="100">Nama</th>
                                    <td>{{ $booking->pelanggan->nama }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $booking->pelanggan->email }}</td>
                                </tr>
                                <tr>
                                    <th>Telepon</th>
                                    <td>{{ $booking->pelanggan->no_telepon }}</td>
                                </tr>
                                <tr>
                                    <th>Alamat</th>
                                    <td>{{ $booking->pelanggan->alamat ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <a href="{{ route('pelanggans.show', $booking->pelanggan->id) }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-lines-fill"></i> Lihat Profil
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Informasi Lapangan -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">
            <i class="bi bi-grid-3x3-gap-fill me-2"></i>Informasi Lapangan
        </h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0" style="border-collapse: separate; border-spacing: 0; border-radius: 8px; overflow: hidden;">
                <thead class="bg-light">
                    <tr>
                        <th colspan="2" class="text-center py-3">
                            <i class="bi bi-building me-2"></i>
                            {{ $booking->lapangan->nama_lapangan ?? 'Detail Lapangan' }}
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="40%" class="bg-light fw-bold ps-4">
                            <i class="bi bi-tag-fill text-primary me-2"></i>Nama Lapangan
                        </td>
                        <td class="ps-4">
                            <span class="fw-semibold">{{ $booking->lapangan->nama_lapangan ?? '-' }}</span>
                            @if(empty($booking->lapangan->nama_lapangan))
                                <small class="text-danger">(tidak tersedia)</small>
                            @endif
                        </td>
                    </tr>

                    <tr>
                        <td width="40%" class="bg-light fw-bold ps-4">
                            <i class="bi bi-house-door-fill text-success me-2"></i>Tipe Lapangan
                        </td>
                        <td class="ps-4">
                            @php
                                $tipe = $booking->lapangan->tipe_lapangan ?? '-';
                                $tipeClass = $tipe == 'indoor' ? 'info' : ($tipe == 'outdoor' ? 'success' : 'secondary');
                            @endphp
                            <span class="badge bg-{{ $tipeClass }} p-2">
                                <i class="bi bi-{{ $tipe == 'indoor' ? 'building' : 'sun' }} me-1"></i>
                                {{ ucfirst($tipe) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <td width="40%" class="bg-light fw-bold ps-4">
                            <i class="bi bi-currency-dollar text-warning me-2"></i>Harga per Jam
                        </td>
                        <td class="ps-4">
                            <span class="fw-bold text-primary fs-5">
                                Rp {{ number_format($booking->lapangan->harga_per_jam ?? 0, 0, ',', '.') }}
                            </span>
                            <small class="text-muted ms-1">/jam</small>
                        </td>
                    </tr>

                    @if($booking->lapangan->deskripsi)
                    <tr>
                        <td width="40%" class="bg-light fw-bold ps-4">
                            <i class="bi bi-card-text text-info me-2"></i>Deskripsi
                        </td>
                        <td class="ps-4">
                            <p class="mb-0 text-muted">{{ $booking->lapangan->deskripsi }}</p>
                        </td>
                    </tr>
                    @endif

                    @if($booking->lapangan->foto)
                    <tr>
                        <td width="40%" class="bg-light fw-bold ps-4">
                            <i class="bi bi-image text-secondary me-2"></i>Foto
                        </td>
                        <td class="ps-4">
                            <a href="{{ asset('storage/' . $booking->lapangan->foto) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Lihat Foto
                            </a>
                        </td>
                    </tr>
                    @endif

                    <tr class="bg-light">
                        <td colspan="2" class="text-center py-2">
                            <small class="text-muted">
                                <i class="bi bi-info-circle me-1"></i>
                                Status Lapangan:
                                <span class="badge bg-{{ $booking->lapangan->status == 'tersedia' ? 'success' : ($booking->lapangan->status == 'perbaikan' ? 'warning' : 'danger') }}">
                                    {{ ucfirst($booking->lapangan->status ?? 'tidak diketahui') }}
                                </span>
                            </small>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

            <!-- Catatan -->
            @if($booking->catatan)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-chat-dots me-2"></i>Catatan
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0"><i class="bi bi-quote me-2 text-muted"></i>{{ $booking->catatan }}</p>
                </div>
            </div>
            @endif

            <!-- Timeline -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="bi bi-clock me-2"></i>Timeline
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-calendar-check text-primary me-2"></i>
                            <strong>Dibuat:</strong> {{ $booking->created_at->format('d/m/Y H:i:s') }}
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-pencil text-warning me-2"></i>
                            <strong>Update:</strong> {{ $booking->updated_at->format('d/m/Y H:i:s') }}
                        </li>
                        @if($booking->pembayaran && $booking->pembayaran->tanggal_pembayaran)
                        <li>
                            <i class="bi bi-cash text-success me-2"></i>
                            <strong>Pembayaran:</strong> {{ $booking->pembayaran->tanggal_pembayaran->format('d/m/Y H:i:s') }}
                        </li>
                        @endif
                    </ul>
                </div>
            </div>
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
                <p>Apakah Anda yakin ingin membatalkan booking dengan kode:</p>
                <p class="text-center fw-bold text-danger">{{ $booking->kode_booking }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-danger">Ya, Batalkan</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelBooking(id) {
    $('#cancelModal').modal('show');
}
</script>
@endpush
@endsection
