<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KewenanganInfrastruktur extends Model
{
    use HasFactory;

    protected $table = 'kewenangan_infrastruktur';

    public const TIPE_BALAI = 'balai';
    public const TIPE_DELEGASI = 'delegasi';

    protected $fillable = [
        'laporan_balai_id', // <-- Changed from infrastruktur_terdampak_id
        'tipe',
        // Type 1: Balai
        'balai_id',
        'unor',
        'kepala',
        'kontak',
        // Type 2: Delegasi
        'das',
        'pch',
        'ruas_jalan',
        'instansi',
        'penanggung_jawab',
        'telepon',
    ];

    public function laporanBalai(): BelongsTo
    {
        return $this->belongsTo(LaporanBalai::class);
    }

    public function balai(): BelongsTo
    {
        return $this->belongsTo(Balai::class);
    }

    public static function forBalai(LaporanBalai $laporanBalai, Balai $balai): self
    {
        return static::create([
            'laporan_balai_id' => $laporanBalai->id,
            'tipe' => self::TIPE_BALAI,
            'balai_id' => $balai->id,
            'unor' => $balai->unor,
            'kepala' => $balai->kepala,
            'kontak' => $balai->kontak,
        ]);
    }

    public static function forDelegasi(LaporanBalai $laporanBalai, array $data): self
    {
        return static::create(array_merge($data, [
            'laporan_balai_id' => $laporanBalai->id,
            'tipe' => self::TIPE_DELEGASI,
        ]));
    }

    public function isBalai(): bool
    {
        return $this->tipe === self::TIPE_BALAI;
    }

    public function isDelegasi(): bool
    {
        return $this->tipe === self::TIPE_DELEGASI;
    }
}