<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PenangananPermanen extends Model
{
    use HasFactory;

    protected $table = 'penanganan_permanen';

    protected $fillable = [
        'laporan_balai_id',
        'tanggal',
        'kewenangan',
        'balai_id',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
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
        return $this->hasMany(PenangananPermanenFoto::class);
    }
}