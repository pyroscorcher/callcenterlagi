<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foto extends Model
{
    use HasFactory;

    protected $fillable = [
        'laporan_masyarakat_id',
        'file_path',
    ];

    /**
     * Relasi Inverse One-to-Many ke LaporanMasyarakat
     */
    public function laporanMasyarakat()
    {
        return $this->belongsTo(LaporanMasyarakat::class);
    }
}