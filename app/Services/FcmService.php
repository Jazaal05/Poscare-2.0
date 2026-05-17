<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FcmService
{
    private string $serviceAccountPath;
    private ?string $projectId = null;

    public function __construct()
    {
        $this->serviceAccountPath = base_path('firebase-service-account.json');
    }

    /**
     * Kirim notifikasi ke semua user yang punya FCM token
     */
    public function sendToAll(string $title, string $body, array $data = []): array
    {
        if (!file_exists($this->serviceAccountPath)) {
            Log::warning('FCM: firebase-service-account.json tidak ditemukan');
            return ['success' => false, 'message' => 'Service account tidak ditemukan'];
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['success' => false, 'message' => 'Gagal mendapatkan access token Firebase'];
        }

        $tokens = DB::table('users')
            ->whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            return ['success' => false, 'message' => 'Tidak ada device terdaftar'];
        }

        $successCount = 0;
        $failCount    = 0;
        $url          = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";

        foreach ($tokens as $token) {
            $payload = [
                'message' => [
                    'token'        => $token,
                    'notification' => ['title' => $title, 'body' => $body],
                    'data'         => array_merge(['type' => 'jadwal'], $data),
                    'android'      => ['priority' => 'high'],
                ],
            ];

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $accessToken,
                'Content-Type: application/json',
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $successCount++;
            } else {
                $failCount++;
                // Hapus token yang sudah tidak valid (UNREGISTERED)
                if ($httpCode === 404) {
                    $decoded = json_decode($response, true);
                    $errorCode = $decoded['error']['details'][0]['errorCode'] ?? '';
                    if ($errorCode === 'UNREGISTERED') {
                        DB::table('users')->where('fcm_token', $token)->update(['fcm_token' => null]);
                        Log::info("FCM: Token UNREGISTERED dihapus dari database");
                    }
                } else {
                    Log::warning("FCM: Gagal kirim ke token $token, HTTP $httpCode, response: $response");
                }
            }
        }

        return [
            'success' => true,
            'message' => "Notifikasi terkirim ke $successCount device, gagal $failCount",
            'total'   => count($tokens),
        ];
    }

    /**
     * Simpan notifikasi ke tabel notifikasi
     */
    public function saveNotifikasi(string $judul, string $pesan, string $tipe = 'jadwal'): void
    {
        DB::table('notifikasi')->insert([
            'judul'      => $judul,
            'pesan'      => $pesan,
            'tipe'       => $tipe,
            'is_read'    => 0,
            'created_at' => now(),
        ]);
    }

    private function getAccessToken(): ?string
    {
        $serviceAccount = json_decode(file_get_contents($this->serviceAccountPath), true);
        $this->projectId = $serviceAccount['project_id'];
        $now = time();

        $header  = rtrim(strtr(base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT'])), '+/', '-_'), '=');
        $payload = rtrim(strtr(base64_encode(json_encode([
            'iss'   => $serviceAccount['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ])), '+/', '-_'), '=');

        $signInput  = $header . '.' . $payload;
        $privateKey = $serviceAccount['private_key'];
        openssl_sign($signInput, $signature, $privateKey, 'SHA256');
        $sig = rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
        $jwt = $signInput . '.' . $sig;

        $ch = curl_init('https://oauth2.googleapis.com/token');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = json_decode(curl_exec($ch), true);
        curl_close($ch);

        return $response['access_token'] ?? null;
    }
}
