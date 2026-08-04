<?php

namespace App\Console\Commands;

use App\Models\Balai;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SyncWilayahBalai extends Command
{
    protected $signature = 'sync:wilayah-balai';
    protected $description = 'Create SDA Balai placeholders from PosDugaAir and map them down to Kelurahan';

    public function handle()
    {
        if (!Schema::hasTable('pos_duga_airs')) {
            $this->error('The pos_duga_airs table does not exist. Cannot sync.');
            return;
        }

        $this->info('Step 1: Generating & Updating SDA Balai Placeholders...');

        // 1. Extract ALL unique Balai names safely
        $pengelolas = DB::table('pos_duga_airs')
            ->whereNotNull('pengelola')
            ->distinct()
            ->pluck('pengelola');

        $updatedCount = 0;

        foreach ($pengelolas as $pengelola) {
            // Find the most frequent province strictly for THIS Balai
            $mostFrequentProvinsi = DB::table('pos_duga_airs')
                ->where('pengelola', $pengelola)
                ->whereNotNull('provinsi')
                ->groupBy('provinsi')
                ->orderByRaw('COUNT(*) DESC')
                ->value('provinsi');

            // Use updateOrCreate so it updates existing Balais with the correct Provinsi
            $balai = Balai::updateOrCreate(
                ['nama_balai' => $pengelola], // Search by this
                [
                    'username' => Str::slug($pengelola, '_'), 
                    'password' => 'password',
                    'unor' => 'SDA',
                    'provinsi' => $mostFrequentProvinsi, // This will now update!
                ]
            );
            $updatedCount++;
        }

        $this->info("Processed and updated {$updatedCount} SDA Balai placeholders.");


        $this->info('Step 2: Mapping hierarchical geographic links...');

        // Clear out the junction table first to ensure a perfectly clean sync
        DB::table('wilayah_balai')->truncate();

        // 2. The mapping query
        $query = "
            INSERT IGNORE INTO wilayah_balai (balai_id, provinsi_id, kabupaten_kota_id, kecamatan_id, kelurahan_id, created_at, updated_at)
            SELECT DISTINCT 
                b.id AS balai_id,
                prov.id AS provinsi_id,
                kab.id AS kabupaten_kota_id,
                kec.id AS kecamatan_id,
                kel.id AS kelurahan_id,
                NOW(),
                NOW()
            FROM pos_duga_airs p
            
            JOIN balais b ON b.nama_balai = p.pengelola
            JOIN provinsis prov ON prov.nama = p.provinsi
            JOIN kabupaten_kotas kab ON kab.provinsi_id = prov.id AND kab.nama = p.kota_kabupaten
            JOIN kecamatans kec ON kec.kabupaten_kota_id = kab.id AND kec.nama = p.kecamatan
            JOIN kelurahans kel ON kel.kecamatan_id = kec.id AND kel.nama = p.kelurahan
            
            WHERE p.pengelola IS NOT NULL
        ";

        try {
            DB::statement($query);
            $this->info('Step 2 Complete: All hierarchical IDs have been successfully linked.');
            
            // Output the final count so we know exactly how many rows mapped
            $totalLinks = DB::table('wilayah_balai')->count();
            $this->info("SUCCESS! Total rows in wilayah_balai table: {$totalLinks}");
            
        } catch (\Exception $e) {
            $this->error('An error occurred during synchronization: ' . $e->getMessage());
        }
    }
}