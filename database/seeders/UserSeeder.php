<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        $users = [
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@bookingpadel.com',
                'password' => Hash::make('super123'),
                'role' => 'superadmin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Admin Utama',
                'email' => 'admin@bookingpadel.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Operator Satu',
                'email' => 'operator1@bookingpadel.com',
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Operator Dua',
                'email' => 'operator2@bookingpadel.com',
                'password' => Hash::make('operator123'),
                'role' => 'operator',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Kasir',
                'email' => 'kasir@bookingpadel.com',
                'password' => Hash::make('kasir123'),
                'role' => 'kasir',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        $this->command->info(count($users) . ' user berhasil ditambahkan!');
    }
}
