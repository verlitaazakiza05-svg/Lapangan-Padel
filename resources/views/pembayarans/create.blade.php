@extends('layouts.app')

@section('title', 'Input Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Input Pembayaran Baru</h1>
        <a href="{{ route('pembayarans.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Pembayaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('pembayarans.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                @if(isset($booking) && $booking)
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="booking_id" class="form-label">Pilih Booking <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('booking_id') is-invalid @enderror"
                                id="booking_id"
                                name="booking_id"
                                {{ isset($booking) ? 'disabled' : '' }}
                                required>
                            <option value="">Pilih Booking</option>
                            @foreach($bookings as $bookingItem)
                            <option value="{{ $bookingItem->id }}"
                                    data-total="{{ $bookingItem->total_harga }}"
                                    data-kode="{{ $bookingItem->kode_booking }}"
                                    data-pelanggan="{{ $bookingItem->pelanggan->nama }}"
                                    {{ (old('booking_id', $booking->id ?? '') == $bookingItem->id) ? 'selected' : '' }}>
                                {{ $bookingItem->kode_booking }} - {{ $bookingItem->pelanggan->nama }} - Rp {{ number_format($bookingItem->total_harga, 0, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
                        @if(isset($booking))
                        <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                        @endif
                        @error('booking_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="jumlah" class="form-label">Jumlah Pembayaran <span class="text-danger">*</span></label>
                        <input type="number"
                               class="form-control @error('jumlah') is-invalid @enderror"
                               id="jumlah"
                               name="jumlah"
                               value="{{ old('jumlah', $booking->total_harga ?? '') }}"
                               min="0"
                               step="1000"
                               required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="totalInfo"></small>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="metode" class="form-label">Metode Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('metode') is-invalid @enderror"
                                id="metode"
                                name="metode"
                                required>
                            <option value="">Pilih Metode</option>
                            <option value="tunai" {{ old('metode') == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer_bank" {{ old('metode') == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="kartu_kredit" {{ old('metode') == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                            <option value="e_wallet" {{ old('metode') == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
                        </select>
                        @error('metode')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status Pembayaran <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror"
                                id="status"
                                name="status"
                                required>
                            <option value="">Pilih Status</option>
                            <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sukses" {{ old('status') == 'sukses' ? 'selected' : '' }}>Sukses</option>
                            <option value="gagal" {{ old('status') == 'gagal' ? 'selected' : '' }}>Gagal</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="tanggal_pembayaran" class="form-label">Tanggal Pembayaran</label>
                        <input type="datetime-local"
                               class="form-control @error('tanggal_pembayaran') is-invalid @enderror"
                               id="tanggal_pembayaran"
                               name="tanggal_pembayaran"
                               value="{{ old('tanggal_pembayaran', now()->format('Y-m-d\TH:i')) }}">
                        @error('tanggal_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran</label>
                        <input type="file"
                               class="form-control @error('bukti_pembayaran') is-invalid @enderror"
                               id="bukti_pembayaran"
                               name="bukti_pembayaran"
                               accept="image/*">
                        <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB</small>
                        @error('bukti_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan"
                              name="keterangan"
                              rows="3">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Simpan Pembayaran
                </button>
                <button type="reset" class="btn btn-warning">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </button>
            </form>
        </div>
    </div>

    @if(isset($booking) && $booking)
    <!-- Info Booking -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Informasi Booking</h6>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <tr>
                    <th width="150">Kode Booking</th>
                    <td>{{ $booking->kode_booking }}</td>
                </tr>
                <tr>
                    <th>Pelanggan</th>
                    <td>{{ $booking->pelanggan->nama }}</td>
                </tr>
                <tr>
                    <th>Lapangan</th>
                    <td>{{ $booking->lapangan->nama_lapangan }}</td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td>{{ $booking->tanggal_booking->format('d/m/Y') }}</td>
                </tr>
                <tr>
                    <th>Waktu</th>
                    <td>{{ substr($booking->waktu_mulai, 0, 5) }} - {{ substr($booking->waktu_selesai, 0, 5) }}</td>
                </tr>
                <tr>
                    <th>Total Harga</th>
                    <td class="fw-bold text-primary">Rp {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>
    </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    // Update jumlah maksimal berdasarkan booking yang dipilih
    $('#booking_id').change(function() {
        const selected = $(this).find('option:selected');
        const total = selected.data('total');
        const kode = selected.data('kode');
        const pelanggan = selected.data('pelanggan');

        if (total) {
            $('#jumlah').attr('max', total);
            $('#totalInfo').text('Total tagihan: Rp ' + new Intl.NumberFormat('id-ID').format(total));
        }
    });

    // Trigger change if there's a selected value
    if ($('#booking_id').val()) {
        $('#booking_id').trigger('change');
    }
});
</script>
@endpush
@endsection
