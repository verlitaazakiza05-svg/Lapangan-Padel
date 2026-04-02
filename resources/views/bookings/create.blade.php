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

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Form Booking</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('bookings.store') }}" method="POST" id="bookingForm">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="pelanggan_id" class="form-label">Pelanggan <span
                                    class="text-danger">*</span></label>
                            <select class="form-select select2 @error('pelanggan_id') is-invalid @enderror"
                                id="pelanggan_id" name="pelanggan_id" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach ($pelanggans as $pelanggan)
                                    <option value="{{ $pelanggan->id }}"
                                        {{ old('pelanggan_id', request('pelanggan_id')) == $pelanggan->id ? 'selected' : '' }}>
                                        {{ $pelanggan->nama }} - {{ $pelanggan->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pelanggan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <a href="{{ route('pelanggans.create') }}" target="_blank">Tambah pelanggan baru</a>
                            </small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="lapangan_id" class="form-label">Lapangan <span class="text-danger">*</span></label>
                            <select class="form-select select2 @error('lapangan_id') is-invalid @enderror" id="lapangan_id"
                                name="lapangan_id" required>
                                <option value="">Pilih Lapangan</option>
                                @foreach ($lapangans as $lapangan)
                                    <option value="{{ $lapangan->id }}" data-harga="{{ $lapangan->harga_per_jam }}"
                                        {{ old('lapangan_id') == $lapangan->id ? 'selected' : '' }}>
                                        {{ $lapangan->nama_lapangan }} - {{ $lapangan->tipe_lapangan }} (Rp
                                        {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}/jam)
                                    </option>
                                @endforeach
                            </select>
                            @error('lapangan_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="tanggal_booking" class="form-label">Tanggal Booking <span
                                    class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('tanggal_booking') is-invalid @enderror"
                                id="tanggal_booking" name="tanggal_booking"
                                value="{{ old('tanggal_booking', date('Y-m-d')) }}" min="{{ date('Y-m-d') }}" required>
                            @error('tanggal_booking')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="waktu_mulai" class="form-label">Waktu Mulai <span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('waktu_mulai') is-invalid @enderror"
                                id="waktu_mulai" name="waktu_mulai" value="{{ old('waktu_mulai', '09:00') }}" required>
                            @error('waktu_mulai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="waktu_selesai" class="form-label">Waktu Selesai <span
                                    class="text-danger">*</span></label>
                            <input type="time" class="form-control @error('waktu_selesai') is-invalid @enderror"
                                id="waktu_selesai" name="waktu_selesai" value="{{ old('waktu_selesai', '10:00') }}"
                                required>
                            @error('waktu_selesai')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <button type="button" class="btn btn-info" id="checkAvailability">
                                <i class="bi bi-check-circle"></i> Cek Ketersediaan
                            </button>
                            <span id="availabilityMessage" class="ms-3"></span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Jam</label>
                            <input type="text" class="form-control" id="total_jam" readonly value="0">
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Total Harga</label>
                            <input type="text" class="form-control" id="total_harga" readonly value="Rp 0">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="catatan" class="form-label">Catatan</label>
                        <textarea class="form-control @error('catatan') is-invalid @enderror" id="catatan" name="catatan" rows="3">{{ old('catatan') }}</textarea>
                        @error('catatan')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Buat Booking
                    </button>
                    <button type="reset" class="btn btn-warning">
                        <i class="bi bi-arrow-counterclockwise"></i> Reset
                    </button>
                </form>
            </div>
        </div>

        <!-- Available Times Card -->
        <div class="card shadow mb-4" id="availableTimesCard" style="display: none;">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Slot Waktu Tersedia</h6>
            </div>
            <div class="card-body">
                <div class="row" id="availableTimes">
                    <!-- Will be filled by JavaScript -->
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            $(document).ready(function() {
                // Initialize Select2
                $('.select2').select2({
                    theme: 'bootstrap-5',
                    width: '100%'
                });

                // Calculate total hours and price
                function calculateTotal() {
                    const mulai = $('#waktu_mulai').val();
                    const selesai = $('#waktu_selesai').val();
                    const hargaPerJam = $('#lapangan_id option:selected').data('harga') || 0;

                    if (mulai && selesai) {
                        const start = moment(mulai, 'HH:mm');
                        const end = moment(selesai, 'HH:mm');
                        const duration = moment.duration(end.diff(start));
                        const hours = duration.asHours();

                        if (hours > 0) {
                            $('#total_jam').val(hours.toFixed(1) + ' jam');
                            const total = hours * hargaPerJam;
                            $('#total_harga').val('Rp ' + new Intl.NumberFormat('id-ID').format(total));
                        } else {
                            $('#total_jam').val('0 jam');
                            $('#total_harga').val('Rp 0');
                        }
                    }
                }

                $('#waktu_mulai, #waktu_selesai, #lapangan_id').change(calculateTotal);
                calculateTotal();

                // Check availability
                $('#checkAvailability').click(function() {
                    const lapanganId = $('#lapangan_id').val();
                    const tanggal = $('#tanggal_booking').val();
                    const waktuMulai = $('#waktu_mulai').val();
                    const waktuSelesai = $('#waktu_selesai').val();

                    if (!lapanganId || !tanggal || !waktuMulai || !waktuSelesai) {
                        alert('Mohon lengkapi semua field terlebih dahulu');
                        return;
                    }

                    $.ajax({
                        url: '{{ route('lapangans.cek-ketersediaan') }}',
                        type: 'GET',
                        data: {
                            lapangan_id: lapanganId,
                            tanggal: tanggal,
                            waktu_mulai: waktuMulai,
                            waktu_selesai: waktuSelesai
                        },
                        success: function(response) {
                            if (response.tersedia) {
                                $('#availabilityMessage').html(
                                    '<span class="text-success"><i class="bi bi-check-circle"></i> ' +
                                    response.message + '</span>');
                            } else {
                                $('#availabilityMessage').html(
                                    '<span class="text-danger"><i class="bi bi-exclamation-triangle"></i> ' +
                                    response.message + '</span>');
                            }
                        }
                    });

                    // Get available times
                    $.ajax({
                        url: '{{ route('bookings.available-times') }}',
                        type: 'GET',
                        data: {
                            lapangan_id: lapanganId,
                            tanggal: tanggal
                        },
                        success: function(response) {
                            if (response.success) {
                                let html = '';
                                response.data.forEach(function(slot) {
                                    const statusClass = slot.tersedia ? 'success' :
                                        'secondary';
                                    const statusIcon = slot.tersedia ? 'check-circle' :
                                        'x-circle';
                                    html += `
                            <div class="col-md-3 mb-2">
                                <div class="card bg-${statusClass} text-white">
                                    <div class="card-body p-2 text-center">
                                        <i class="bi bi-${statusIcon}"></i>
                                        ${slot.waktu_mulai} - ${slot.waktu_selesai}
                                    </div>
                                </div>
                            </div>
                        `;
                                });
                                $('#availableTimes').html(html);
                                $('#availableTimesCard').show();
                            }
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
