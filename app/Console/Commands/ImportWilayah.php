<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportWilayah extends Command
{
    protected $signature = 'wilayah:import {path=database/data/wilayah.sql} {--keep-raw}';
    protected $description = 'Import data wilayah Kemendagri dan pecah menjadi relasi hierarkis';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (!File::exists($path)) {
            $this->error("File tidak ditemukan: {$path}");
            return self::FAILURE;
        }

        $this->info('1. Memuat raw dump ke tabel staging "wilayah"...');
        DB::unprepared(File::get($path));

        // FIX COLLATION ERROR DI SINI
        $this->info('2. Menyesuaikan Collation tabel staging dengan Laravel...');
        DB::statement('ALTER TABLE wilayah CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');

        $this->info('3. Memecah dan memasukkan data ke tabel provinsis...');
        // Hapus data lama agar tidak duplikat jika command dijalankan ulang
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('kelurahans')->truncate();
        DB::table('kecamatans')->truncate();
        DB::table('kabupaten_kotas')->truncate();
        DB::table('provinsis')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::statement("
            INSERT INTO provinsis (kode, nama, created_at, updated_at)
            SELECT kode, nama, NOW(), NOW()
            FROM wilayah
            WHERE kode NOT LIKE '%.%'
        ");

        $this->info('4. Memecah ke tabel kabupaten_kotas (Relasi ke Provinsi)...');
        DB::statement("
            INSERT INTO kabupaten_kotas (kode, nama, provinsi_id, created_at, updated_at)
            SELECT w.kode, w.nama, p.id, NOW(), NOW()
            FROM wilayah w
            JOIN provinsis p ON p.kode = SUBSTRING_INDEX(w.kode, '.', 1)
            WHERE (LENGTH(w.kode) - LENGTH(REPLACE(w.kode, '.', ''))) = 1
        ");

        $this->info('5. Memecah ke tabel kecamatans (Relasi ke Kabupaten)...');
        DB::statement("
            INSERT INTO kecamatans (kode, nama, kabupaten_kota_id, created_at, updated_at)
            SELECT w.kode, w.nama, k.id, NOW(), NOW()
            FROM wilayah w
            JOIN kabupaten_kotas k ON k.kode = SUBSTRING_INDEX(w.kode, '.', 2)
            WHERE (LENGTH(w.kode) - LENGTH(REPLACE(w.kode, '.', ''))) = 2
        ");

        $this->info('6. Memecah ke tabel kelurahans (Relasi ke Kecamatan)...');
        DB::statement("
            INSERT INTO kelurahans (kode, nama, kecamatan_id, created_at, updated_at)
            SELECT w.kode, w.nama, kc.id, NOW(), NOW()
            FROM wilayah w
            JOIN kecamatans kc ON kc.kode = SUBSTRING_INDEX(w.kode, '.', 3)
            WHERE (LENGTH(w.kode) - LENGTH(REPLACE(w.kode, '.', ''))) = 3
        ");

        if (!$this->option('keep-raw')) {
            $this->info('7. Menghapus tabel staging "wilayah"...');
            DB::statement('DROP TABLE IF EXISTS wilayah');
        }

        $this->info('Selesai! Jumlah data yang berhasil dipetakan:');
        $this->table(
            ['Tabel', 'Jumlah Baris'],
            [
                ['Provinsi', DB::table('provinsis')->count()],
                ['Kabupaten/Kota', DB::table('kabupaten_kotas')->count()],
                ['Kecamatan', DB::table('kecamatans')->count()],
                ['Kelurahan/Desa', DB::table('kelurahans')->count()],
            ]
        );

        return self::SUCCESS;
    }
}