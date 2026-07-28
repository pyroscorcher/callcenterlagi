<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Balai extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username', 'password', 'nama_balai', 'unker', 'unor', 
        'provinsi', 'pulau', 'kepala', 'kontak',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    // RELASI MANY-TO-MANY KE PROVINSI
    public function provinsis()
    {
        return $this->belongsToMany(Provinsi::class, 'wilayah_balai', 'balai_id', 'provinsi_id');
    }
    public function laporanMasyarakats()
    {
        return $this->belongsToMany(LaporanMasyarakat::class, 'laporan_balai')->withTimestamps();
    }
}