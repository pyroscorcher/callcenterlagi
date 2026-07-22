<?php

namespace Database\Seeders;

use App\Models\Balai;
use Illuminate\Database\Seeder;

class BalaiSeeder extends Seeder
{
    public function run(): void
    {
        Balai::create([
            'username' => 'balai1',
            'password' => bcrypt('password123'),
            'unitkerja' => 'Balai Contoh',
            'unor' => 'Contoh Unor',
            'provinsi' => 'Sumatera Utara',
            'pulau' => 'Sumatera',
            'kepala' => 'Nama Kepala Balai',
            'kontak' => '081234567890',
        ]);
    }
}