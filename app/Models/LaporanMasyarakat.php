<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanMasyarakat extends Model
{
    use HasFactory;

    protected $fillable = [
        'pelapor',
        'telepon',
        'jenis_bencana',
        'nama_bencana',
        'dampak_bencana',
        'waktu_kejadian',
        'wilayah_waktu',
        'lokasi',
        'lintang',
        'bujur',
        'deskripsi',
        'infrastruktur_terdampak',
        'status',
        'detail_status',
        'kebutuhan_mendesak',
        'foto',
    ];
}