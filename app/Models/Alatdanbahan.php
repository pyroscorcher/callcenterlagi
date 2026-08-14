<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlatDanBahan extends Model
{
    use HasFactory;

    protected $table = 'alat_dan_bahan';

    protected $fillable = [
        // Exactly one of these two should be set: an Alat/Bahan row either
        // belongs to a specific Penanganan Sementara entry, or -- if the
        // Sumberdaya section is filled in as a flat, report-level list with
        // no specific entry selected -- it hangs directly off LaporanBalai.
        'penanganan_sementara_id',
        'laporan_balai_id',
        'kategori',
        'kelas',
        'model',
        'jumlah',
    ];

    protected $casts = [
        'jumlah' => 'integer',
    ];

    public function penangananSementara(): BelongsTo
    {
        return $this->belongsTo(PenangananSementara::class);
    }

    public function laporanBalai(): BelongsTo
    {
        return $this->belongsTo(LaporanBalai::class);
    }
}