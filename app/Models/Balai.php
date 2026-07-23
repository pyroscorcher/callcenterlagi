<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Balai extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'username',
        'password',
        'nama_balai',
        'unker',
        'unor',
        'provinsi',
        'pulau',
        'kepala',
        'kontak',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}