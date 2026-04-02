@extends('layouts.app')

@section('title', 'Daftar Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Lapangan</h1>
        <a href="{{ route('lapangans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Lapangan
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filter</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('lapangans.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Cari</label>
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Nama lapangan...">
                </div>
                <div class="col-md-3">
                    <label for="tipe" class="form-label">Tipe</label>
                    <select class="form-select" id="tipe" name="tipe">
                        <option value="">Semua</option>
                        <option value="indoor" {{ request('tipe') == 'indoor' ? 'selected' : '' }}>Indoor</option>
                        <option value="outdoor" {{ request('tipe') == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Semua</option>
                        <option value="tersedia" {{ request('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                        <option value="perbaikan" {{ request('status') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                        <option value="tidak_tersedia" {{ request('status') == 'tidak_tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lapangan List -->
    <div class="row">
        @forelse($lapangans as $lapangan)
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col-4">
                            @if($lapangan->foto)
                                <img src="{{ asset('storage/' . $lapangan->foto) }}" alt="{{ $lapangan->nama_lapangan }}" class="img-fluid rounded">
                            @else
                                <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="height: 100px;">
                                    <i class="bi bi-image text-white fs-1"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-8">
                            <div class="ms-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1">{{ $lapangan->nama_lapangan }}</h5>
                                    <span class="badge bg-{{ $lapangan->status == 'tersedia' ? 'success' : ($lapangan->status == 'perbaikan' ? 'warning' : 'danger') }}">
                                        {{ $lapangan->status }}
                                    </span>
                                </div>
                                <p class="mb-1 text-muted">{{ $lapangan->tipe_lapangan }}</p>
                                <p class="mb-2">Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}/jam</p>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('lapangans.show', $lapangan->id) }}" class="btn btn-info">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('lapangans.edit', $lapangan->id) }}" class="btn btn-warning">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $lapangan->id }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Belum ada data lapangan.
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $lapangans->links() }}
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Konfirmasi Hapus</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus lapangan ini?</p>
                <p class="text-danger"><small>Lapangan yang memiliki booking aktif tidak dapat dihapus.</small></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    $('#deleteForm').attr('action', '/lapangans/' + id);
    $('#deleteModal').modal('show');
}

// Update status via AJAX
$('.status-select').change(function() {
    let lapanganId = $(this).data('id');
    let status = $(this).val();

    $.ajax({
        url: '/lapangans/' + lapanganId + '/update-status',
        type: 'POST',
        data: {
            status: status,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if (response.success) {
                alert(response.message);
            }
        }
    });
});
</script>
@endpush
@endsection
