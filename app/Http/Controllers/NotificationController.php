<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function index()
    {
        return response()->json([
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
        ]);
    }

    public function markAsRead($id)
    {
        return response()->json(['message' => 'Notifikasi ditandai dibaca.']);
    }

    public function markAllAsRead()
    {
        return response()->json(['message' => 'Semua notifikasi berhasil ditandai dibaca.']);
    }
}
