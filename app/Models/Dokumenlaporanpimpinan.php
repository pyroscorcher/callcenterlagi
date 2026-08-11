<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DokumenLaporanPimpinan extends Model
{
    use HasFactory;

    protected $table = 'dokumen_laporan_pimpinan';

    protected $fillable = [
        'laporan_balai_id',
        'nama_dokumen',
        'file_path',
        'deskripsi',
    ];

    public function laporanBalai(): BelongsTo
    {
        return $this->belongsTo(LaporanBalai::class);
    }
}