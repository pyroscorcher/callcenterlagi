<?php

namespace Database\Seeders;

use App\Models\Kelurahan;
use App\Models\LaporanMasyarakat;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class LaporanMasyarakatSeeder extends Seeder
{
    public function run(): void
    {
        $jenisBencana = [
            'Banjir',
            'Longsor',
            'Gempa Bumi',
            'Puting Beliung',
            'Kebakaran Hutan',
        ];

        $status = [
            'Menunggu Verifikasi',
            'Sedang Diproses',
            'Selesai',
        ];

        $dampak = [
            'Rumah Rusak',
            'Jalan Terputus',
            'Jembatan Rusak',
            'Sawah Terendam',
            'Listrik Padam',
        ];

        $infrastruktur = [
            'Jalan',
            'Jembatan',
            'Sekolah',
            'Puskesmas',
            'Irigasi',
        ];

        for ($i = 1; $i <= 20; $i++) {

            $kelurahan = Kelurahan::with(
                'kecamatan.kabupatenKota.provinsi'
            )->inRandomOrder()->first();

            if (!$kelurahan) {
                $this->command->warn('Kelurahan table is empty.');
                return;
            }

            $kecamatan = $kelurahan->kecamatan;
            $kabupaten = $kecamatan->kabupatenKota;
            $provinsi = $kabupaten->provinsi;

            LaporanMasyarakat::create([
                'pelapor' => "Pelapor {$i}",
                'telepon' => '0812345678' . str_pad($i, 2, '0', STR_PAD_LEFT),

                'jenis_bencana' => fake()->randomElement($jenisBencana),

                'nama_bencana' => fake()->randomElement([
                    'Banjir Bandang',
                    'Longsor Tebing',
                    'Gempa Dangkal',
                    'Angin Kencang',
                    'Luapan Sungai',
                ]),

                'dampak_bencana' => fake()->randomElement($dampak),

                'waktu_kejadian' => Carbon::now()
                    ->subDays(rand(1, 30))
                    ->format('Y-m-d H:i:s'),

                'wilayah_waktu' => 'WIB',

                'lokasi' => fake()->streetAddress(),

                'provinsi_id' => $provinsi->id,
                'kabupaten_kota_id' => $kabupaten->id,
                'kecamatan_id' => $kecamatan->id,
                'kelurahan_id' => $kelurahan->id,

                'lintang' => fake()->latitude(-11, 6),
                'bujur' => fake()->longitude(95, 141),

                'deskripsi' => fake()->paragraph(3),

                'infrastruktur_terdampak' => fake()->randomElement($infrastruktur),

                'status' => fake()->randomElement($status),

                'detail_status' => fake()->sentence(),

                'kebutuhan_mendesak' => fake()->randomElement([
                    'Logistik',
                    'Air Bersih',
                    'Tenda',
                    'Obat-obatan',
                    'Evakuasi',
                ]),

                'validasi' => fake()->boolean(70)
                    ? 'Valid'
                    : 'Belum Valid',

                'created_at' => Carbon::now()->subDays(rand(0, 15)),
                'updated_at' => now(),
            ]);
        }
    }
}