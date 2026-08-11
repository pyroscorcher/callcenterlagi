<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class InfrastrukturTerdampak extends Model
{
    use HasFactory;

    protected $table = 'infrastruktur_terdampak';

    protected $fillable = [
        'laporan_balai_id',
        'unor',
        'kategori',
        'nama',
        'satuan',
        'jumlah',
        'detail',
        'dokumentasi',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function laporanBalai(): BelongsTo
    {
        return $this->belongsTo(LaporanBalai::class);
    }
}