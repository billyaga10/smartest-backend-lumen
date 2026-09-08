<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LeaveController extends Controller
{
    private string $filePath;

    public function __construct()
    {
        $this->filePath = storage_path('app/leaves.json');
    }

    private function getInitialLeaves(): array
    {
        return [
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
    }

    private function loadLeaves(): array
    {
        if (!file_exists($this->filePath)) {
            $initial = $this->getInitialLeaves();
            $this->saveLeaves($initial);
            return $initial;
        }

        $content = file_get_contents($this->filePath);
        $data = json_decode($content, true);

        return is_array($data) ? $data : $this->getInitialLeaves();
    }

    private function saveLeaves(array $leaves): void
    {
        file_put_contents($this->filePath, json_encode($leaves, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function index(Request $request)
    {
        $status = $request->query('status');
        $data = $this->loadLeaves();

        if ($status && $status !== 'ALL') {
            $data = array_values(array_filter($data, fn($item) => $item['status'] === $status));
        }

        return response()->json($data);
    }

    public function show($id)
    {
        $data = $this->loadLeaves();
        foreach ($data as $item) {
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

        $leaves = $this->loadLeaves();
        $attachment = $request->input('attachment');
        if ($attachment && is_array($attachment)) {
            $uri = $attachment['uri'] ?? '';
            $name = strtolower($attachment['name'] ?? '');
            if (empty($uri) || str_starts_with($uri, 'blob:') || str_starts_with($uri, 'file:')) {
                if (str_contains($name, '.pdf')) {
                    $attachment['uri'] = 'https://www.w3.org/WAI/ER/tests/xhtml/testfiles/resources/pdf/dummy.pdf';
                } else {
                    $attachment['uri'] = 'https://images.unsplash.com/photo-1584515979956-d9f6e5d09982?w=1000';
                }
            }
        }

        $newId = 'lv_' . time();
        $newRequest = [
            'id' => $newId,
            'requestNumber' => '#IZN-' . date('Ymd') . '-' . rand(100, 999),
            'studentId' => 'STD-1001',
            'type' => $request->input('type'),
            'startDate' => $request->input('startDate'),
            'endDate' => $request->input('endDate'),
            'formattedDateRange' => $request->input('startDate') === $request->input('endDate')
                ? $request->input('startDate')
                : $request->input('startDate') . ' s/d ' . $request->input('endDate'),
            'durationDays' => 1,
            'reason' => $request->input('reason'),
            'attachment' => $attachment,
            'status' => 'PENDING',
            'operatorNote' => 'Permohonan baru berhasil diajukan.',
            'submittedAt' => date('d M Y, H:i') . ' WIB',
        ];

        array_unshift($leaves, $newRequest);
        $this->saveLeaves($leaves);

        return response()->json($newRequest, 201);
    }

    public function cancel($id)
    {
        $leaves = $this->loadLeaves();
        $found = false;

        foreach ($leaves as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = 'CANCELLED';
                $item['operatorNote'] = 'Pengajuan dibatalkan oleh siswa.';
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->saveLeaves($leaves);
            return response()->json(['message' => 'Pengajuan izin berhasil dibatalkan.']);
        }

        return response()->json(['message' => 'Pengajuan izin tidak ditemukan.'], 404);
    }

    public function updateStatus(Request $request, $id)
    {
        $this->validate($request, [
            'status' => 'required|in:APPROVED,REJECTED,CANCELLED,PENDING',
        ]);

        $leaves = $this->loadLeaves();
        $found = false;
        $updatedItem = null;

        foreach ($leaves as &$item) {
            if ($item['id'] === $id) {
                $item['status'] = $request->input('status');
                if ($request->has('operatorNote')) {
                    $item['operatorNote'] = $request->input('operatorNote');
                } else if ($item['status'] === 'APPROVED') {
                    $item['operatorNote'] = 'Disetujui oleh Wali Kelas.';
                } else if ($item['status'] === 'REJECTED') {
                    $item['operatorNote'] = 'Permohonan ditolak oleh Wali Kelas.';
                }
                $updatedItem = $item;
                $found = true;
                break;
            }
        }

        if ($found) {
            $this->saveLeaves($leaves);
            return response()->json([
                'status' => 'success',
                'message' => 'Status permohonan izin berhasil diperbarui.',
                'data' => $updatedItem,
            ]);
        }

        return response()->json(['message' => 'Pengajuan izin tidak ditemukan.'], 404);
    }
}
