@extends('layouts.app')

@section('title', 'Riwayat Booking Saya')

@section('content')
<style>
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending {
        background: #ffc107;
        color: #856404;
    }
    .status-dikonfirmasi {
        background: #28a745;
        color: white;
    }
    .status-selesai {
        background: #17a2b8;
        color: white;
    }
    .status-batal {
        background: #dc3545;
        color: white;
    }
    .booking-card {
        transition: all 0.3s ease;
        border-radius: 16px;
        overflow: hidden;
    }
    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(128,0,32,0.1);
    }
</style>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0" style="color: #800020;">
            <i class="bi bi-calendar-check me-2"></i>Riwayat Booking Saya
        </h1>
        <a href="{{ route('user.dashboard') }}" class="btn btn-outline-custom">
            <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
        </a>
    </div>

    <!-- Filter -->
    <div class="card shadow-sm mb-4 border-0 rounded-4">
        <div class="card-body">
            <form method="GET" action="{{ route('user.bookings') }}" class="row g-3">
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

    <!-- Daftar Booking -->
    <div class="row">
        @forelse($bookings as $booking)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card booking-card shadow-sm border-0 h-100 rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="color: #800020;">{{ $booking->kode_booking }}</span>
                        <span class="status-badge status-{{ $booking->status }}">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-grid-3x3-gap-fill me-2" style="color: #800020;"></i>
                            <strong>{{ $booking->lapangan->nama_lapangan ?? '-' }}</strong>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar me-2" style="color: #800020;"></i>
                            {{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d F Y') }}
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-clock me-2" style="color: #800020;"></i>
                            {{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}
                            ({{ $booking->total_jam }} jam)
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash-stack me-2" style="color: #800020;"></i>
                            <strong>Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</strong>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pb-4 pt-0">
                    <div class="d-flex gap-2">
                        <a href="{{ route('bookings.show', $booking->id) }}" class="btn btn-sm btn-outline-custom flex-grow-1">
                            <i class="bi bi-eye"></i> Detail
                        </a>
                        @if($booking->status == 'pending')
                        <button type="button" class="btn btn-sm btn-danger" onclick="cancelBooking({{ $booking->id }})">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        @endif
                        @if($booking->status == 'selesai')
                        <a href="{{ route('bookings.print', $booking->id) }}" class="btn btn-sm btn-info text-white" target="_blank">
                            <i class="bi bi-printer"></i> Cetak
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-inbox fs-1 text-muted"></i>
                <p class="mt-3">Belum ada booking</p>
                <a href="{{ route('user.dashboard') }}" class="btn btn-primary-custom">Booking Sekarang</a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center mt-4">
        {{ $bookings->links() }}
    </div>
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
