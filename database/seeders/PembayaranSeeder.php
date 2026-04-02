<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pembayaran;
use App\Models\Booking;

class PembayaranSeeder extends Seeder
{
    public function run()
    {
        $bookings = Booking::all();

        if ($bookings->count() == 0) {
            $this->command->info('Tidak ada booking untuk dibuat pembayaran');
            return;
        }

        foreach ($bookings as $index => $booking) {

            $metodes = ['transfer_bank', 'e_wallet', 'tunai', 'kartu_kredit'];
            $statuses = ['sukses', 'pending', 'gagal'];

            Pembayaran::create([
                'kode_pembayaran' => Pembayaran::generateKodePembayaran(),
                'booking_id' => $booking->id,
                'jumlah' => $booking->total_harga,
                'metode' => $metodes[$index % count($metodes)],
                'status' => $statuses[$index % count($statuses)],
                'tanggal_pembayaran' => now(),
                'keterangan' => 'Pembayaran otomatis dari seeder',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('Pembayaran berhasil di-seed: '.$bookings->count().' data');
    }
}
