<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed users table with admin and customers.
     */
    public function run(): void
    {
        $users = [
            // Admin Accounts
            [
                'name' => 'Admin Utama RentCar',
                'email' => 'admin@rentcar.com',
                'password' => Hash::make('admin123'),
                'phone' => '081234567890',
                'address' => 'Jl. Sudirman No. 45, Jakarta Pusat',
                'role' => 'admin',
            ],
            [
                'name' => 'Petugas Rental (Staff)',
                'email' => 'staff@rentcar.com',
                'password' => Hash::make('staff123'),
                'phone' => '081298765432',
                'address' => 'Jl. Thamrin No. 12, Jakarta Pusat',
                'role' => 'staff',
            ],

            // Customer Accounts
            [
                'name' => 'Kafka Mumtazul Hizam',
                'email' => 'kafka@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '089876543210',
                'address' => 'Jl. Kebon Jeruk No. 10, Jakarta Barat',
                'role' => 'customer',
            ],
            [
                'name' => 'Ahmad Rizky',
                'email' => 'ahmad.rizky@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '081345678901',
                'address' => 'Jl. Melati No. 8, Jakarta Selatan',
                'role' => 'customer',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '085678901234',
                'address' => 'Jl. Cempaka Putih No. 22, Jakarta Pusat',
                'role' => 'customer',
            ],
            [
                'name' => 'Siti Nurhaliza',
                'email' => 'siti.nur@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '087812345678',
                'address' => 'Jl. Tebet Barat Dalam No. 15, Jakarta Selatan',
                'role' => 'customer',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '082198765432',
                'address' => 'Jl. Rawamangun Muka No. 3, Jakarta Timur',
                'role' => 'customer',
            ],
            [
                'name' => 'Hendra Wijaya',
                'email' => 'hendra.wijaya@gmail.com',
                'password' => Hash::make('password123'),
                'phone' => '081912345678',
                'address' => 'Jl. Kelapa Gading Boulevard No. 99, Jakarta Utara',
                'role' => 'customer',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
