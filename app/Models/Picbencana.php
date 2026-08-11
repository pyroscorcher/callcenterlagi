<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PicBencana extends Model
{
    use HasFactory;

    protected $table = 'picbencana';

    protected $fillable = [
        'laporan_balai_id',
        // Registered-user PIC — nama_pic/kontak/balai_id are snapshots
        // taken at assignment time (see forUser() below), same pattern
        // as KewenanganInfrastruktur's Type 1. They do NOT stay in sync
        // if the user's profile changes later.
        'user_id',
        'nama_pic',
        'kontak',
        'balai_id',
        // Escape hatch when the PIC isn't a registered user at all
        'pic_lainnya',
    ];

    public function laporanBalai(): BelongsTo
    {
        return $this->belongsTo(LaporanBalai::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function balai(): BelongsTo
    {
        return $this->belongsTo(Balai::class);
    }

    /**
     * Assign a registered user as PIC, snapshotting nama_pic/kontak/balai_id
     * from that user's profile at the moment of assignment.
     */
    public static function forUser(LaporanBalai $laporanBalai, User $user): self
    {
        return static::create([
            'laporan_balai_id' => $laporanBalai->id,
            'user_id' => $user->id,
            'nama_pic' => $user->name,
            'kontak' => $user->kontak, // adjust to the actual column name on users
            'balai_id' => $user->balai_id, // adjust if the relation is named differently
        ]);
    }

    /**
     * Assign a PIC who isn't a registered user (free-text name only).
     */
    public static function forPicLainnya(LaporanBalai $laporanBalai, string $namaPic): self
    {
        return static::create([
            'laporan_balai_id' => $laporanBalai->id,
            'pic_lainnya' => $namaPic,
        ]);
    }

    public function isExternalPic(): bool
    {
        return is_null($this->user_id) && ! empty($this->pic_lainnya);
    }
}