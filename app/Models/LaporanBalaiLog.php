<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanBalaiLog extends Model
{
    protected $fillable = [
        'laporan_id',
        'user_id',
        'action',
        'nama_balai',
        'kepala_balai',
    ];

    public function laporan()
    {
        // Sesuaikan dengan nama class Model laporan Anda
        return $this->belongsTo(LaporanMasyarakat::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}