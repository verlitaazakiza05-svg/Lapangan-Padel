@extends('layouts.app')

@section('title', 'Edit Booking')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Edit Booking</h1>
        <a href="{{ route('bookings.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <form method="POST" action="{{ route('bookings.update', $booking->id) }}">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Lapangan <span class="text-danger">*</span></label>
                            <select name="lapangan_id" class="form-select @error('lapangan_id') is-invalid @enderror" required>
                                <option value="">Pilih Lapangan</option>
                                @foreach($lapangans as $lapangan)
                                <option value="{{ $lapangan->id }}" {{ old('lapangan_id', $booking->lapangan_id) == $lapangan->id ? 'selected' : '' }}>
                                    {{ $lapangan->nama_lapangan }} - Rp {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}/jam
                                </option>
                                @endforeach
                            </select>
                            @error('lapangan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Pelanggan <span class="text-danger">*</span></label>
                            <select name="pelanggan_id" class="form-select @error('pelanggan_id') is-invalid @enderror" required>
                                <option value="">Pilih Pelanggan</option>
                                @foreach($pelanggans as $pelanggan)
                                <option value="{{ $pelanggan->id }}" {{ old('pelanggan_id', $booking->pelanggan_id) == $pelanggan->id ? 'selected' : '' }}>
                                    {{ $pelanggan->nama }} - {{ $pelanggan->email }}
                                </option>
                                @endforeach
                            </select>
                            @error('pelanggan_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tanggal Booking <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_booking" class="form-control @error('tanggal_booking') is-invalid @enderror" value="{{ old('tanggal_booking', $booking->tanggal_booking) }}" min="{{ date('Y-m-d') }}" required>
                            @error('tanggal_booking')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Waktu Mulai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_mulai" class="form-control @error('waktu_mulai') is-invalid @enderror" value="{{ old('waktu_mulai', substr($booking->waktu_mulai, 0, 5)) }}" required>
                            @error('waktu_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Waktu Selesai <span class="text-danger">*</span></label>
                            <input type="time" name="waktu_selesai" class="form-control @error('waktu_selesai') is-invalid @enderror" value="{{ old('waktu_selesai', substr($booking->waktu_selesai, 0, 5)) }}" required>
                            @error('waktu_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3">{{ old('catatan', $booking->catatan) }}</textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> Update Booking
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
