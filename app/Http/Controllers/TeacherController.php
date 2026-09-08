<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherController extends Controller
{
    public function dashboard()
    {
        return response()->json([
            'teacher' => [
                'id' => 'tch_001',
                'name' => 'Drs. H. Mulyadi, M.Pd',
                'nip' => '197508122002121003',
                'className' => 'XII MIPA 1',
                'school' => 'MAN 1 Kota Malang',
            ],
            'summary' => [
                'totalStudents' => 32,
                'present' => 28,
                'sick' => 2,
                'permission' => 1,
                'absent' => 1,
            ],
            'students' => [
                ['id' => 'std_001', 'name' => 'Ahmad Faiz Al-Ghifari', 'nisn' => '0087462819', 'status' => 'PRESENT', 'checkIn' => '06:52 WIB'],
                ['id' => 'std_002', 'name' => 'Budi Santoso', 'nisn' => '0089123847', 'status' => 'PRESENT', 'checkIn' => '06:45 WIB'],
                ['id' => 'std_003', 'name' => 'Citra Dewi', 'nisn' => '0081239845', 'status' => 'SICK', 'checkIn' => null],
                ['id' => 'std_004', 'name' => 'Doni Pratama', 'nisn' => '0087162534', 'status' => 'PERMISSION', 'checkIn' => null],
            ],
        ]);
    }
}
