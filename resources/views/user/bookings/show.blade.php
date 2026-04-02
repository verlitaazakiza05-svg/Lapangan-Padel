@extends('layouts.app')

@section('title', 'Detail Booking')

@section('content')
<div class="container-fluid px-4">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="fw-bold" style="color: var(--primary);">
                    <i class="bi bi-receipt me-2"></i>Detail Booking
                </h4>
                <a href="{{ route('bookings.index') }}" class="btn btn-outline-custom">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="bg-light p-4 rounded-3">
                        <h5 class="fw-bold mb-3">Informasi Booking</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Kode Booking</th>
                                <td><strong class="text-primary">{{ $booking->kode_booking }}</strong></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <span class="badge px-3 py-2"
                                        style="background:
                                        @if($booking->status == 'dikonfirmasi') #28a745
                                        @elseif($booking->status == 'pending') #ffc107
                                        @elseif($booking->status == 'selesai') #17a2b8
                                        @else #dc3545 @endif; color: white;">
                                        {{ strtoupper($booking->status) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Booking</th>
                                <td>{{ Carbon\Carbon::parse($booking->tanggal_booking)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>Waktu</th>
                                <td>{{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}</td>
                            </tr>
                            <tr>
                                <th>Durasi</th>
                                <td>{{ $booking->total_jam }} Jam</td>
                            </tr>
                            <tr>
                                <th>Total Harga</th>
                                <td><strong class="fs-5" style="color: var(--primary);">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</strong></td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="bg-light p-4 rounded-3">
                        <h5 class="fw-bold mb-3">Informasi Lapangan</h5>
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nama Lapangan</th>
                                <td><strong>{{ $booking->lapangan->nama_lapangan }}</strong></td>
                            </tr>
                            <tr>
                                <th>Tipe Lapangan</th>
                                <td>{{ $booking->lapangan->tipe_lapangan ?? 'Indoor' }}</td>
                            </tr>
                            <tr>
                                <th>Harga per Jam</th>
                                <td>Rp {{ number_format($booking->lapangan->harga_per_jam, 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        @if($booking->status == 'pending')
                            <div class="alert alert-warning mt-3">
                                <i class="bi bi-exclamation-triangle"></i>
                                Booking belum dibayar. Segera lakukan pembayaran untuk mengkonfirmasi booking.
                            </div>
                            <button class="btn btn-primary-custom w-100" onclick="bayarSekarang()">
                                <i class="bi bi-credit-card"></i> Bayar Sekarang
                            </button>
                        @endif

                        @if(in_array($booking->status, ['pending', 'dikonfirmasi']))
                            <button class="btn btn-danger w-100 mt-2" onclick="batalkanBooking()">
                                <i class="bi bi-x-circle"></i> Batalkan Booking
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function bayarSekarang() {
    // Redirect ke halaman pembayaran
    alert('Fitur pembayaran akan segera tersedia');
    // window.location.href = "{{ route('payments.create', $booking->id) }}";
}

function batalkanBooking() {
    if (confirm('Apakah Anda yakin ingin membatalkan booking ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = "{{ route('bookings.update', $booking->id) }}";

        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'PUT';
        form.appendChild(method);

        const action = document.createElement('input');
        action.type = 'hidden';
        action.name = 'action';
        action.value = 'cancel';
        form.appendChild(action);

        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection
