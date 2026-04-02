<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Booking;
use App\Models\Lapangan;
use App\Models\Pelanggan;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $lapangans = Lapangan::all();
        $pelanggans = Pelanggan::all();

        if ($lapangans->isEmpty() || $pelanggans->isEmpty()) {
            $this->command->error('Lapangan atau Pelanggan kosong! Jalankan seeder lain dulu.');
            return;
        }

        $bookings = [
            [
                'lapangan_id' => $lapangans[0]->id,
                'pelanggan_id' => $pelanggans[0]->id,
                'tanggal_booking' => Carbon::today(),
                'waktu_mulai' => '09:00:00',
                'waktu_selesai' => '11:00:00',
                'status' => 'dikonfirmasi',
                'catatan' => 'Booking untuk latihan rutin'
            ],
            [
                'lapangan_id' => $lapangans[1]->id,
                'pelanggan_id' => $pelanggans[1]->id,
                'tanggal_booking' => Carbon::today(),
                'waktu_mulai' => '14:00:00',
                'waktu_selesai' => '16:00:00',
                'status' => 'dikonfirmasi',
                'catatan' => 'Booking untuk pertandingan'
            ],
        ];

        foreach ($bookings as $bookingData) {
            $lapangan = Lapangan::find($bookingData['lapangan_id']);

            // Hitung total jam
            $start = Carbon::parse($bookingData['waktu_mulai']);
            $end = Carbon::parse($bookingData['waktu_selesai']);
            $totalJam = $end->diffInHours($start);

            Booking::create([
                'kode_booking' => 'PDL' . date('Ymd') . str_pad(rand(1,999), 3, '0', STR_PAD_LEFT),
                'lapangan_id' => $bookingData['lapangan_id'],
                'pelanggan_id' => $bookingData['pelanggan_id'],
                'tanggal_booking' => $bookingData['tanggal_booking'],
                'waktu_mulai' => $bookingData['waktu_mulai'],
                'waktu_selesai' => $bookingData['waktu_selesai'],
                'total_jam' => $totalJam,
                'total_harga' => $totalJam * $lapangan->harga_per_jam,
                'status' => $bookingData['status'],
                'catatan' => $bookingData['catatan'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }

        $this->command->info('Booking berhasil di-seed: ' . count($bookings) . ' data');
    }
}
