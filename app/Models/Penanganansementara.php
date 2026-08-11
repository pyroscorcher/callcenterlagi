<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenangananSementara extends Model
{
    use HasFactory;

    protected $table = 'penanganan_sementara';

    protected $fillable = [
        'laporan_balai_id',
        'tanggal',
        'kewenangan',
        'balai_id',
        'keterangan',
        'jumlah_personil',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah_personil' => 'integer',
    ];

    public function laporanBalai(): BelongsTo
    {
        return $this->belongsTo(LaporanBalai::class);
    }

    public function balai(): BelongsTo
    {
        return $this->belongsTo(Balai::class);
    }

    public function foto(): HasMany
    {
        return $this->hasMany(PenangananSementaraFoto::class);
    }

    public function alatDanBahan(): HasMany
    {
        return $this->hasMany(AlatDanBahan::class);
    }
}