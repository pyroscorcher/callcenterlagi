<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenangananPermanenFoto extends Model
{
    use HasFactory;

    protected $table = 'penanganan_permanen_foto';

    protected $fillable = [
        'penanganan_permanen_id',
        'foto',
        'latitude',
        'longitude',
        'keterangan',
    ];

    public function penangananPermanen(): BelongsTo
    {
        return $this->belongsTo(PenangananPermanen::class);
    }
}