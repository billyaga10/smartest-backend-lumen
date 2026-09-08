<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FcmService;

class FcmController extends Controller
{
    protected FcmService $fcmService;

    public function __construct(FcmService $fcmService)
    {
        $this->fcmService = $fcmService;
    }

    /**
     * POST /api/fcm/token
     * Register or update User FCM Device Token
     */
    public function updateToken(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|string',
            'fcm_token' => 'required|string',
        ]);

        $userId = $request->input('user_id');
        $fcmToken = $request->input('fcm_token');

        // Here in production:
        // User::where('id', $userId)->update(['fcm_token' => $fcmToken]);

        return response()->json([
            'status' => 'success',
            'message' => 'FCM Token registered successfully.',
            'data' => [
                'user_id' => $userId,
                'fcm_token' => $fcmToken,
            ]
        ]);
    }

    /**
     * POST /api/fcm/send
     * Trigger a Push Notification from Backend to Device/Topic
     */
    public function sendNotification(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|string',
            'body' => 'required|string',
        ]);

        $title = $request->input('title');
        $body = $request->input('body');
        $token = $request->input('fcm_token');
        $topic = $request->input('topic');

        if (!empty($topic)) {
            $success = $this->fcmService->sendPushToTopic($topic, $title, $body);
        } else if (!empty($token)) {
            $success = $this->fcmService->sendPushToDevice($token, $title, $body);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Either fcm_token or topic is required.',
            ], 422);
        }

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? 'Push notification sent via FCM.' : 'Failed to send notification via FCM.',
        ]);
    }
}
