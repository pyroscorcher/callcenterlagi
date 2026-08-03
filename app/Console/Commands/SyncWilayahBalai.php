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

        $this->info('Step 1: Generating SDA Balai Placeholders...');

        // 1. Extract unique Balai names (pengelola) from PosDugaAir
        $pengelolas = DB::table('pos_duga_airs')
            ->select('pengelola')
            ->whereNotNull('pengelola')
            ->distinct()
            ->pluck('pengelola');

        $createdCount = 0;

        foreach ($pengelolas as $pengelola) {
            // firstOrCreate prevents duplicating Balais if you run the command twice
            $balai = Balai::firstOrCreate(
                ['nama_balai' => $pengelola], // Search by this
                [
                    // If not found, create it with these placeholders
                    'username' => Str::slug($pengelola, '_'), 
                    'password' => 'password', // Your model automatically hashes this
                    'unor' => 'SDA', // Tagging them so you know they are SDA
                ]
            );

            if ($balai->wasRecentlyCreated) {
                $createdCount++;
            }
        }

        $this->info("Created {$createdCount} new SDA Balai placeholders.");


        $this->info('Step 2: Mapping hierarchical geographic links...');

        // 2. Map the geography using the raw, highly-optimized query
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
            
            -- Match the Balai Entity we just created
            JOIN balais b ON b.nama_balai = p.pengelola
            
            -- Traverse the geographical hierarchy to ensure exact location matching
            JOIN provinsis prov ON prov.nama = p.provinsi
            JOIN kabupaten_kotas kab ON kab.provinsi_id = prov.id AND kab.nama = p.kota_kabupaten
            JOIN kecamatans kec ON kec.kabupaten_kota_id = kab.id AND kec.nama = p.kecamatan
            JOIN kelurahans kel ON kel.kecamatan_id = kec.id AND kel.nama = p.kelurahan
            
            WHERE p.pengelola IS NOT NULL
        ";

        try {
            DB::statement($query);
            $this->info('Step 2 Complete: All hierarchical IDs have been successfully linked.');
            $this->info('Process finished!');
        } catch (\Exception $e) {
            $this->error('An error occurred during synchronization: ' . $e->getMessage());
        }
    }
}