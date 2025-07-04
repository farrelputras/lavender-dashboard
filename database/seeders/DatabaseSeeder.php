<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Admin
        User::factory()->create([
            'name' => 'Lavender',
            'email' => 'admin@lavender.com',
            'password' => Hash::make('gama35'),
            'email_verified_at' => now()
        ]);

        // User::factory(10)->create();

        Kendaraan::insert([
            'nopol' => 'N 6717 ACI',
            'jenis' => 'MOTOR',
            'model' => 'Honda Vario 160',
            'tahun' => 2022,
            'kilometer' => 16000,
            'gambar' => 'fotokendaraan/vario160.jpg',
            'status' => 'TERSEDIA',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }
}
