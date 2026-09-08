<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'student' => [
                'id' => 'STD-1001',
                'name' => 'Ahmad Fulan',
                'nisn' => '0054819201',
                'className' => 'XII IPA 1',
                'school' => 'MAN 1 Kota Malang',
                'academicYear' => '2026/2027 Ganjil',
                'avatar' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=400',
            ],
            'todayAttendance' => [
                'id' => 'att_today',
                'studentId' => 'STD-1001',
                'date' => date('Y-m-d'),
                'fullDate' => 'Selasa, 8 September 2026',
                'status' => 'PRESENT',
                'checkIn' => '06:45 WIB',
                'checkOut' => '15:30 WIB',
                'checkInLocation' => 'Gerbang Utama Madrasah',
                'checkOutLocation' => 'Gerbang Utama Madrasah',
                'location' => 'Gerbang Utama Madrasah',
                'verificationStatus' => 'Terverifikasi Mesin Presensi & QR Code',
            ],
            'summary' => [
                'totalDays' => 24,
                'presentCount' => 20,
                'permissionCount' => 2,
                'sickCount' => 1,
                'absentCount' => 1,
                'attendancePercentage' => 91.6,
            ],
        ]);
    }

    public function attendances(Request $request)
    {
        $status = $request->query('status');

        $attendances = [
            [
                'id' => 'att_20260908',
                'studentId' => 'STD-1001',
                'date' => '2026-09-08',
                'fullDate' => 'Selasa, 8 September 2026',
                'status' => 'PRESENT',
                'checkIn' => '06:45 WIB',
                'checkOut' => '15:30 WIB',
                'checkInLocation' => 'Gerbang Utama',
                'checkOutLocation' => 'Gerbang Utama',
                'location' => 'Gerbang Utama',
                'verificationStatus' => 'Verifikasi berhasil',
            ],
            [
                'id' => 'att_20260907',
                'studentId' => 'STD-1001',
                'date' => '2026-09-07',
                'fullDate' => 'Senin, 7 September 2026',
                'status' => 'PRESENT',
                'checkIn' => '06:50 WIB',
                'checkOut' => '15:30 WIB',
                'checkInLocation' => 'Gerbang Utama',
                'checkOutLocation' => 'Gerbang Utama',
                'location' => 'Gerbang Utama',
                'verificationStatus' => 'Verifikasi berhasil',
            ],
            [
                'id' => 'att_20260904',
                'studentId' => 'STD-1001',
                'date' => '2026-09-04',
                'fullDate' => 'Jumat, 4 September 2026',
                'status' => 'SICK',
                'checkIn' => null,
                'checkOut' => null,
                'location' => 'Surat Dokter Terlampir',
                'verificationStatus' => 'Disetujui Wali Kelas',
                'notes' => 'Demam tinggi 2 hari',
            ],
            [
                'id' => 'att_20260903',
                'studentId' => 'STD-1001',
                'date' => '2026-09-03',
                'fullDate' => 'Kamis, 3 September 2026',
                'status' => 'PRESENT',
                'checkIn' => '06:43 WIB',
                'checkOut' => '15:35 WIB',
                'checkInLocation' => 'Gerbang Utama',
                'checkOutLocation' => 'Gerbang Utama',
                'location' => 'Gerbang Utama',
                'verificationStatus' => 'Verifikasi berhasil',
            ],
        ];

        if ($status && $status !== 'ALL') {
            $attendances = array_values(array_filter($attendances, fn($item) => $item['status'] === $status));
        }

        return response()->json($attendances);
    }

    public function attendanceDetail($id)
    {
        return response()->json([
            'id' => $id,
            'studentId' => 'STD-1001',
            'date' => '2026-09-08',
            'fullDate' => 'Selasa, 8 September 2026',
            'status' => 'PRESENT',
            'checkIn' => '06:45 WIB',
            'checkOut' => '15:30 WIB',
            'checkInLocation' => 'Gerbang Utama Madrasah',
            'checkOutLocation' => 'Gerbang Utama Madrasah',
            'location' => 'Gerbang Utama Madrasah',
            'verificationStatus' => 'Terverifikasi Mesin Presensi & QR Code',
            'notes' => 'Hadir tepat waktu mengikuti apel pagi.',
        ]);
    }
}
