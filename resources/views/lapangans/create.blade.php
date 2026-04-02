@extends('layouts.app')

@section('title', 'Tambah Lapangan')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Tambah Lapangan Baru</h1>
        <a href="{{ route('lapangans.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Lapangan</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('lapangans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_lapangan" class="form-label">Nama Lapangan <span class="text-danger">*</span></label>
                        <input type="text"
                               class="form-control @error('nama_lapangan') is-invalid @enderror"
                               id="nama_lapangan"
                               name="nama_lapangan"
                               value="{{ old('nama_lapangan') }}"
                               required>
                        @error('nama_lapangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="tipe_lapangan" class="form-label">Tipe Lapangan <span class="text-danger">*</span></label>
                        <select class="form-select @error('tipe_lapangan') is-invalid @enderror"
                                id="tipe_lapangan"
                                name="tipe_lapangan"
                                required>
                            <option value="">Pilih Tipe</option>
                            <option value="indoor" {{ old('tipe_lapangan') == 'indoor' ? 'selected' : '' }}>Indoor</option>
                            <option value="outdoor" {{ old('tipe_lapangan') == 'outdoor' ? 'selected' : '' }}>Outdoor</option>
                        </select>
                        @error('tipe_lapangan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="">Pilih Status</option>
                            <option value="tersedia" {{ old('status') == 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="perbaikan" {{ old('status') == 'perbaikan' ? 'selected' : '' }}>Perbaikan</option>
                            <option value="tidak_tersedia" {{ old('status') == 'tidak_tersedia' ? 'selected' : '' }}>Tidak Tersedia</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="harga_per_jam" class="form-label">Harga per Jam (Rp) <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control @error('harga_per_jam') is-invalid @enderror"
                               id="harga_per_jam"
                               name="harga_per_jam"
                               value="{{ old('harga_per_jam') }}"
                               min="0"
                               step="1000"
                               required>
                        @error('harga_per_jam')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="foto" class="form-label">Foto Lapangan</label>
                        <input type="file"
                               class="form-control @error('foto') is-invalid @enderror"
                               id="foto"
                               name="foto"
                               accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                        @error('foto')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="deskripsi" class="form-label">Deskripsi</label>
                    <textarea class="form-control @error('deskripsi') is-invalid @enderror"
                              id="deskripsi"
                              name="deskripsi"
                              rows="4">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="preview_foto" onclick="previewImage()">
                        <label class="form-check-label" for="preview_foto">
                            Preview Foto
                        </label>
                    </div>
                </div>

                <div class="mb-3" id="imagePreview" style="display: none;">
                    <img id="preview" src="#" alt="Preview" class="img-fluid rounded" style="max-height: 300px;">
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan
                </button>
                <button type="reset" class="btn btn-warning">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function previewImage() {
    const preview = document.getElementById('imagePreview');
    const checkbox = document.getElementById('preview_foto');

    if (checkbox.checked) {
        const file = document.getElementById('foto').files[0];
        const reader = new FileReader();

        reader.onloadend = function() {
            document.getElementById('preview').src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
        }
    } else {
        preview.style.display = 'none';
    }
}

document.getElementById('foto').addEventListener('change', function() {
    if (document.getElementById('preview_foto').checked) {
        previewImage();
    }
});
</script>
@endpush
@endsection
