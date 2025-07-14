<?php

namespace Database\Seeders;

use App\Models\Kendaraan;
use App\Models\Penyewa;
use App\Models\User;
use App\Models\Servis;
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
            'gambar' => 'fotoKendaraan/vario160.png',
            'harga_6jam' => 50000,
            'harga_12jam' => 70000,
            'harga_24jam' => 100000,
            'bbm' => 5,
            'bbm_per_kotak' => 13000,
            'status' => 'TERSEDIA',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Penyewa::insert([
            'nama' => 'Farrel',
            'alamat' => 'Jl. Janti Barat',
            'no_telp' => '8113663727',
            'jenis_kelamin' => 'L',
            'jaminan1' => 'KTP',
            'foto_jaminan1' => 'fotoJaminan/KTP.jpg',
            'created_at' => now(),
            'updated_at' => now()
        ]);

        Servis::insert([
            'kendaraan_id' => 1,
            'deskripsi' => 'ganti oli',
            'kilometer_servis' => 13000,
            'tipe_servis' => 'OLI',
            'bengkel' => 'om yopi',
            'tanggal_servis' => '2025-03-07',
            'created_at' => now(),
            'updated_at' => now()

        ]);
    }
}
