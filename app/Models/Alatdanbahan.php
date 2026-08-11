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
        'penanganan_sementara_id',
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
}