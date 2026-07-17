<?php

namespace App\Http\Controllers;

use App\Models\LaporanMasyarakat;
use Illuminate\Http\Request;

class WhatsappWebhookController extends Controller
{
    public function store(Request $request)
    {
        if ($request->input('token') !== config('services.whatsapp_bot.secret')) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'pelapor' => ['required', 'string', 'max:255'],
            'jenis_bencana' => ['required', 'string', 'max:255'],
            'nama_bencana' => ['required', 'string', 'max:255'],
            'dampak_bencana' => ['required', 'string', 'max:255'],
            'waktu_kejadian' => ['required', 'string', 'max:255'],
            'wilayah_waktu' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string', 'max:255'],
            'infrastruktur_terdampak' => ['required', 'string', 'max:255'],
            'kebutuhan_mendesak' => ['nullable', 'string', 'max:255'],
            // Optional — the bot only sends this when the reporter attaches a photo.
            'foto' => ['nullable', 'image', 'max:5120'], // 5MB
        ]);

        if ($request->hasFile('foto')) {
            // Stored under storage/app/public/laporan-foto — make sure you've run
            // `php artisan storage:link` so this is reachable at /storage/laporan-foto/...
            $validated['foto'] = $request->file('foto')->store('laporan-foto', 'public');
        }

        $laporan = LaporanMasyarakat::create([
            ...$validated,
            'status' => 'Baru',
        ]);

        return response()->json([
            'message' => 'Laporan berhasil disimpan',
            'id' => $laporan->id,
        ], 201);
    }
}