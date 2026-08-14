<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\LaporanMasyarakat;

class WhatsappNotifier
{
    /**
     * Sends a WhatsApp message to the given phone number via the bot's
     * outbound /send-message endpoint.
     */
    public function send(string $telepon, string $message): bool
    {
        try {
            $response = Http::timeout(10)->post(
                rtrim(config('services.whatsapp_bot.url'), '/') . '/send-message',
                [
                    'token' => config('services.whatsapp_bot.secret'),
                    'telepon' => $telepon,
                    'message' => $message,
                ]
            );

            if ($response->failed()) {
                Log::warning('Gagal mengirim pesan WhatsApp', [
                    'telepon' => $telepon,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Error saat mengirim pesan WhatsApp: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Sends the SAME message to a list of phone numbers.
     */
    public function sendBlast(array $teleponList, string $message): ?string
    {
        try {
            $response = Http::timeout(15)->post(
                rtrim(config('services.whatsapp_bot.url'), '/') . '/send-blast',
                [
                    'token' => config('services.whatsapp_bot.secret'),
                    'telepon' => $teleponList,
                    'message' => $message,
                ]
            );

            if ($response->failed()) {
                Log::warning('Gagal mengirim blast WhatsApp', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json('blast_id');
        } catch (\Throwable $e) {
            Log::error('Error saat mengirim blast WhatsApp: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Sends a CUSTOM message to a list of phone numbers.
     * Expects an array of arrays: [['telepon' => '08..', 'message' => '...']]
     */
    public function sendCustomBlast(array $recipients): ?string
    {
        try {
            $response = Http::timeout(15)->post(
                rtrim(config('services.whatsapp_bot.url'), '/') . '/send-blast',
                [
                    'token' => config('services.whatsapp_bot.secret'),
                    'recipients' => $recipients,
                ]
            );

            if ($response->failed()) {
                Log::warning('Gagal mengirim custom blast WhatsApp', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return null;
            }

            return $response->json('blast_id');
        } catch (\Throwable $e) {
            Log::error('Error saat mengirim custom blast WhatsApp: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper specifically for notifying assigned Balais about a new/updated Laporan.
     * Generates a dynamic message per Balai and sends them via custom blast.
     */
    public function notifyBalaisAboutLaporan(LaporanMasyarakat $laporan, $balais): ?string
    {
        $recipients = [];
        $waktuPelaporan = $laporan->created_at ? $laporan->created_at->format('d M Y H:i') : '-';
        
        // Eager load relationships safely to avoid N+1 queries if they aren't loaded
        $laporan->loadMissing(['kelurahan', 'kecamatan', 'kabupatenKota', 'provinsi']);
        
        $lokasi = sprintf(
            "%s, Kec. %s, %s, Prov. %s",
            $laporan->kelurahan->nama ?? '-',
            $laporan->kecamatan->nama ?? '-',
            $laporan->kabupatenKota->nama ?? '-',
            $laporan->provinsi->nama ?? '-'
        );

        $linkSitaba = route('laporan.show', $laporan->id);
        $linkGmaps = $laporan->gmaps_link ?: '-';

        foreach ($balais as $balai) {
            // Asumsi field nomor telepon di tabel balais bernama 'telepon' / 'no_whatsapp'
            // Sesuaikan dengan nama field di database Anda
            $nomorBalai = $balai->telepon ?? $balai->no_wa ?? null; 
            
            if (!$nomorBalai) continue;

            $namaBalai = $balai->nama_balai ?? $balai->nama;

            $pesan = "Kepada {$namaBalai}
Kami dari Tim Call Center Bencana Pusdatin menginformasikan adanya aduan masyarakat

* Nama Pelapor: {$laporan->pelapor}
* No. WhatsApp: {$laporan->telepon}
  RINCIAN LAPORAN
* Waktu Pelaporan: {$waktuPelaporan}
* Jenis Bencana: {$laporan->jenis_bencana} ({$laporan->nama_bencana})
* Lokasi: {$lokasi}
* Link Google Maps: {$linkGmaps}
Mohon untuk dapat segera ditindaklanjuti serta disampaikan kembali laporan tersebut pada Sitaba
🔗 {$linkSitaba}

Salam Hormat,
Call Center Bencana Pusdatin
WhatsApp Center : 0815-1000-0158";

            $recipients[] = [
                'telepon' => $nomorBalai,
                'message' => $pesan,
            ];
        }

        if (empty($recipients)) {
            Log::info('Tidak ada nomor balai valid untuk di-blast laporan ID: ' . $laporan->id);
            return null;
        }

        return $this->sendCustomBlast($recipients);
    }
}