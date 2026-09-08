<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class FcmService
{
    /**
     * Firebase Cloud Messaging API Endpoint & Config
     */
    protected string $fcmUrl = 'https://fcm.googleapis.com/fcm/send';
    protected string $serverKey;
    protected string $credentialsPath;

    public function __construct()
    {
        $this->serverKey = env('FCM_SERVER_KEY', 'YOUR_FIREBASE_SERVER_KEY');
        $this->credentialsPath = base_path('firebase-credentials.json');
    }

    /**
     * Send FCM Push Notification to a single Android FCM Token
     *
     * @param string $fcmToken
     * @param string $title
     * @param string $body
     * @param array $extraData
     * @return bool
     */
    public function sendPushToDevice(string $fcmToken, string $title, string $body, array $extraData = []): bool
    {
        if (empty($fcmToken)) {
            Log::warning('[FCM Backend] Empty FCM token provided.');
            return false;
        }

        $payload = [
            'to' => $fcmToken,
            'priority' => 'high',
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
                'badge' => 1,
                'android_channel_id' => 'default',
            ],
            'data' => array_merge([
                'title' => $title,
                'body' => $body,
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                'timestamp' => date('c'),
            ], $extraData),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->serverKey,
                'Content-Type' => 'application/json',
            ])->post($this->fcmUrl, $payload);

            if ($response->successful()) {
                Log::info("[FCM Backend] Successfully sent push notification to token: {$fcmToken}");
                return true;
            }

            Log::error("[FCM Backend] Failed sending FCM push. Status: {$response->status()}, Response: {$response->body()}");
            return false;
        } catch (\Throwable $e) {
            Log::error('[FCM Backend] Exception while sending push: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Send FCM Push Notification to a Topic (e.g., 'kelas_12_mipa_1' or 'all_parents')
     */
    public function sendPushToTopic(string $topic, string $title, string $body, array $extraData = []): bool
    {
        return $this->sendPushToDevice('/topics/' . $topic, $title, $body, $extraData);
    }
}
