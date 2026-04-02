<?php
// database/seeders/PelangganSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelanggan;

class PelangganSeeder extends Seeder
{
    public function run()
    {
        $pelanggans = [
            [
                'nama' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'no_telepon' => '081234567890',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Siti Aisyah',
                'email' => 'siti.aisyah@email.com',
                'no_telepon' => '082345678901',
                'alamat' => 'Jl. Sudirman No. 45, Bandung',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Ahmad Hidayat',
                'email' => 'ahmad.hidayat@email.com',
                'no_telepon' => '083456789012',
                'alamat' => 'Jl. Gatot Subroto No. 67, Surabaya',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Dewi Lestari',
                'email' => 'dewi.lestari@email.com',
                'no_telepon' => '084567890123',
                'alamat' => 'Jl. Diponegoro No. 89, Yogyakarta',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'nama' => 'Rudi Hartono',
                'email' => 'rudi.hartono@email.com',
                'no_telepon' => '085678901234',
                'alamat' => 'Jl. Pahlawan No. 12, Semarang',
                'created_at' => now(),
                'updated_at' => now()
            ],
        ];

        foreach ($pelanggans as $pelanggan) {
            Pelanggan::create($pelanggan);
        }
    }
}
