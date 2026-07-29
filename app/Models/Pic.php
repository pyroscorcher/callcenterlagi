<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pic extends Model
{
    protected $fillable = ['balai_id', 'nama', 'kontak'];

    public function balai()
    {
        return $this->belongsTo(Balai::class);
    }
}