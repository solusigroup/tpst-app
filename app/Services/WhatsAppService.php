<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Send WhatsApp message using generic API (default Fonnte format).
     * 
     * @param string $target Phone number
     * @param string $message Message to send
     * @return bool
     */
    public static function sendMessage($target, $message)
    {
        $token = env('WA_API_TOKEN', 'YOUR_FONNTE_TOKEN');
        $endpoint = env('WA_API_URL', 'https://api.fonnte.com/send');

        if ($token === 'YOUR_FONNTE_TOKEN' || empty($token)) {
            Log::warning('WhatsApp token not configured, message not sent: ' . $message);
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post($endpoint, [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info('WhatsApp message sent successfully to ' . $target);
                return true;
            } else {
                Log::error('WhatsApp message failed: ' . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp message exception: ' . $e->getMessage());
            return false;
        }
    }
}
