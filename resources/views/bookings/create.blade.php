@extends('layouts.app')

@section('title', 'Buat Booking Baru')

@section('content')
    <div class="container-fluid">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">Buat Booking Baru</h1>
            <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Booking</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <div class="row">
                        {{-- Lapangan --}}
                        <div class="col-md-6 mb-3">
                            <label for="lapangan_id" class="form-label">
                                Lapangan <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('lapangan_id') is-invalid @enderror"
                                    id="lapangan_id" name="lapangan_id" required>
                                <option value="">Pilih Lapangan</option>
                                @foreach ($lapangans as $lapangan)
                                    <option value="{{ $lapangan->id }}"
                                            data-harga="{{ $lapangan->harga_per_jam }}"
                                            {{ old('lapangan_id') == $lapangan->id ? 'selected' : '' }}>
                                        {{ $lapangan->nama_lapangan }} - {{ $lapangan->tipe_lapangan }}
                                        (Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}/jam)
                                    </option>
                                @endforeach
                            </select>
                            @error('lapangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Tanggal --}}
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_booking" class="form-label">
                                Tanggal Booking <span class="text-danger">*</span>
                            </label>
                            <input type="date"
                                   class="form-control @error('tanggal_booking') is-invalid @enderror"
                                   id="tanggal_booking" name="tanggal_booking"
                                   value="{{ old('tanggal_booking', date('Y-m-d')) }}"
                                   min="{{ date('Y-m-d') }}" required>
                            @error('tanggal_booking')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        {{-- Waktu Mulai --}}
                        <div class="col-md-6 mb-3">
                            <label for="waktu_mulai" class="form-label">
                                Waktu Mulai <span class="text-danger">*</span>
                            </label>
                            <input type="time"
                                   class="form-control @error('waktu_mulai') is-invalid @enderror"
                                   id="waktu_mulai" name="waktu_mulai"
                                   value="{{ old('waktu_mulai', '09:00') }}" required>
                            @error('waktu_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Waktu Selesai --}}
                        <div class="col-md-6 mb-3">
                            <label for="waktu_selesai" class="form-label">
                                Waktu Selesai <span class="text-danger">*</span>
                            </label>
                            <input type="time"
                                   class="form-control @error('waktu_selesai') is-invalid @enderror"
                                   id="waktu_selesai" name="waktu_selesai"
                                   value="{{ old('waktu_selesai', '10:00') }}" required>
                            @error('waktu_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Ringkasan otomatis --}}
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Total Jam</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-clock"></i></span>
                                {{-- READONLY display --}}
                                <input type="text" class="form-control" id="total_jam_display" readonly
                                       placeholder="0 jam">
                                {{-- HIDDEN field yang dikirim ke controller --}}
                                <input type="hidden" id="total_jam" name="total_jam" value="{{ old('total_jam', 0) }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Total Harga</label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="text" class="form-control" id="total_harga_display" readonly
                                       placeholder="0">
                                <input type="hidden" id="total_harga_val" name="total_harga" value="{{ old('total_harga', 0) }}">
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="button" class="btn btn-outline-info w-100" id="checkAvailability">
                                <i class="bi bi-search"></i> Cek Ketersediaan
                            </button>
                        </div>
                    </div>

                    {{-- Pesan ketersediaan --}}
                    <div id="availabilityMessage" class="mb-3" style="display:none;"></div>

                    {{-- Catatan --}}
                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror"
                                  id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" id="btnSubmit" disabled>
                            <i class="bi bi-save"></i> Buat Booking
                        </button>
                        <button type="reset" class="btn btn-warning" id="btnReset">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    </div>
                    <small class="text-muted mt-1 d-block">
                        <i class="bi bi-info-circle"></i>
                        Pilih lapangan, tanggal, dan waktu agar tombol Buat Booking aktif.
                    </small>
                </form>
            </div>
        </div>

        {{-- Slot Waktu Tersedia --}}
        <div class="card shadow mb-4" id="availableTimesCard" style="display:none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">
                    <i class="bi bi-calendar3"></i> Slot Waktu Tersedia
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-2" id="availableTimes"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const lapanganEl   = document.getElementById('lapangan_id');
    const tanggalEl    = document.getElementById('tanggal_booking');
    const mulaiEl      = document.getElementById('waktu_mulai');
    const selesaiEl    = document.getElementById('waktu_selesai');
    const totalJamDisp = document.getElementById('total_jam_display');
    const totalJamHid  = document.getElementById('total_jam');
    const totalHargaDisp = document.getElementById('total_harga_display');
    const totalHargaHid  = document.getElementById('total_harga_val');
    const btnSubmit    = document.getElementById('btnSubmit');
    const btnReset     = document.getElementById('btnReset');
    const msgEl        = document.getElementById('availabilityMessage');

    // ── Hitung total jam & harga ────────────────────────────────────────────
    function calculateTotal() {
        const mulai   = mulaiEl.value;
        const selesai = selesaiEl.value;
        const hargaOpt = lapanganEl.options[lapanganEl.selectedIndex];
        const hargaPerJam = hargaOpt ? parseInt(hargaOpt.dataset.harga || 0) : 0;

        if (!mulai || !selesai) {
            resetTotals();
            return;
        }

        const [hM, mM] = mulai.split(':').map(Number);
        const [hS, mS] = selesai.split(':').map(Number);
        const totalMenit = (hS * 60 + mS) - (hM * 60 + mM);

        if (totalMenit <= 0) {
            resetTotals();
            showMsg('danger', '<i class="bi bi-exclamation-triangle"></i> Waktu selesai harus lebih besar dari waktu mulai.');
            btnSubmit.disabled = true;
            return;
        }

        // Hitung jam (ceiling ke 0.5 jam atau bilangan bulat sesuai kebutuhan)
        const totalJam   = Math.ceil(totalMenit / 60);  // dibulatkan ke atas
        const totalHarga = totalJam * hargaPerJam;

        totalJamDisp.value   = totalJam + ' jam';
        totalJamHid.value    = totalJam;
        totalHargaDisp.value = new Intl.NumberFormat('id-ID').format(totalHarga);
        totalHargaHid.value  = totalHarga;

        validateForm();
    }

    function resetTotals() {
        totalJamDisp.value   = '0 jam';
        totalJamHid.value    = 0;
        totalHargaDisp.value = '0';
        totalHargaHid.value  = 0;
        btnSubmit.disabled   = true;
    }

    function validateForm() {
        const ok = lapanganEl.value &&
                   tanggalEl.value &&
                   mulaiEl.value &&
                   selesaiEl.value &&
                   parseInt(totalJamHid.value) > 0;
        btnSubmit.disabled = !ok;
    }

    function showMsg(type, html) {
        msgEl.innerHTML = `<div class="alert alert-${type} py-2">${html}</div>`;
        msgEl.style.display = 'block';
    }

    function hideMsg() {
        msgEl.style.display = 'none';
        msgEl.innerHTML = '';
    }

    [lapanganEl, tanggalEl, mulaiEl, selesaiEl].forEach(el => {
        el.addEventListener('change', () => { hideMsg(); calculateTotal(); });
    });

    // Hitung saat halaman load (jika ada old value)
    calculateTotal();

    // ── Cek Ketersediaan ───────────────────────────────────────────────────
    document.getElementById('checkAvailability').addEventListener('click', function () {
        const lapanganId = lapanganEl.value;
        const tanggal    = tanggalEl.value;
        const waktuMulai = mulaiEl.value;
        const waktuSelesai = selesaiEl.value;

        if (!lapanganId || !tanggal || !waktuMulai || !waktuSelesai) {
            showMsg('warning', '<i class="bi bi-exclamation-triangle"></i> Lengkapi lapangan, tanggal, dan waktu terlebih dahulu.');
            return;
        }

        if (parseInt(totalJamHid.value) <= 0) {
            showMsg('danger', '<i class="bi bi-exclamation-triangle"></i> Waktu selesai harus lebih besar dari waktu mulai.');
            return;
        }

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Mengecek...';

        // ── 1. Cek slot yang dipilih tersedia atau tidak ──
        fetch(`{{ route('lapangans.cek-ketersediaan') }}?lapangan_id=${lapanganId}&tanggal=${tanggal}&waktu_mulai=${waktuMulai}&waktu_selesai=${waktuSelesai}`)
            .then(r => r.json())
            .then(data => {
                if (data.tersedia) {
                    showMsg('success', '<i class="bi bi-check-circle-fill"></i> ' + data.message);
                } else {
                    showMsg('danger', '<i class="bi bi-x-circle-fill"></i> ' + data.message);
                }
            })
            .catch(() => showMsg('warning', 'Gagal mengecek ketersediaan. Coba lagi.'))
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-search"></i> Cek Ketersediaan';
            });

        // ── 2. Tampilkan semua slot hari itu ──
        fetch(`{{ route('bookings.available-times') }}?lapangan_id=${lapanganId}&tanggal=${tanggal}`)
            .then(r => r.json())
            .then(data => {
                if (!data.success) return;

                let html = '';
                data.data.forEach(slot => {
                    const color = slot.tersedia ? 'success' : 'secondary';
                    const icon  = slot.tersedia ? 'check-circle' : 'x-circle';
                    const label = slot.tersedia ? 'Tersedia' : 'Terbooked';
                    html += `
                        <div class="col-6 col-md-3 col-lg-2">
                            <div class="card text-white bg-${color} text-center py-2 px-1">
                                <small><i class="bi bi-${icon}"></i> ${slot.waktu_mulai}–${slot.waktu_selesai}</small>
                                <small>${label}</small>
                            </div>
                        </div>`;
                });

                document.getElementById('availableTimes').innerHTML = html || '<p class="text-muted">Tidak ada data slot.</p>';
                document.getElementById('availableTimesCard').style.display = 'block';
            })
            .catch(() => {});
    });

    // ── Reset ──────────────────────────────────────────────────────────────
    btnReset.addEventListener('click', function () {
        setTimeout(() => {
            hideMsg();
            resetTotals();
            document.getElementById('availableTimesCard').style.display = 'none';
            document.getElementById('availableTimes').innerHTML = '';
        }, 10);
    });

});
</script>
@endpush
