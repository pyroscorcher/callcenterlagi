<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosDugaAir extends Model
{
    protected $fillable = [
        'external_id',
        'objectid',
        'nama_hidrologi',
        'daerah_aliran_sungai',
        'wilayah_sungai',
        'kecamatan',
        'kelurahan',
        'kota_kabupaten',
        'provinsi',
        'pengelola',
        'kode',
        'kode_balai',
        'kode_daerah_aliran_sungai',
        'kode_daerah_aliran_sungai_lain',
        'kode_wilayah_sungai',
        'kode_wilayah_sungai_lain',
        'teknis_jenis_pos',
        'tipe_hidrologi',
        'latitude',
        'longitude',
        'urut',
        'source_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'source_updated_at' => 'datetime',
        ];
    }
}