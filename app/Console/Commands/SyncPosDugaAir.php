<?php

namespace App\Console\Commands;

use App\Models\PosDugaAir;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class SyncPosDugaAir extends Command
{
    protected $signature = 'pos-duga-air:sync';

    protected $description = 'Fetch Pos Duga Air station metadata from the SIGI GeoAPI and upsert into the database';

    private const BASE_URL = 'https://sigi.pu.go.id/geoapi/api/v1/pos_duga_air/data';
    private const PAGE_SIZE = 100;

    public function handle(): int
    {
        $email = config('services.sigi.email');
        $token = config('services.sigi.token');

        if (! $email || ! $token) {
            $this->error('SIGI_API_EMAIL / SIGI_API_TOKEN are not set in .env.');
            return self::FAILURE;
        }

        $offset = 0;
        $totalSynced = 0;
        $totalRecords = null;

        do {
            $response = Http::get(self::BASE_URL, [
                'filter' => '1=1',
                'fields' => '*',
                'format' => 'json',
                'geometry' => 'true',
                'offset' => $offset,
                'limit' => self::PAGE_SIZE,
                'email' => $email,
                'token' => $token,
            ]);

            if ($response->failed()) {
                $this->error("Request failed at offset {$offset}: HTTP {$response->status()}");
                return self::FAILURE;
            }

            $data = $response->json();
            $totalRecords ??= $data['totalRecords'] ?? 0;
            $features = $data['features'] ?? [];

            foreach ($features as $feature) {
                $attributes = $feature['attributes'] ?? [];

                if (empty($attributes['id'])) {
                    continue; // skip anything without the identifier we upsert on
                }

                PosDugaAir::updateOrCreate(
                    ['external_id' => $attributes['id']],
                    [
                        'objectid' => $attributes['objectid'] ?? null,
                        'nama_hidrologi' => $attributes['nama_hidrologi'] ?? null,
                        'daerah_aliran_sungai' => $attributes['daerah_aliran_sungai'] ?? null,
                        'wilayah_sungai' => $attributes['wilayah_sungai'] ?? null,
                        'kecamatan' => $attributes['kecamatan'] ?? null,
                        'kelurahan' => $attributes['kelurahan'] ?? null,
                        'kota_kabupaten' => $attributes['kota_kabupaten'] ?? null,
                        'provinsi' => $attributes['provinsi'] ?? null,
                        'pengelola' => $attributes['pengelola'] ?? null,
                        'kode' => $attributes['kode'] ?? null,
                        'kode_balai' => $attributes['kode_balai'] ?? null,
                        'kode_daerah_aliran_sungai' => $attributes['kode_daerah_aliran_sungai'] ?? null,
                        'kode_daerah_aliran_sungai_lain' => $attributes['kode_daerah_aliran_sungai_lain'] ?? null,
                        'kode_wilayah_sungai' => $attributes['kode_wilayah_sungai'] ?? null,
                        'kode_wilayah_sungai_lain' => $attributes['kode_wilayah_sungai_lain'] ?? null,
                        'teknis_jenis_pos' => $attributes['teknis_jenis_pos'] ?? null,
                        'tipe_hidrologi' => $attributes['tipe_hidrologi'] ?? null,
                        'latitude' => $attributes['latitude'] ?? null,
                        'longitude' => $attributes['longitude'] ?? null,
                        'urut' => $attributes['urut'] ?? null,
                        'source_updated_at' => $attributes['updated_at'] ?? null,
                    ]
                );

                $totalSynced++;
            }

            $this->info("Synced offset {$offset}: " . count($features) . ' records');

            $offset += self::PAGE_SIZE;
        } while ($offset < $totalRecords);

        $this->info("Done. Synced {$totalSynced} of {$totalRecords} total records.");

        return self::SUCCESS;
    }
}