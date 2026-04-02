@extends('layouts.app')

@section('title', 'Edit Pembayaran')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Pembayaran</h1>
        <div>
            <a href="{{ route('pembayarans.show', $pembayaran->id) }}" class="btn btn-info">
                <i class="bi bi-eye"></i> Detail
            </a>
            <a href="{{ route('pembayarans.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Form Edit Pembayaran</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('pembayarans.update', $pembayaran->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Kode Pembayaran: <strong>{{ $pembayaran->kode_pembayaran }}</strong>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="booking_id" class="form-label">Booking <span class="text-danger">*</span></label>
                        <select class="form-select select2 @error('booking_id') is-invalid @enderror"
                                id="booking_id"
                                name="booking_id"
                                required>
                            <option value="">Pilih Booking</option>
                            @foreach($bookings as $bookingItem)
                            <option value="{{ $bookingItem->id }}"
                                    data-total="{{ $bookingItem->total_harga }}"
                                    {{ old('booking_id', $pembayaran->booking_id) == $bookingItem->id ? 'selected' : '' }}>
                                {{ $bookingItem->kode_booking }} - {{ $bookingItem->pelanggan->nama }} - Rp {{ number_format($bookingItem->total_harga, 0, ',', '.') }}
                            </option>
                            @endforeach
                        </select>
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
                               value="{{ old('jumlah', $pembayaran->jumlah) }}"
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
                            <option value="tunai" {{ old('metode', $pembayaran->metode) == 'tunai' ? 'selected' : '' }}>Tunai</option>
                            <option value="transfer_bank" {{ old('metode', $pembayaran->metode) == 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="kartu_kredit" {{ old('metode', $pembayaran->metode) == 'kartu_kredit' ? 'selected' : '' }}>Kartu Kredit</option>
                            <option value="e_wallet" {{ old('metode', $pembayaran->metode) == 'e_wallet' ? 'selected' : '' }}>E-Wallet</option>
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
                            <option value="pending" {{ old('status', $pembayaran->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="sukses" {{ old('status', $pembayaran->status) == 'sukses' ? 'selected' : '' }}>Sukses</option>
                            <option value="gagal" {{ old('status', $pembayaran->status) == 'gagal' ? 'selected' : '' }}>Gagal</option>
                            <option value="refund" {{ old('status', $pembayaran->status) == 'refund' ? 'selected' : '' }}>Refund</option>
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
                               value="{{ old('tanggal_pembayaran', $pembayaran->tanggal_pembayaran ? $pembayaran->tanggal_pembayaran->format('Y-m-d\TH:i') : '') }}">
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
                        <small class="text-muted">Format: JPG, JPEG, PNG. Maks: 2MB. Kosongkan jika tidak ingin mengubah bukti.</small>
                        @error('bukti_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if($pembayaran->bukti_pembayaran)
                <div class="mb-3">
                    <label class="form-label">Bukti Saat Ini</label>
                    <div>
                        <a href="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" target="_blank">
                            <img src="{{ asset('storage/' . $pembayaran->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="img-fluid rounded" style="max-height: 200px;">
                        </a>
                    </div>
                </div>
                @endif

                <div class="mb-3">
                    <label for="keterangan" class="form-label">Keterangan</label>
                    <textarea class="form-control @error('keterangan') is-invalid @enderror"
                              id="keterangan"
                              name="keterangan"
                              rows="3">{{ old('keterangan', $pembayaran->keterangan) }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Pembayaran
                </button>
                <a href="{{ route('pembayarans.show', $pembayaran->id) }}" class="btn btn-secondary">
                    <i class="bi bi-x-circle"></i> Batal
                </a>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    $('.select2').select2({
        theme: 'bootstrap-5',
        width: '100%'
    });

    $('#booking_id').change(function() {
        const selected = $(this).find('option:selected');
        const total = selected.data('total');

        if (total) {
            $('#jumlah').attr('max', total);
            $('#totalInfo').text('Total tagihan: Rp ' + new Intl.NumberFormat('id-ID').format(total));
        }
    });

    // Trigger change to show total
    $('#booking_id').trigger('change');
});
</script>
@endpush
@endsection
