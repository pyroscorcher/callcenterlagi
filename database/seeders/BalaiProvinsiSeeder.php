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
        // 1. Masukkan data Master Provinsi
        $daftarProvinsi = [
            'DKI JAKARTA', 'JAWA BARAT', 'JAWA TENGAH', 'BANTEN'
        ];

        foreach ($daftarProvinsi as $namaProv) {
            Provinsi::firstOrCreate(['nama' => $namaProv]);
        }

        // 2. Data Master Balai beserta naungan provinsinya
        $dataBalai = [
            // Balai SDA (Air)
            'BBWS Citarum' => [
                'unker'    => 'Ditjen SDA',
                'unor'     => 'SDA',
                'provinsi' => 'JAWA BARAT',
                'pulau'    => 'Jawa',
                'kepala'   => 'Bapak Fulan (SDA)',
                'kontak'   => '081111',
                'naungan'  => ['JAWA BARAT'] // Citarum pegang Jabar
            ],
            // Balai Bina Marga (Jalan & Jembatan)
            'BPJN Jawa Barat' => [
                'unker'    => 'Ditjen Bina Marga',
                'unor'     => 'Bina Marga',
                'provinsi' => 'JAWA BARAT',
                'pulau'    => 'Jawa',
                'kepala'   => 'Ibu Fulani (BM)',
                'kontak'   => '082222',
                'naungan'  => ['JAWA BARAT'] // BPJN juga pegang Jabar
            ],
            // Balai Lintas Provinsi
            'BBWS Ciliwung Cisadane' => [
                'unker'    => 'Ditjen SDA',
                'unor'     => 'SDA',
                'provinsi' => 'DKI JAKARTA',
                'pulau'    => 'Jawa',
                'kepala'   => 'Bapak Budi',
                'kontak'   => '083333',
                'naungan'  => ['DKI JAKARTA', 'BANTEN', 'JAWA BARAT'] // Pegang 3 Provinsi
            ],
        ];

        // 3. Proses Insert & Sync (Menghubungkan Balai dan Provinsi)
        foreach ($dataBalai as $namaBalai => $detail) {
            
            // A. Buat record Balai
            $balai = Balai::firstOrCreate(
                ['nama_balai' => $namaBalai],
                [
                    'username' => strtolower(str_replace(' ', '_', $namaBalai)),
                    'password' => Hash::make('password123'),
                    'unker'    => $detail['unker'],
                    'unor'     => $detail['unor'],
                    'provinsi' => $detail['provinsi'],
                    'pulau'    => $detail['pulau'],
                    'kepala'   => $detail['kepala'],
                    'kontak'   => $detail['kontak'],
                ]
            );

            // B. Cari ID provinsi berdasarkan nama di array 'naungan'
            $provinsiIds = Provinsi::whereIn('nama', $detail['naungan'])->pluck('id')->toArray();

            // C. Pasangkan Balai dengan Provinsi menggunakan Sync 
            // (Sync menghindari data ganda di tabel pivot jika seeder dijalankan 2x)
            if (!empty($provinsiIds)) {
                $balai->provinsis()->sync($provinsiIds);
            }
        }
        
        $this->command->info('Skenario 2: Data Balai & Pivot Provinsi berhasil dibuat!');
    }
}