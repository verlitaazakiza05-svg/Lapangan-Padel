@extends('layouts.user')

@section('title', 'Dashboard')

@section('content')

<style>
    :root {
        --primary: #800020;
        --primary-dark: #660019;
        --primary-light: #f8e9ed;
        --secondary: #ffc107;
        --success: #28a745;
        --gray-bg: #f8f9fa;
    }

    body {
        background: linear-gradient(135deg, #fdf7f9 0%, #f3e5e9 100%);
    }

    /* Hero Section */
    .hero-section {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 24px;
        padding: 2rem;
        margin-bottom: 2rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 300px;
        height: 300px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .hero-section::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: -10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    /* Card Stats */
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(128, 0, 32, 0.1);
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }

    /* Lapangan Card */
    .lapangan-card {
        background: white;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        cursor: pointer;
    }

    .lapangan-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(128, 0, 32, 0.15);
    }

    .lapangan-image {
        height: 200px;
        background-size: cover;
        background-position: center;
        position: relative;
    }

    .lapangan-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: var(--primary);
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: bold;
    }

    .lapangan-info {
        padding: 1.5rem;
    }

    .lapangan-price {
        color: var(--primary);
        font-size: 24px;
        font-weight: bold;
    }

    /* Promo Card */
    .promo-card {
        background: linear-gradient(135deg, var(--primary-light) 0%, white 100%);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
        border: 1px solid rgba(128, 0, 32, 0.1);
    }

    .promo-card:hover {
        transform: scale(1.05);
        box-shadow: 0 10px 25px rgba(128, 0, 32, 0.1);
    }

    .promo-icon {
        font-size: 48px;
        margin-bottom: 10px;
    }

    /* Button */
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary), var(--primary-dark));
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(128, 0, 32, 0.3);
        color: white;
    }

    .btn-outline-custom {
        background: transparent;
        border: 2px solid var(--primary);
        color: var(--primary);
        padding: 8px 20px;
        border-radius: 10px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-custom:hover {
        background: var(--primary);
        color: white;
    }

    /* Table */
    .table-custom {
        border-radius: 16px;
        overflow: hidden;
    }

    .table-custom thead {
        background: var(--primary-light);
    }

    .table-custom th {
        color: var(--primary);
        font-weight: 600;
        border: none;
    }

    /* Modal */
    .modal-content {
        border-radius: 24px;
        border: none;
    }

    .modal-header {
        background: var(--primary-light);
        border-bottom: none;
    }

    .booking-step {
        display: flex;
        justify-content: space-between;
        margin-bottom: 30px;
    }

    .step {
        text-align: center;
        flex: 1;
        position: relative;
    }

    .step-number {
        width: 40px;
        height: 40px;
        background: #e9ecef;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 10px;
        font-weight: bold;
        color: #6c757d;
    }

    .step.active .step-number {
        background: var(--primary);
        color: white;
    }

    .step.completed .step-number {
        background: var(--success);
        color: white;
    }

    .time-slots {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-top: 15px;
    }

    .time-slot {
        padding: 10px;
        text-align: center;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .time-slot:hover {
        border-color: var(--primary);
        background: var(--primary-light);
    }

    .time-slot.selected {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .time-slot.disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<div class="container-fluid px-4">

    <!-- HERO SECTION -->
    <div class="hero-section">
        <div class="hero-content">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-3">Halo, {{ Auth::user()->name }}! 👋</h1>
                    <p class="lead mb-4">Siap bermain padel hari ini? Booking lapangan favoritmu sekarang dan dapatkan
                        pengalaman bermain terbaik!</p>
                    <button class="btn btn-light btn-lg rounded-pill px-4" onclick="openBookingModal()">
                        <i class="bi bi-calendar-plus me-2"></i>Booking Sekarang
                    </button>
                </div>
                <div class="col-md-4 text-center">
                    <img src="https://cdn-icons-png.flaticon.com/512/2917/2917995.png" alt="Padel"
                        style="width: 180px; filter: brightness(0) invert(1);">
                </div>
            </div>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Booking</p>
                        <h2 class="fw-bold mb-0" style="color: var(--primary);">{{ $totalBookingSaya }}</h2>
                    </div>
                    <div class="stat-icon" style="background: var(--primary-light);">
                        <i class="bi bi-calendar-check fs-2" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Booking Aktif</p>
                        <h2 class="fw-bold mb-0 text-warning">{{ $bookingAktif }}</h2>
                    </div>
                    <div class="stat-icon" style="background: #fff3cd;">
                        <i class="bi bi-clock-history fs-2 text-warning"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Booking Selesai</p>
                        <h2 class="fw-bold mb-0 text-success">{{ $bookingSelesai }}</h2>
                    </div>
                    <div class="stat-icon" style="background: #d4edda;">
                        <i class="bi bi-check-circle fs-2 text-success"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="text-muted mb-1">Total Pengeluaran</p>
                        <h2 class="fw-bold mb-0" style="color: var(--primary);">Rp
                            {{ number_format($totalPengeluaran, 0, ',', '.') }}</h2>
                    </div>
                    <div class="stat-icon" style="background: var(--primary-light);">
                        <i class="bi bi-cash-stack fs-2" style="color: var(--primary);"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LAPANGAN TERSEDIA -->
    <div class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold" style="color: var(--primary);">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>Lapangan Tersedia
            </h3>
            <a href="{{ route('lapangans.index') }}" class="btn-outline-custom">Lihat Semua →</a>
        </div>
        <div class="row g-4">
            @forelse($lapanganTersedia as $lapangan)
                <div class="col-md-4 col-lg-3">
                    <div class="lapangan-card"
                        onclick="quickBooking({{ $lapangan->id }}, '{{ $lapangan->nama_lapangan ?? $lapangan->nama }}', {{ $lapangan->harga_per_jam }})">
                        <div class="lapangan-image"
                            style="background-image: url('{{ $lapangan->foto ? asset('storage/' . $lapangan->foto) : 'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?w=400&h=300&fit=crop' }}');">
                            <span class="lapangan-badge">Tersedia</span>
                        </div>
                        <div class="lapangan-info">
                            <h5 class="fw-bold mb-2">{{ $lapangan->nama_lapangan ?? $lapangan->nama }}</h5>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-geo-alt"></i> {{ $lapangan->tipe_lapangan ?? 'Lapangan Indoor' }}
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="lapangan-price">Rp
                                        {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}</span>
                                    <small class="text-muted">/jam</small>
                                </div>
                                <button class="btn btn-sm btn-primary-custom"
                                    style="padding: 8px 20px;">Booking</button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5">
                    <i class="bi bi-emoji-frown fs-1 text-muted"></i>
                    <p class="mt-3">Belum ada lapangan tersedia saat ini</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- PROMO SECTION -->
    <div class="mb-5">
        <h3 class="fw-bold mb-4" style="color: var(--primary);">
            <i class="bi bi-gift me-2"></i>Promo & Event
        </h3>
        <div class="row g-4">
            @foreach ($promos as $promo)
                <div class="col-md-4">
                    <div class="promo-card">
                        <div class="promo-icon">{{ $promo['image'] }}</div>
                        <h5 class="fw-bold mb-2">{{ $promo['title'] }}</h5>
                        <p class="text-muted mb-0">{{ $promo['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- BOOKING TERBARU -->
    <div class="card border-0 shadow-sm rounded-4 mb-5">
        <div class="card-header bg-white border-0 pt-4 pb-0">
            <h5 class="fw-bold mb-0" style="color: var(--primary);">
                <i class="bi bi-clock-history me-2"></i>Booking Terbaru
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Kode Booking</th>
                            <th>Lapangan</th>
                            <th>Tanggal</th>
                            <th>Waktu</th>
                            <th>Total Harga</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </thead>
                    <tbody>
                        @forelse($recentBookings as $booking)
                            <tr>
                                <td class="fw-bold">{{ $booking->kode_booking }}</td>
                                <td>{{ $booking->lapangan->nama_lapangan ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($booking->tanggal_booking)->format('d/m/Y') }}</td>
                                <td>{{ substr($booking->waktu_mulai, 0, 5) }} -
                                    {{ substr($booking->waktu_selesai, 0, 5) }}</td>
                                <td class="fw-bold" style="color: var(--primary);">Rp
                                    {{ number_format($booking->total_harga, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge px-3 py-2"
                                        style="background:
                                        @if($booking->status == 'dikonfirmasi') #28a745
                                        @elseif($booking->status == 'pending') #ffc107
                                        @elseif($booking->status == 'selesai') #17a2b8
                                        @else #dc3545 @endif; color: white;">
                                        {{ strtoupper($booking->status) }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('bookings.show', $booking->id) }}"
                                        class="btn btn-sm btn-outline-custom">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="bi bi-inbox fs-1 text-muted"></i>
                                    <p class="mt-2 mb-0">Belum ada booking</p>
                                    <button class="btn btn-primary-custom mt-3" onclick="openBookingModal()">Booking
                                        Sekarang</button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<!-- MODAL BOOKING -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" style="color: var(--primary);">
                    <i class="bi bi-calendar-plus me-2"></i>Booking Lapangan
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="booking-step">
                    <div class="step active" id="step1">
                        <div class="step-number">1</div>
                        <small>Pilih Lapangan</small>
                    </div>
                    <div class="step" id="step2">
                        <div class="step-number">2</div>
                        <small>Pilih Tanggal & Waktu</small>
                    </div>
                    <div class="step" id="step3">
                        <div class="step-number">3</div>
                        <small>Konfirmasi</small>
                    </div>
                </div>

                <form id="bookingForm">
                    @csrf
                    <div id="stepContent">
                        <!-- Step 1: Pilih Lapangan -->
                        <div id="step1Content">
                            <div class="row g-3" id="lapanganList">
                                @foreach ($lapanganTersedia as $lapangan)
                                    <div class="col-md-6">
                                        <div class="lapangan-card p-3"
                                            onclick="selectLapanganModal({{ $lapangan->id }}, '{{ $lapangan->nama_lapangan ?? $lapangan->nama }}', {{ $lapangan->harga_per_jam }})"
                                            style="cursor: pointer;">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h6 class="fw-bold mb-1">{{ $lapangan->nama_lapangan ?? $lapangan->nama }}</h6>
                                                    <small class="text-muted">Rp
                                                        {{ number_format($lapangan->harga_per_jam, 0, ',', '.') }}/jam</small>
                                                </div>
                                                <div class="radio-custom">
                                                    <input type="radio" name="lapangan_id"
                                                        value="{{ $lapangan->id }}"
                                                        id="lapangan_{{ $lapangan->id }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Step 2: Pilih Tanggal & Waktu -->
                        <div id="step2Content" style="display: none;">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Tanggal Booking</label>
                                <input type="date" class="form-control" id="tanggal_booking"
                                    min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Pilih Waktu</label>
                                <div class="time-slots" id="timeSlots">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Total Jam</label>
                                <input type="number" class="form-control" id="total_jam" min="1"
                                    max="4" value="1">
                            </div>
                        </div>

                        <!-- Step 3: Konfirmasi -->
                        <div id="step3Content" style="display: none;">
                            <div class="bg-light p-3 rounded-3">
                                <h6 class="fw-bold">Ringkasan Booking</h6>
                                <table class="table table-sm mt-3">
                                    <tr>
                                        <td>Lapangan</td>
                                        <td class="fw-bold" id="confirmLapangan">-</td>
                                    </tr>
                                    <tr>
                                        <td>Tanggal</td>
                                        <td class="fw-bold" id="confirmTanggal">-</td>
                                    </tr>
                                    <tr>
                                        <td>Waktu</td>
                                        <td class="fw-bold" id="confirmWaktu">-</td>
                                    </tr>
                                    <tr>
                                        <td>Durasi</td>
                                        <td class="fw-bold" id="confirmDurasi">-</td>
                                    </tr>
                                    <tr>
                                        <td>Total Harga</td>
                                        <td class="fw-bold text-primary" id="confirmTotal">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="prevBtn" onclick="prevStep()"
                    style="display: none;">Kembali</button>
                <button type="button" class="btn btn-primary-custom" id="nextBtn"
                    onclick="nextStep()">Lanjut</button>
                <button type="button" class="btn btn-primary-custom" id="submitBtn" onclick="submitBooking()"
                    style="display: none;">Booking Sekarang</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentStep = 1;
    let selectedLapangan = null;
    let selectedTime = null;

    function quickBooking(id, nama, harga) {
        selectedLapangan = { id: id, nama: nama, harga: harga };
        openBookingModal();

        // Tandai di modal
        setTimeout(() => {
            const radio = document.querySelector(`input[name="lapangan_id"][value="${id}"]`);
            if (radio) {
                radio.checked = true;
                const card = radio.closest('.lapangan-card');
                if (card) {
                    card.style.border = '2px solid #800020';
                }
            }
        }, 100);
    }

    function openBookingModal() {
        currentStep = 1;
        updateStepDisplay();
        document.getElementById('tanggal_booking').value = '';
        document.getElementById('total_jam').value = '1';
        generateTimeSlots();
        $('#bookingModal').modal('show');
    }

    function selectLapanganModal(id, nama, harga) {
        selectedLapangan = { id: id, nama: nama, harga: harga };

        document.querySelectorAll('#lapanganList .lapangan-card').forEach(card => {
            card.style.border = 'none';
        });
        event.currentTarget.style.border = '2px solid #800020';

        const radio = document.getElementById(`lapangan_${id}`);
        if (radio) radio.checked = true;
    }

    function updateStepDisplay() {
        document.getElementById('step1Content').style.display = currentStep === 1 ? 'block' : 'none';
        document.getElementById('step2Content').style.display = currentStep === 2 ? 'block' : 'none';
        document.getElementById('step3Content').style.display = currentStep === 3 ? 'block' : 'none';

        document.getElementById('prevBtn').style.display = currentStep > 1 ? 'inline-block' : 'none';
        document.getElementById('nextBtn').style.display = currentStep < 3 ? 'inline-block' : 'none';
        document.getElementById('submitBtn').style.display = currentStep === 3 ? 'inline-block' : 'none';

        for (let i = 1; i <= 3; i++) {
            const step = document.getElementById(`step${i}`);
            if (i < currentStep) step.className = 'step completed';
            else if (i === currentStep) step.className = 'step active';
            else step.className = 'step';
        }
    }

    function nextStep() {
        if (currentStep === 1 && !selectedLapangan) {
            alert('Pilih lapangan terlebih dahulu');
            return;
        }
        if (currentStep === 2) {
            const tanggal = document.getElementById('tanggal_booking').value;
            const waktu = document.querySelector('.time-slot.selected');
            if (!tanggal) {
                alert('Pilih tanggal booking');
                return;
            }
            if (!waktu) {
                alert('Pilih waktu booking');
                return;
            }
            selectedTime = waktu.dataset.time;

            document.getElementById('confirmLapangan').innerText = selectedLapangan.nama;
            document.getElementById('confirmTanggal').innerText = tanggal;
            document.getElementById('confirmWaktu').innerText = selectedTime;
            const jam = document.getElementById('total_jam').value;
            document.getElementById('confirmDurasi').innerText = jam + ' jam';
            const total = selectedLapangan.harga * jam;
            document.getElementById('confirmTotal').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(total);
        }
        currentStep++;
        updateStepDisplay();
    }

    function prevStep() {
        currentStep--;
        updateStepDisplay();
    }

    function submitBooking() {
    const lapanganId = selectedLapangan.id;
    const tanggal = document.getElementById('tanggal_booking').value;
    const waktuMulai = selectedTime;
    const totalJam = document.getElementById('total_jam').value;

    let [hours, minutes] = waktuMulai.split(':');
    let endHour = parseInt(hours) + parseInt(totalJam);
    let waktuSelesai = endHour.toString().padStart(2, '0') + ':' + minutes + ':00';
    let waktuMulaiFormatted = waktuMulai + ':00';

    // Buat form dan submit langsung (bukan AJAX)
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("bookings.store") }}';

    const csrf = document.createElement('input');
    csrf.type = 'hidden';
    csrf.name = '_token';
    csrf.value = '{{ csrf_token() }}';
    form.appendChild(csrf);

    const fields = {
        lapangan_id: lapanganId,
        tanggal_booking: tanggal,
        waktu_mulai: waktuMulaiFormatted,
        waktu_selesai: waktuSelesai,
        total_jam: totalJam
    };

    for (let key in fields) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = fields[key];
        form.appendChild(input);
    }

    document.body.appendChild(form);
    form.submit();
}

    function generateTimeSlots() {
        const slots = ['08:00', '09:00', '10:00', '11:00', '13:00', '14:00', '15:00', '16:00', '17:00', '18:00', '19:00', '20:00'];
        const container = document.getElementById('timeSlots');
        if (container) {
            container.innerHTML = slots.map(slot =>
                `<div class="time-slot" data-time="${slot}" onclick="selectTimeSlot(this)">${slot}</div>`
            ).join('');
        }
    }

    function selectTimeSlot(el) {
        document.querySelectorAll('.time-slot').forEach(slot => {
            slot.classList.remove('selected');
            slot.style.background = '';
            slot.style.color = '';
        });
        el.classList.add('selected');
        el.style.background = '#800020';
        el.style.color = 'white';
    }

    document.getElementById('tanggal_booking').addEventListener('change', function() {
        generateTimeSlots();
    });

    generateTimeSlots();
</script>

@endsection
