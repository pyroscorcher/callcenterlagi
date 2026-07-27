<?php
namespace Database\Seeders;

use App\Models\Balai;
use App\Models\Provinsi;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class BalaiProvinsiSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Daftar seluruh provinsi di Indonesia (Bisa Anda sesuaikan/tambahkan)
        $daftarProvinsi = [
            'ACEH', 'SUMATERA UTARA', 'SUMATERA BARAT', 'RIAU', 'JAMBI', 
            'SUMATERA SELATAN', 'BENGKULU', 'LAMPUNG', 'KEPULAUAN BANGKA BELITUNG', 'KEPULAUAN RIAU', 
            'DKI JAKARTA', 'JAWA BARAT', 'JAWA TENGAH', 'DI YOGYAKARTA', 'JAWA TIMUR', 'BANTEN'
        ];

        // Memasukkan data provinsi ke tabel provinsis
        foreach ($daftarProvinsi as $namaProv) {
            Provinsi::firstOrCreate(['nama' => $namaProv]);
        }

        // 2. Data lengkap Balai beserta provinsi yang dinaunginya (naungan)
        $dataBalai = [
            'BBWS Citarum' => [
                'unker'    => 'Ditjen SDA',
                'unor'     => 'SDA',
                'provinsi' => 'JAWA BARAT',        // Lokasi kantor Balai
                'pulau'    => 'Jawa',
                'kepala'   => 'Bapak Ir. Fulan',
                'kontak'   => '081200001111',
                'naungan'  => ['JAWA BARAT']       // Provinsi yang dipegang
            ],
            'BWS Sumatera II' => [
                'unker'    => 'Ditjen SDA',
                'unor'     => 'SDA',
                'provinsi' => 'SUMATERA UTARA',
                'pulau'    => 'Sumatera',
                'kepala'   => 'Ibu Ir. Fulani',
                'kontak'   => '081200002222',
                'naungan'  => ['SUMATERA UTARA', 'RIAU'] // Memegang 2 provinsi
            ],
            'BBWS Ciliwung Cisadane' => [
                'unker'    => 'Ditjen SDA',
                'unor'     => 'SDA',
                'provinsi' => 'DKI JAKARTA',
                'pulau'    => 'Jawa',
                'kepala'   => 'Bapak Dr. Budi',
                'kontak'   => '081200003333',
                'naungan'  => ['DKI JAKARTA', 'BANTEN']
            ],
        ];

        // 3. Proses pembuatan data dan relasi
        foreach ($dataBalai as $namaBalai => $detail) {
            
            // A. Buat record Balai sesuai struktur fillable model Anda
            $balai = Balai::firstOrCreate(
                ['nama_balai' => $namaBalai],
                [
                    // Username dibuat otomatis: misal 'BBWS Citarum' -> 'bbws_citarum'
                    'username' => strtolower(str_replace(' ', '_', $namaBalai)),
                    'password' => Hash::make('password123'), // Default password
                    'unker'    => $detail['unker'],
                    'unor'     => $detail['unor'],
                    'provinsi' => $detail['provinsi'],
                    'pulau'    => $detail['pulau'],
                    'kepala'   => $detail['kepala'],
                    'kontak'   => $detail['kontak'],
                ]
            );

            // B. Cari provinsi yang ada di dalam array 'naungan' lalu set balai_id nya
            if (isset($detail['naungan']) && count($detail['naungan']) > 0) {
                Provinsi::whereIn('nama', $detail['naungan'])->update([
                    'balai_id' => $balai->id
                ]);
            }
        }
        
        $this->command->info('Data Balai dan pemetaan Provinsi berhasil di-seed!');
    }
}