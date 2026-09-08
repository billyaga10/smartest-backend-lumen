<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/student_attendances.json');
    }

    private function getInitialAttendances(): array
    {
        return [
            [
                'id' => 'att_20260908',
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
                'notes' => 'Hadir tepat waktu mengikuti apel pagi.',
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
    }

    private function loadAttendances(): array
    {
        if (!file_exists($this->filePath)) {
            $initial = $this->getInitialAttendances();
            $this->saveAttendances($initial);
            return $initial;
        }

        $content = file_get_contents($this->filePath);
        $data = json_decode($content, true);

        return is_array($data) ? $data : $this->getInitialAttendances();
    }

    private function saveAttendances(array $attendances): void
    {
        file_put_contents($this->filePath, json_encode($attendances, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function dashboard()
    {
        $attendances = $this->loadAttendances();
        $today = date('Y-m-d');
        $todayAttendance = null;

        foreach ($attendances as $att) {
            if ($att['date'] === $today) {
                $todayAttendance = $att;
                break;
            }
        }

        if (!$todayAttendance) {
            $todayAttendance = $attendances[0] ?? [
                'id' => 'att_today',
                'studentId' => 'STD-1001',
                'date' => $today,
                'fullDate' => 'Hari Ini',
                'status' => 'PRESENT',
                'checkIn' => '06:45 WIB',
                'checkOut' => '15:30 WIB',
                'location' => 'Gerbang Utama',
                'verificationStatus' => 'Verifikasi berhasil',
            ];
        }

        $presentCount = count(array_filter($attendances, fn($a) => $a['status'] === 'PRESENT'));
        $permissionCount = count(array_filter($attendances, fn($a) => $a['status'] === 'PERMISSION'));
        $sickCount = count(array_filter($attendances, fn($a) => $a['status'] === 'SICK'));
        $absentCount = count(array_filter($attendances, fn($a) => $a['status'] === 'ABSENT'));

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
            'todayAttendance' => $todayAttendance,
            'summary' => [
                'totalDays' => count($attendances),
                'presentCount' => $presentCount,
                'permissionCount' => $permissionCount,
                'sickCount' => $sickCount,
                'absentCount' => $absentCount,
                'attendancePercentage' => count($attendances) > 0 ? round(($presentCount / count($attendances)) * 100, 1) : 100,
            ],
        ]);
    }

    public function attendances(Request $request)
    {
        $status = $request->query('status');
        $attendances = $this->loadAttendances();

        if ($status && $status !== 'ALL') {
            $attendances = array_values(array_filter($attendances, fn($item) => $item['status'] === $status));
        }

        return response()->json($attendances);
    }

    public function attendanceDetail($id)
    {
        $attendances = $this->loadAttendances();
        foreach ($attendances as $att) {
            if ($att['id'] === $id) {
                return response()->json($att);
            }
        }

        return response()->json([
            'id' => $id,
            'studentId' => 'STD-1001',
            'date' => date('Y-m-d'),
            'fullDate' => 'Hari Ini',
            'status' => 'PRESENT',
            'checkIn' => '06:45 WIB',
            'checkOut' => '15:30 WIB',
            'checkInLocation' => 'Gerbang Utama Madrasah',
            'checkOutLocation' => 'Gerbang Utama Madrasah',
            'location' => 'Gerbang Utama Madrasah',
            'verificationStatus' => 'Terverifikasi Mesin Presensi & QR Code',
            'notes' => 'Hadir tepat waktu.',
        ]);
    }
}
