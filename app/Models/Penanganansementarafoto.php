<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenangananSementaraFoto extends Model
{
    use HasFactory;

    protected $table = 'penanganan_sementara_foto';

    protected $fillable = [
        'penanganan_sementara_id',
        'foto',
        'latitude',
        'longitude',
        'keterangan',
    ];

    public function penangananSementara(): BelongsTo
    {
        return $this->belongsTo(PenangananSementara::class);
    }
}