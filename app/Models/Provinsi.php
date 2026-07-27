<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provinsi extends Model
{
    // Hapus 'balai_id' dari fillable
    protected $fillable = ['kode', 'nama']; 

    // RELASI MANY-TO-MANY KE BALAI
    public function balais()
    {
        return $this->belongsToMany(Balai::class, 'wilayah_balai', 'provinsi_id', 'balai_id');
    }
}