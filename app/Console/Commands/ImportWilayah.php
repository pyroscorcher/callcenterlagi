<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class ImportWilayah extends Command
{
    protected $signature = 'wilayah:import
        {path=database/data/wilayah.sql : Path to the wilayah.sql dump}
        {--keep-raw : Keep the staging "wilayah" table after import instead of dropping it}';

    protected $description = 'Import wilayah.sql (flat Kemendagri code dump) and split it into provinsis, kabupaten_kotas, kecamatans, and kelurahans';

    public function handle(): int
    {
        $path = base_path($this->argument('path'));

        if (! File::exists($path)) {
            $this->error("File not found: {$path}");
            return self::FAILURE;
        }

        $this->info('Loading raw dump into staging table "wilayah"...');

        $this->info('Normalizing staging table collation to match Laravel...');
        DB::statement('ALTER TABLE wilayah CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;');

        // The dump defines its own DROP TABLE IF EXISTS / CREATE TABLE for
        // "wilayah", so running it as-is creates and populates the staging
        // table in one shot — no need for a separate migration for it.
        DB::unprepared(File::get($path));

        $this->info('Splitting into provinsis...');
        DB::statement("
            INSERT INTO provinsis (kode, nama, created_at, updated_at)
            SELECT kode, nama, NOW(), NOW()
            FROM wilayah
            WHERE kode NOT LIKE '%.%'
        ");

        $this->info('Splitting into kabupaten_kotas...');
        DB::statement("
            INSERT INTO kabupaten_kotas (kode, nama, provinsi_id, created_at, updated_at)
            SELECT w.kode, w.nama, p.id, NOW(), NOW()
            FROM wilayah w
            JOIN provinsis p ON p.kode = SUBSTRING_INDEX(w.kode, '.', 1)
            WHERE (LENGTH(w.kode) - LENGTH(REPLACE(w.kode, '.', ''))) = 1
        ");

        $this->info('Splitting into kecamatans...');
        DB::statement("
            INSERT INTO kecamatans (kode, nama, kabupaten_kota_id, created_at, updated_at)
            SELECT w.kode, w.nama, k.id, NOW(), NOW()
            FROM wilayah w
            JOIN kabupaten_kotas k ON k.kode = SUBSTRING_INDEX(w.kode, '.', 2)
            WHERE (LENGTH(w.kode) - LENGTH(REPLACE(w.kode, '.', ''))) = 2
        ");

        $this->info('Splitting into kelurahans...');
        DB::statement("
            INSERT INTO kelurahans (kode, nama, kecamatan_id, created_at, updated_at)
            SELECT w.kode, w.nama, kc.id, NOW(), NOW()
            FROM wilayah w
            JOIN kecamatans kc ON kc.kode = SUBSTRING_INDEX(w.kode, '.', 3)
            WHERE (LENGTH(w.kode) - LENGTH(REPLACE(w.kode, '.', ''))) = 3
        ");

        if (! $this->option('keep-raw')) {
            $this->info('Dropping staging table "wilayah"...');
            DB::statement('DROP TABLE IF EXISTS wilayah');
        }

        $this->info('Done. Row counts:');
        $this->table(
            ['Table', 'Rows'],
            [
                ['provinsis', DB::table('provinsis')->count()],
                ['kabupaten_kotas', DB::table('kabupaten_kotas')->count()],
                ['kecamatans', DB::table('kecamatans')->count()],
                ['kelurahans', DB::table('kelurahans')->count()],
            ]
        );

        return self::SUCCESS;
    }
}