<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WhatsappWebhookController extends Controller
{
    private const WILAYAH_OFFSETS = [
        'WIB' => 7,
        'WITA' => 8,
        'WIT' => 9,
    ];

    public function store(Request $request)
    {
        if ($request->input('token') !== config('services.whatsapp_bot.secret')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'pelapor' => ['required', 'string', 'max:255'],
            'telepon' => ['required', 'string', 'max:255'],
            'jenis_bencana' => ['required', 'string', 'max:255'],
            'nama_bencana' => ['required', 'string', 'max:255'],
            'dampak_bencana' => ['required', 'string', 'max:255'],
            'waktu_kejadian' => ['required', 'string', 'max:255'],
            'wilayah_waktu' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'infrastruktur_terdampak' => ['required', 'string', 'max:255'],
            'kebutuhan_mendesak' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'max:5120'], // 5MB
        ]);

        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('laporan-foto', 'public');
        }

        // The bot's server clock isn't necessarily in Indonesian time, so
        // rather than trust the server's own "now", we build the timestamp
        // from the reporter's chosen wilayah_waktu (WIB/WITA/WIT).
        //
        // Caveat: this stores a "wall clock" local time as created_at rather
        // than a true UTC instant — fine for display purposes on this
        // dashboard, but if you ever need to reliably sort/compare reports
        // chronologically across different wilayah_waktu values, a true UTC
        // timestamp plus a separate display-timezone conversion would be more
        // correct. This matches what was asked for, just flagging the tradeoff.
        $offset = self::WILAYAH_OFFSETS[$validated['wilayah_waktu']] ?? self::WILAYAH_OFFSETS['WIB'];
        $localTimestamp = Carbon::now('UTC')->addHours($offset);

        $laporan = new LaporanMasyarakat([
            ...$validated,
            'status' => 'Baru',
        ]);

        $laporan->timestamps = false;
        $laporan->created_at = $localTimestamp;
        $laporan->updated_at = $localTimestamp;
        $laporan->save();

        return response()->json([
            'message' => 'Laporan berhasil disimpan',
            'id' => $laporan->id,
        ], 201);
    }
}