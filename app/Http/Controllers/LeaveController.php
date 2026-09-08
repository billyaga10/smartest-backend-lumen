<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveController extends Controller
{
    private static $leaves = [
        [
            'id' => 'lv_001',
            'requestNumber' => '#IZN-20260908-042',
            'studentId' => 'STD-1001',
            'type' => 'SICK',
            'startDate' => '2026-09-09',
            'endDate' => '2026-09-10',
            'formattedDateRange' => '9 Sep 2026 - 10 Sep 2026',
            'durationDays' => 2,
            'reason' => 'Demam tinggi disertai flu, dokter menyarankan istirahat selama 2 hari.',
            'attachment' => [
                'name' => 'Surat_Dokter_Klinik.pdf',
                'uri' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'size' => '1.8 MB',
                'type' => 'application/pdf',
            ],
            'status' => 'PENDING',
            'operatorNote' => 'Menunggu persetujuan Wali Kelas (Drs. H. Mulyadi, M.Pd)',
            'submittedAt' => '8 Sep 2026, 08:30 WIB',
        ],
        [
            'id' => 'lv_002',
            'requestNumber' => '#IZN-20260901-015',
            'studentId' => 'STD-1001',
            'type' => 'PERMISSION',
            'startDate' => '2026-09-02',
            'endDate' => '2026-09-02',
            'formattedDateRange' => '2 Sep 2026',
            'durationDays' => 1,
            'reason' => 'Mengikuti Olimpiade Sains Madrasah Tingkat Kota',
            'attachment' => [
                'name' => 'Surat_Tugas_Olimpiade.pdf',
                'uri' => 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf',
                'size' => '850 KB',
                'type' => 'application/pdf',
            ],
            'status' => 'APPROVED',
            'operatorNote' => 'Disetujui oleh Waka Kesiswaan',
            'submittedAt' => '1 Sep 2026, 14:15 WIB',
        ],
    ];

    public function index(Request $request)
    {
        $status = $request->query('status');
        $data = self::$leaves;

        if ($status && $status !== 'ALL') {
            $data = array_values(array_filter($data, fn($item) => $item['status'] === $status));
        }

        return response()->json($data);
    }

    public function show($id)
    {
        foreach (self::$leaves as $item) {
            if ($item['id'] === $id) {
                return response()->json($item);
            }
        }

        return response()->json(['message' => 'Pengajuan izin tidak ditemukan'], 404);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'type' => 'required',
            'startDate' => 'required',
            'endDate' => 'required',
            'reason' => 'required|max:250',
        ]);

        $newId = 'lv_' . time();
        $newRequest = [
            'id' => $newId,
            'requestNumber' => '#IZN-' . date('Ymd') . '-' . rand(100, 999),
            'studentId' => 'STD-1001',
            'type' => $request->input('type'),
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'formattedDateRange' => $request->input('startDate') . ' s/d ' . $request->input('endDate'),
            'durationDays' => 1,
            'reason' => $request->input('reason'),
            'attachment' => $request->input('attachment'),
            'status' => 'PENDING',
            'operatorNote' => 'Permohonan baru berhasil diajukan.',
            'submittedAt' => date('d M Y, H:i') . ' WIB',
        ];

        array_unshift(self::$leaves, $newRequest);

        return response()->json($newRequest, 201);
    }

    public function cancel($id)
    {
        return response()->json(['message' => 'Pengajuan izin berhasil dibatalkan.']);
    }
}
