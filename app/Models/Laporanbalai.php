<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LaporanBalai extends Model
{
    use HasFactory;

    protected $table = 'laporan_balai';

    protected $fillable = [
        'laporan_masyarakat_id',
        'balai_id',
        'created_by',
        'status',
        'status_terkini',
        'tanggal_respon',
        'catatan',
    ];

    protected $casts = [
        'tanggal_respon' => 'date',
    ];

    public function laporanMasyarakat(): BelongsTo
    {
        return $this->belongsTo(LaporanMasyarakat::class);
    }

    public function balai(): BelongsTo
    {
        return $this->belongsTo(Balai::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function kewenangan(): HasOne
    {
        return $this->hasOne(KewenanganInfrastruktur::class);
    }

    public function infrastrukturTerdampak(): HasMany
    {
        return $this->hasMany(InfrastrukturTerdampak::class);
    }

    public function penangananSementara(): HasMany
    {
        return $this->hasMany(PenangananSementara::class);
    }

    public function penangananPermanen(): HasMany
    {
        return $this->hasMany(PenangananPermanen::class);
    }

    /**
     * Report-level Sumberdaya (Alat & Bahan) rows -- ones filled in without
     * being tied to a specific Penanganan Sementara entry. Entry-specific
     * rows are still reached via penangananSementara()->alatDanBahan().
     */
    public function alatDanBahan(): HasMany
    {
        return $this->hasMany(AlatDanBahan::class);
    }

    public function dokumenLaporanPimpinan(): HasMany
    {
        return $this->hasMany(DokumenLaporanPimpinan::class);
    }

    public function picBencanas(): HasMany
    {
        return $this->hasMany(PicBencana::class, 'laporan_balai_id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LaporanBalaiLog::class, 'laporan_id');
    }
}