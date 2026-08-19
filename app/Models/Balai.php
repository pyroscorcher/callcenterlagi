<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Balai extends Model
{
    protected $fillable = [
        'nama_balai', 'unker', 'unor',
        'provinsi', 'pulau', 'kepala', 'kontak',
    ];

    public function provinsis()
    {
        return $this->belongsToMany(Provinsi::class, 'wilayah_balai', 'balai_id', 'provinsi_id');
    }

    public function laporanMasyarakats()
    {
        return $this->belongsToMany(LaporanMasyarakat::class, 'laporan_balai')->withTimestamps();
    }

    // Kept the same method name so anything still calling $balai->pics doesn't break —
    // it now points at users with role=pic instead of the old pics table.
    public function pics()
    {
        return $this->hasMany(User::class)->where('role', 'pic');
    }

    public function picUsers()
    {
        return $this->hasMany(User::class, 'balai_id')->where('role', 'pic');
    }

}