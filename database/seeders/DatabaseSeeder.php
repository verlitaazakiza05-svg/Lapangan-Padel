<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            LapanganSeeder::class,
            PelangganSeeder::class,
            BookingSeeder::class,
            PembayaranSeeder::class,
        ]);
    }
}
