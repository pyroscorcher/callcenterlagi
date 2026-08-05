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
        'provinsi_id',
        'kabupaten_kota_id',
        'kecamatan_id',
        'kelurahan_id',
        'lintang',
        'bujur',
        'deskripsi',
        'infrastruktur_terdampak',
        'status',
        'detail_status',
        'kebutuhan_mendesak',
        'validasi',
        'verifikasi',
    ];


    public function fotos()
    {
        return $this->hasMany(Foto::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupatenKota()
    {
        return $this->belongsTo(KabupatenKota::class);
    }

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }

    public function kelurahan()
    {
        return $this->belongsTo(Kelurahan::class);
    }

    public function balais()
    {
        return $this->belongsToMany(Balai::class, 'balai_laporan', 'laporan_id', 'balai_id');
    }
}