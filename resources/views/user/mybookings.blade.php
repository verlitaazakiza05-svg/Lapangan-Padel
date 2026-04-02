@extends('layouts.user')

@section('title', 'My Bookings')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="color: #800020;">
            <i class="bi bi-calendar-check me-2"></i>My Bookings
        </h1>
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.mybookings') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select rounded-3">
                        <option value="">Semua</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="dikonfirmasi" {{ request('status') == 'dikonfirmasi' ? 'selected' : '' }}>Dikonfirmasi</option>
                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="batal" {{ request('status') == 'batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control rounded-3" value="{{ request('start_date') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control rounded-3" value="{{ request('end_date') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary-custom w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bookings List -->
    @if($bookings->count() > 0)
        <div class="row">
            @foreach($bookings as $booking)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 pt-4">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold" style="color: #800020;">{{ $booking->kode_booking }}</span>
                            <span class="badge" style="background:
                                @if($booking->status == 'pending') #ffc107
                                @elseif($booking->status == 'dikonfirmasi') #28a745
                                @elseif($booking->status == 'selesai') #17a2b8
                                @else #dc3545
                                @endif; color: white;">
                                {{ strtoupper($booking->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">{{ $booking->lapangan->nama_lapangan ?? '-' }}</h5>
                        <div class="mb-2">
                            <i class="bi bi-calendar me-2"></i> {{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d F Y') }}
                        </div>
                        <div class="mb-2">
                            <i class="bi bi-clock me-2"></i> {{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}
                        </div>
                        <div class="mb-3">
                            <i class="bi bi-cash-stack me-2"></i> <strong>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0 pb-4">
                        <div class="d-flex gap-2">
                            <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-outline-custom btn-sm flex-grow-1">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                            @if($booking->status == 'pending')
                            <button class="btn btn-danger btn-sm" onclick="cancelBooking({{ $booking->id }})">
                                <i class="bi bi-x-circle"></i> Batal
                            </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $bookings->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-3">Belum ada booking</p>
            <a href="{{ route('user.dashboard') }}" class="btn btn-primary-custom">Booking Sekarang</a>
        </div>
    @endif
</div>

<script>
function cancelBooking(id) {
    if(confirm('Yakin ingin membatalkan booking ini?')) {
        fetch(`/bookings/${id}/cancel`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        }).then(response => response.json()).then(data => {
            if(data.success) {
                alert('Booking berhasil dibatalkan');
                location.reload();
            } else {
                alert('Gagal membatalkan booking');
            }
        });
    }
}
</script>
@endsection
