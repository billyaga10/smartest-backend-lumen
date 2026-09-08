<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/notifications.json');
    }

    private function getInitialNotifications(): array
    {
        return [
            [
                'id' => 'notif_001',
                'title' => 'Pengajuan Izin Disetujui',
                'message' => 'Permohonan izin Olimpiade Sains pada 2 Sep 2026 telah disetujui oleh Waka Kesiswaan.',
                'date' => '1 Sep 2026, 14:15 WIB',
                'isRead' => false,
                'type' => 'LEAVE_STATUS',
                'targetId' => 'lv_002',
            ],
            [
                'id' => 'notif_002',
                'title' => 'Presensi Tepat Waktu',
                'message' => 'Presensi masuk Anda pada Selasa, 8 Sep 2026 tercatat pukul 06:45 WIB.',
                'date' => '8 Sep 2026, 06:45 WIB',
                'isRead' => true,
                'type' => 'ATTENDANCE_ALERT',
                'targetId' => 'att_20260908',
            ],
        ];
    }

    private function loadNotifications(): array
    {
        if (!file_exists($this->filePath)) {
            $initial = $this->getInitialNotifications();
            $this->saveNotifications($initial);
            return $initial;
        }

        $content = file_get_contents($this->filePath);
        $data = json_decode($content, true);

        return is_array($data) ? $data : $this->getInitialNotifications();
    }

    private function saveNotifications(array $data): void
    {
        file_put_contents($this->filePath, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index()
    {
        return response()->json($this->loadNotifications());
    }

    public function markAsRead($id)
    {
        $notifications = $this->loadNotifications();
        foreach ($notifications as &$notif) {
            if ($notif['id'] === $id) {
                $notif['isRead'] = true;
                break;
            }
        }
        $this->saveNotifications($notifications);
        return response()->json(['message' => 'Notifikasi ditandai dibaca.']);
    }

    public function markAllAsRead()
    {
        $notifications = $this->loadNotifications();
        foreach ($notifications as &$notif) {
            $notif['isRead'] = true;
        }
        $this->saveNotifications($notifications);
        return response()->json(['message' => 'Semua notifikasi berhasil ditandai dibaca.']);
    }
}
