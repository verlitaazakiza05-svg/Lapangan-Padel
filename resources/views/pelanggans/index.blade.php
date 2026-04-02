@extends('layouts.app')

@section('title', 'Daftar Pelanggan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Daftar Pelanggan</h1>
        <a href="{{ route('pelanggans.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Tambah Pelanggan
        </a>
    </div>

    <!-- Search Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Cari Pelanggan</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('pelanggans.index') }}" class="row g-3">
                <div class="col-md-10">
                    <input type="text" class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Cari berdasarkan nama, email, atau no telepon...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Pelanggan List -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="pelangganTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
                            <th>Total Booking</th>
                            <th>Bergabung</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pelanggans as $index => $pelanggan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $pelanggan->nama }}</td>
                            <td>{{ $pelanggan->email }}</td>
                            <td>{{ $pelanggan->no_telepon }}</td>
                            <td>{{ Str::limit($pelanggan->alamat, 30) ?? '-' }}</td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $pelanggan->bookings_count ?? $pelanggan->bookings->count() }}</span>
                            </td>
                            <td>{{ $pelanggan->created_at->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('pelanggans.show', $pelanggan->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('pelanggans.edit', $pelanggan->id) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmDelete({{ $pelanggan->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">Tidak ada data pelanggan</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-3">
                {{ $pelanggans->links() }}
            </div>
        </div>
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
                <p>Apakah Anda yakin ingin menghapus pelanggan ini?</p>
                <p class="text-danger"><small>Pelanggan yang memiliki riwayat booking tidak dapat dihapus.</small></p>
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
    $('#deleteForm').attr('action', '/pelanggans/' + id);
    $('#deleteModal').modal('show');
}

$(document).ready(function() {
    $('#pelangganTable').DataTable({
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
