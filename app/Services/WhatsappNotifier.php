<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappNotifier
{
    /**
     * Sends a WhatsApp message to the given phone number via the bot's
     * outbound /send-message endpoint. Returns true/false rather than
     * throwing, so a failed notification doesn't break whatever admin
     * action triggered it.
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
     * Sends the same message to a list of phone numbers via the bot's
     * /send-blast endpoint. Returns the blast_id on success so the caller
     * can report back to the user, or null if the request itself failed.
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
}