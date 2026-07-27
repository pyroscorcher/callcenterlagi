<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    protected $fillable = ['kode', 'nama', 'kecamatan_id'];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class);
    }
}