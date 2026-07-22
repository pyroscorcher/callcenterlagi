<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaporanMasyarakatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Uses DB::table()->insertGetId() to preserve exact timestamps
     * while also retrieving the generated ID to link with the new 
     * 'fotos' table relation.
     */
    public function run(): void
    {
        // --- LAPORAN 1 ---
        $laporan1Id = DB::table('laporan_masyarakats')->insertGetId([
            'created_at' => '2026-07-22 01:22:49',
            'updated_at' => '2026-07-22 01:22:49',
            'pelapor' => 'Adinda',
            'telepon' => '08960146284',
            'jenis_bencana' => 'Tanah Longsor',
            'nama_bencana' => 'Longsor',
            'dampak_bencana' => 'Kerusakan Jalanan dan Jembatan',
            'waktu_kejadian' => '16.00 12 Maret 2025',
            'wilayah_waktu' => 'WIB',
            'lokasi' => 'JAWA BARAT DEPOK SAWANGAN PENGASINAN',
            'lintang' => null,
            'bujur' => null,
            'deskripsi' => 'terjadi longsor di pinggir kali yg membuat jalan hancur setengah',
            'infrastruktur_terdampak' => 'Jalan Umum Sawangan Raya',
            'status' => 'Baru',
            'detail_status' => null,
            'kebutuhan_mendesak' => 'Perbaikan Jalan secepatnya, atau Marka untuk menandakan adanya jalan yg rusak',
        ]);

        // Insert foto untuk laporan 1
        DB::table('fotos')->insert([
            'laporan_masyarakat_id' => $laporan1Id,
            'file_path' => 'laporan-foto/WdzFQLuswBOlhMeHcd18MTmCQsl6EjG5spYH8N9y.jpg',
            'created_at' => '2026-07-22 01:22:49',
            'updated_at' => '2026-07-22 01:22:49',
        ]);


        // --- LAPORAN 2 ---
        $laporan2Id = DB::table('laporan_masyarakats')->insertGetId([
            'created_at' => '2026-07-22 01:29:29',
            'updated_at' => '2026-07-22 01:29:29',
            'pelapor' => 'Rumyah',
            'telepon' => '089606278191',
            'jenis_bencana' => 'Gagal Teknologi',
            'nama_bencana' => 'Kegagalan Industri',
            'dampak_bencana' => 'Kerusakan SDA, Kerusakan Pemukiman, Kerusakan Jalanan dan Jembatan',
            'waktu_kejadian' => '16.00 15 April 2026',
            'wilayah_waktu' => 'WIB',
            'lokasi' => 'TANGGERANG SELATAN PAMULANG SUKARAJA',
            'lintang' => null,
            'bujur' => null,
            'deskripsi' => 'ada bangunan yang kepingan hancurannya berserakan dijalan',
            'infrastruktur_terdampak' => 'Jalan Umum',
            'status' => 'Baru',
            'detail_status' => null,
            'kebutuhan_mendesak' => 'pembersihan jalan',
        ]);

        // Insert foto untuk laporan 2
        DB::table('fotos')->insert([
            'laporan_masyarakat_id' => $laporan2Id,
            'file_path' => 'laporan-foto/pIzGSb2cNbktDPpehW9Jeq152XlBRuLUT68cMVqj.jpg',
            'created_at' => '2026-07-22 01:29:29',
            'updated_at' => '2026-07-22 01:29:29',
        ]);
    }
}