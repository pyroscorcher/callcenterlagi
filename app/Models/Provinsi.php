<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    protected $fillable = ['kode', 'nama', 'balai_id'];

    public function balai()
    {
        return $this->belongsTo(Balai::class, 'balai_id');
    }
}