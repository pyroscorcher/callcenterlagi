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
        'status_terkini', // <-- Tambahkan ini
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

    public function dokumenLaporanPimpinan(): HasMany
    {
        return $this->hasMany(DokumenLaporanPimpinan::class);
    }

    public function picBencanas()
    {
        return $this->hasMany(PicBencana::class, 'laporan_balai_id');
    }
    public function logs()
    {
        return $this->hasMany(LaporanBalaiLog::class, 'laporan_id');
    }
}