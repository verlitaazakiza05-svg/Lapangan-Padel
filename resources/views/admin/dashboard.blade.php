@extends('layouts.app')

@section('title', 'Dashboard User')

@section('content')

<style>
    /* ... semua CSS Anda di sini ... */
</style>

<!-- USER NAVBAR -->
<div class="user-navbar">
    <!-- ... navbar content ... -->
</div>

<div class="container-fluid px-4">
    <!-- ... semua konten dashboard ... -->
</div>

<!-- MODAL BOOKING -->
<div class="modal fade" id="bookingModal" tabindex="-1">
    <!-- ... modal content ... -->
</div>

<script>
    let currentStep = 1;
    let selectedLapangan = null;
    let selectedTime = null;

    // Dropdown user menu
    document.getElementById('userMenuBtn')?.addEventListener('click', function() {
        const dropdown = document.getElementById('userDropdown');
        if (dropdown) {
            dropdown.classList.toggle('show');
        }
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('userDropdown');
        const userMenuBtn = document.getElementById('userMenuBtn');
        if (dropdown && userMenuBtn && !userMenuBtn.contains(event.target) && !dropdown.contains(event.target)) {
            dropdown.classList.remove('show');
        }
    });

    function quickBooking(id, nama, harga) {
        selectedLapangan = { id: id, nama: nama, harga: harga };
        openBookingModal();

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

        if (event && event.currentTarget) {
            event.currentTarget.style.border = '2px solid #800020';
        }

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
            if (step) {
                if (i < currentStep) step.className = 'step completed';
                else if (i === currentStep) step.className = 'step active';
                else step.className = 'step';
            }
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

    // ==================== INI ADALAH FUNCTION SUBMIT BOOKING ====================
    function submitBooking() {
        // Validasi data
        if (!selectedLapangan) {
            alert('Pilih lapangan terlebih dahulu');
            return;
        }

        const tanggal = document.getElementById('tanggal_booking').value;
        if (!tanggal) {
            alert('Pilih tanggal booking');
            return;
        }

        const waktuMulai = selectedTime;
        if (!waktuMulai) {
            alert('Pilih waktu booking');
            return;
        }

        const totalJam = document.getElementById('total_jam').value;
        if (!totalJam || totalJam < 1 || totalJam > 4) {
            alert('Durasi booking harus antara 1-4 jam');
            return;
        }

        // Hitung waktu selesai
        let [hours, minutes] = waktuMulai.split(':');
        let endHour = parseInt(hours) + parseInt(totalJam);
        let waktuSelesai = endHour.toString().padStart(2, '0') + ':' + minutes + ':00';
        let waktuMulaiFormatted = waktuMulai + ':00';

        // Buat form dan submit langsung
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("bookings.store") }}';

        // CSRF token
        const csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = '{{ csrf_token() }}';
        form.appendChild(csrf);

        // Data booking
        const fields = {
            lapangan_id: selectedLapangan.id,
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

        // Tampilkan loading
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerText;
        submitBtn.innerText = 'Memproses...';
        submitBtn.disabled = true;

        document.body.appendChild(form);

        // Submit form
        try {
            form.submit();
        } catch (error) {
            console.error('Error submitting form:', error);
            alert('Terjadi kesalahan, silakan coba lagi');
            submitBtn.innerText = originalText;
            submitBtn.disabled = false;
        }
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

    // Event listener untuk tanggal booking
    document.getElementById('tanggal_booking')?.addEventListener('change', function() {
        generateTimeSlots();
    });

    // Generate time slots saat halaman dimuat
    generateTimeSlots();
</script>

@endsection
