<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Lapangan;
use Illuminate\Support\Facades\DB;

class LapanganSeeder extends Seeder
{
    public function run()
    {
        // Matikan sementara foreign key
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('lapangans')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $lapangans = [
            [
                'nama_lapangan' => 'Lapangan Padel A',
                'deskripsi' => 'Lapangan utama dengan fasilitas lengkap',
                'tipe_lapangan' => 'indoor',
                'harga_per_jam' => 150000,
                'status' => 'tersedia',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_lapangan' => 'Lapangan Padel B',
                'deskripsi' => 'Lapangan dengan pencahayaan baik',
                'tipe_lapangan' => 'indoor',
                'harga_per_jam' => 150000,
                'status' => 'tersedia',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_lapangan' => 'Lapangan Padel C',
                'deskripsi' => 'Lapangan outdoor dengan pemandangan',
                'tipe_lapangan' => 'outdoor',
                'harga_per_jam' => 120000,
                'status' => 'tersedia',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_lapangan' => 'Lapangan Padel D',
                'deskripsi' => 'Lapangan VIP dengan fasilitas premium',
                'tipe_lapangan' => 'indoor',
                'harga_per_jam' => 200000,
                'status' => 'tersedia',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama_lapangan' => 'Lapangan Padel E',
                'deskripsi' => 'Lapangan untuk latihan dan kompetisi',
                'tipe_lapangan' => 'outdoor',
                'harga_per_jam' => 120000,
                'status' => 'perbaikan',
                'foto' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($lapangans as $lapangan) {
            Lapangan::create($lapangan);
        }

        $this->command->info('Lapangan berhasil di-seed: ' . count($lapangans) . ' data');
    }
}
