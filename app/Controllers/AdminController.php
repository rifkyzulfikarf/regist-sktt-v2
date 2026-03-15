<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\AdminLoginLogModel;
use App\Models\AttendanceLogModel;
use App\Models\AttendanceScanEventModel;
use App\Models\ParticipantModel;
use Dompdf\Dompdf;
use Dompdf\Options;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class AdminController extends BaseController
{
    private function isSuperAdmin(): bool
    {
        return session()->get('admin_role') === 'super_admin';
    }

    private function getScopedWorkUnit(): ?string
    {
        if ($this->isSuperAdmin()) {
            return null;
        }

        $workUnit = trim((string) session()->get('admin_work_unit'));

        return $workUnit !== '' ? $workUnit : null;
    }

    private function requireAdminLogin()
    {
        if (! session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/login'));
        }

        return null;
    }

    private function requireSuperAdmin()
    {
        if (! $this->isSuperAdmin()) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Akses ditolak. Fitur ini hanya untuk Super Admin.');
        }

        return null;
    }

    public function login()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        if ($this->request->getMethod() === 'post') {
            $rules = [
                'username' => 'required|max_length[100]',
                'password' => 'required|max_length[100]',
            ];

            if (! $this->validateData($this->request->getPost(array_keys($rules)), $rules)) {
                return redirect()->back()->withInput()->with('error', validation_list_errors());
            }

            $adminModel = new AdminModel();
            $admin = $adminModel->verifyLogin(
                (string) $this->request->getPost('username'),
                (string) $this->request->getPost('password')
            );

            if (! $admin) {
                return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
            }

            session()->set([
                'admin_logged_in' => true,
                'admin_id'        => $admin['id'],
                'admin_username'  => $admin['username'],
                'admin_role'      => $admin['role'] ?? 'admin_unit',
                'admin_work_unit' => $admin['work_unit'],
            ]);

            if (($admin['role'] ?? 'admin_unit') === 'admin_unit') {
                $this->writeAdminUnitLoginLog($admin['id'], 'success', 'Login berhasil');
            }

            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login');
    }

    public function logout()
    {
        session()->remove(['admin_logged_in', 'admin_id', 'admin_username', 'admin_role', 'admin_work_unit']);

        return redirect()->to(base_url('admin/login'));
    }

    public function dashboard()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }

        $participantModel = new ParticipantModel();
        $scopedWorkUnit = $this->getScopedWorkUnit();

        $participantBuilder = $participantModel->builder();
        if ($scopedWorkUnit !== null) {
            $participantBuilder->where('work_unit', $scopedWorkUnit);
        }
        $totalParticipants = $participantBuilder->countAllResults();

        $attendanceBuilder = db_connect()->table('participants p')
            ->join('attendance_logs a', 'a.participant_id = p.id', 'inner')
            ->where('a.first_scanned_at IS NOT NULL');
        if ($scopedWorkUnit !== null) {
            $attendanceBuilder->where('p.work_unit', $scopedWorkUnit);
        }
        $hadir = $attendanceBuilder->countAllResults();

        return view('admin/dashboard', [
            'totalParticipants' => $totalParticipants,
            'hadir'             => $hadir,
            'tidakHadir'        => max($totalParticipants - $hadir, 0),
            'isSuperAdmin'      => $this->isSuperAdmin(),
            'adminRole'         => (string) session()->get('admin_role'),
            'adminWorkUnit'     => (string) session()->get('admin_work_unit'),
        ]);
    }

    public function scan()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }

        $result = null;

        if ($this->request->getMethod() === 'post') {
            $barcode = trim((string) $this->request->getPost('barcode_value'));
            $result = $this->processScan($barcode);
        }

        return view('admin/scan', [
            'result' => $result,
            'isSuperAdmin' => $this->isSuperAdmin(),
            'adminWorkUnit' => (string) session()->get('admin_work_unit'),
        ]);
    }

    public function import()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }

        if (! $this->isSuperAdmin()) {
            return redirect()->to(base_url('admin/dashboard'))->with('error', 'Fitur import hanya untuk Super Admin.');
        }

        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('excel_file');

            if (! $file || ! $file->isValid()) {
                return redirect()->back()->with('error', 'File Excel tidak valid.');
            }

            if (strtolower($file->getExtension()) !== 'xlsx') {
                return redirect()->back()->with('error', 'Format file harus .xlsx');
            }

            $tempPath = WRITEPATH . 'uploads/' . $file->getRandomName();
            $file->move(dirname($tempPath), basename($tempPath));

            try {
                $summary = $this->importFromExcel($tempPath);
                @unlink($tempPath);

                return redirect()->back()->with('success', sprintf(
                    'Import selesai. Inserted: %d, Updated: %d, Skipped: %d.',
                    $summary['inserted'],
                    $summary['updated'],
                    $summary['skipped']
                ));
            } catch (\Throwable $e) {
                @unlink($tempPath);
                return redirect()->back()->with('error', 'Import gagal: ' . $e->getMessage());
            }
        }

        return view('admin/import');
    }

    public function report()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }

        $participantModel = new ParticipantModel();

        $workUnit = (string) $this->request->getGet('work_unit');
        $scopedWorkUnit = $this->getScopedWorkUnit();
        if ($scopedWorkUnit !== null) {
            $workUnit = $scopedWorkUnit;
        }
        $position = (string) $this->request->getGet('position');
        $status   = (string) $this->request->getGet('status');

        if ($status === '') {
            $status = 'all';
        }

        $rows = $participantModel->getReportRows($workUnit, $position, $status);

        $hadir = 0;
        foreach ($rows as $row) {
            if (! empty($row['first_scanned_at'])) {
                $hadir++;
            }
        }

        return view('admin/report', [
            'rows'       => $rows,
            'filters'    => [
                'work_unit' => $workUnit,
                'position'  => $position,
                'status'    => $status,
            ],
            'workUnits'  => $participantModel->getDistinctWorkUnits(),
            'positions'  => $participantModel->getDistinctPositions(),
            'summary'    => [
                'total'       => count($rows),
                'hadir'       => $hadir,
                'tidak_hadir' => count($rows) - $hadir,
            ],
            'isSuperAdmin'  => $this->isSuperAdmin(),
            'adminWorkUnit' => (string) session()->get('admin_work_unit'),
        ]);
    }

    public function reportPdf()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }

        $reportData = $this->getReportDataFromRequest();
        $html = view('admin/report_pdf', $reportData);

        $options = new Options();
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        return $this->response
            ->setContentType('application/pdf')
            ->setHeader('Content-Disposition', 'inline; filename="laporan_kehadiran_sktt.pdf"')
            ->setBody($dompdf->output());
    }

    public function reportCsv()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }

        $reportData = $this->getReportDataFromRequest();
        $rows = $reportData['rows'];

        $filename = 'laporan_kehadiran_sktt_' . date('Ymd_His') . '.csv';
        $headers = [
            'Nomor Peserta',
            'Nama',
            'Jabatan',
            'Unit Kerja',
            'Tanggal Lahir',
            'Status Kehadiran',
            'Waktu Registrasi Pertama',
            'Jumlah Scan',
        ];

        $fh = fopen('php://temp', 'w+');
        fputcsv($fh, $headers);

        foreach ($rows as $row) {
            fputcsv($fh, [
                $row['participant_number'] ?? '-',
                $row['full_name'] ?? '-',
                $row['position'] ?? '-',
                $row['work_unit'] ?? '-',
                $row['birth_date'] ?? '-',
                ! empty($row['first_scanned_at']) ? 'Hadir' : 'Tidak Hadir',
                $row['first_scanned_at'] ?? '-',
                (string) ($row['scan_count'] ?? 0),
            ]);
        }

        rewind($fh);
        $csv = stream_get_contents($fh);
        fclose($fh);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename=' . $filename)
            ->setBody("\xEF\xBB\xBF" . $csv);
    }

    public function loginLogs()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }
        if ($redirect = $this->requireSuperAdmin()) {
            return $redirect;
        }

        $rows = db_connect()->table('admin_login_logs l')
            ->select('l.*, a.username, a.work_unit')
            ->join('admins a', 'a.id = l.admin_id', 'left')
            ->where('l.admin_role', 'admin_unit')
            ->orderBy('l.login_at', 'DESC')
            ->limit(500)
            ->get()
            ->getResultArray();

        return view('admin/logins', ['rows' => $rows]);
    }

    public function scanLogs()
    {
        if ($redirect = $this->requireAdminLogin()) {
            return $redirect;
        }
        if ($redirect = $this->requireSuperAdmin()) {
            return $redirect;
        }

        $rows = db_connect()->table('attendance_scan_events s')
            ->select('s.*, a.username, a.work_unit, p.participant_number, p.full_name')
            ->join('admins a', 'a.id = s.admin_id', 'left')
            ->join('participants p', 'p.id = s.participant_id', 'left')
            ->where('a.role', 'admin_unit')
            ->orderBy('s.scanned_at', 'DESC')
            ->limit(1000)
            ->get()
            ->getResultArray();

        return view('admin/scan_logs', ['rows' => $rows]);
    }

    private function processScan(string $barcodeValue): array
    {
        $scanEventModel  = new AttendanceScanEventModel();
        $participantModel = new ParticipantModel();
        $attendanceModel = new AttendanceLogModel();
        $adminId = (int) session()->get('admin_id');
        $now = date('Y-m-d H:i:s');

        if ($barcodeValue === '') {
            $scanEventModel->insert([
                'participant_id' => null,
                'admin_id'       => $adminId,
                'barcode_value'  => null,
                'status'         => 'invalid',
                'message'        => 'Input barcode kosong.',
                'scanned_at'     => $now,
            ]);

            return [
                'type'    => 'error',
                'title'   => 'Barcode kosong',
                'message' => 'Silakan input/scan barcode terlebih dahulu.',
            ];
        }

        $participant = null;
        if (preg_match('/^[a-f0-9]{32}$/i', $barcodeValue)) {
            $participant = $participantModel->findByBarcodeMd5($barcodeValue);
        }

        // fallback manual number for emergency/manual input
        if (! $participant) {
            $participant = $participantModel->findByParticipantNumber($barcodeValue);
        }

        if (! $participant) {
            $scanEventModel->insert([
                'participant_id' => null,
                'admin_id'       => $adminId,
                'barcode_value'  => $barcodeValue,
                'status'         => 'invalid',
                'message'        => 'Peserta tidak ditemukan.',
                'scanned_at'     => $now,
            ]);

            return [
                'type'    => 'error',
                'title'   => 'Peserta tidak ditemukan',
                'message' => 'Kode barcode tidak valid atau peserta tidak ada di master data.',
            ];
        }

        $scopedWorkUnit = $this->getScopedWorkUnit();
        if ($scopedWorkUnit !== null && (string) ($participant['work_unit'] ?? '') !== $scopedWorkUnit) {
            $scanEventModel->insert([
                'participant_id' => $participant['id'],
                'admin_id'       => $adminId,
                'barcode_value'  => $barcodeValue,
                'status'         => 'forbidden',
                'message'        => 'Peserta berada di unit kerja lain.',
                'scanned_at'     => $now,
            ]);

            return [
                'type'    => 'error',
                'title'   => 'Akses ditolak',
                'message' => 'Admin unit kerja hanya dapat scan peserta dari unit kerjanya sendiri.',
            ];
        }

        $attendance = $attendanceModel->where('participant_id', $participant['id'])->first();

        if (! $attendance) {
            try {
                $attendanceModel->insert([
                    'participant_id'    => $participant['id'],
                    'first_scanned_at'  => $now,
                    'first_scanned_by'  => $adminId,
                    'scan_count'        => 1,
                    'last_scanned_at'   => $now,
                    'last_scanned_by'   => $adminId,
                ]);

                $scanEventModel->insert([
                    'participant_id' => $participant['id'],
                    'admin_id'       => $adminId,
                    'barcode_value'  => $barcodeValue,
                    'status'         => 'first',
                    'message'        => 'Scan pertama valid.',
                    'scanned_at'     => $now,
                ]);

                return [
                    'type'         => 'success',
                    'title'        => 'Scan pertama valid',
                    'message'      => 'Kehadiran peserta berhasil dicatat.',
                    'participant'  => $participant,
                    'registeredAt' => $now,
                    'scanCount'    => 1,
                ];
            } catch (\Throwable $e) {
                // Jika race condition terjadi pada unique key participant_id
                $attendance = $attendanceModel->where('participant_id', $participant['id'])->first();
                if (! $attendance) {
                    throw $e;
                }
            }
        }

        $attendanceModel->update($attendance['id'], [
            'scan_count'      => ((int) $attendance['scan_count']) + 1,
            'last_scanned_at' => $now,
            'last_scanned_by' => $adminId,
        ]);

        $scanEventModel->insert([
            'participant_id' => $participant['id'],
            'admin_id'       => $adminId,
            'barcode_value'  => $barcodeValue,
            'status'         => 'duplicate',
            'message'        => 'Scan ulang. Waktu registrasi pertama tidak diubah.',
            'scanned_at'     => $now,
        ]);

        return [
            'type'         => 'warning',
            'title'        => 'Sudah pernah scan',
            'message'      => 'Peserta ini sudah tercatat hadir. Waktu registrasi pertama tetap dipertahankan.',
            'participant'  => $participant,
            'registeredAt' => $attendance['first_scanned_at'],
            'scanCount'    => ((int) $attendance['scan_count']) + 1,
        ];
    }

    private function importFromExcel(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $sheet = $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        $headers = [];
        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $header = trim((string) $sheet->getCell([$col, 1])->getFormattedValue());
            if ($header === '') {
                $header = 'Kolom_' . $col;
            }

            if (isset($headers[$header])) {
                $headers[$header] .= '_dupe';
                $header = $headers[$header];
            } else {
                $headers[$header] = $header;
            }
        }

        $headerList = array_values($headers);

        $participantCol = $this->detectHeaderIndex($headerList, ['nomor peserta', 'no peserta', 'nomor_peserta', 'no_peserta']);
        $positionCol    = $this->detectHeaderIndex($headerList, ['jabatan', 'jabatan dilamar', 'formasi jabatan']);
        $birthDateCol   = $this->detectHeaderIndex($headerList, ['tanggal lahir', 'tgl lahir', 'tgl_lahir', 'tanggal_lahir']);

        if ($participantCol === null || $positionCol === null || $birthDateCol === null) {
            throw new \RuntimeException('Kolom wajib tidak ditemukan. Pastikan file memiliki kolom Nomor Peserta, Jabatan, dan Tanggal Lahir.');
        }

        $nameCol = $this->detectHeaderIndex($headerList, ['nama peserta', 'nama']);
        $workUnitCol = $this->detectHeaderIndex($headerList, ['unit kerja', 'satker', 'tilok sktt', 'lokasi ujian']);

        $participantModel = new ParticipantModel();
        $inserted = 0;
        $updated = 0;
        $skipped = 0;

        for ($row = 2; $row <= $highestRow; $row++) {
            $rowData = [];

            foreach ($headerList as $index => $headerName) {
                $col = $index + 1;
                $cell = $sheet->getCell([$col, $row]);
                $rowData[$headerName] = trim((string) $cell->getFormattedValue());
            }

            $participantNumber = trim((string) ($rowData[$headerList[$participantCol]] ?? ''));
            $position = trim((string) ($rowData[$headerList[$positionCol]] ?? ''));

            $birthDateCell = $sheet->getCell([$birthDateCol + 1, $row]);
            $birthDate = $this->normalizeBirthDate($birthDateCell->getValue(), $birthDateCell->getFormattedValue());

            if ($participantNumber === '' || $position === '' || $birthDate === null) {
                $skipped++;
                continue;
            }

            $data = [
                'participant_number' => $participantNumber,
                'full_name'          => $nameCol !== null ? trim((string) ($rowData[$headerList[$nameCol]] ?? '')) : null,
                'position'           => $position,
                'birth_date'         => $birthDate,
                'work_unit'          => $workUnitCol !== null ? trim((string) ($rowData[$headerList[$workUnitCol]] ?? '')) : null,
                'raw_data'           => json_encode($rowData, JSON_UNESCAPED_UNICODE),
                'imported_at'        => date('Y-m-d H:i:s'),
            ];

            $existing = $participantModel->findByParticipantNumber($participantNumber);

            if ($existing) {
                $participantModel->update($existing['id'], $data);
                $updated++;
            } else {
                $participantModel->insert($data);
                $inserted++;
            }
        }

        return compact('inserted', 'updated', 'skipped');
    }

    private function detectHeaderIndex(array $headers, array $keywords): ?int
    {
        foreach ($headers as $index => $header) {
            $normalized = $this->normalizeHeader($header);
            foreach ($keywords as $keyword) {
                if (str_contains($normalized, $this->normalizeHeader($keyword))) {
                    return $index;
                }
            }
        }

        return null;
    }

    private function normalizeHeader(string $value): string
    {
        $value = strtolower(trim($value));
        $value = preg_replace('/[^a-z0-9]+/', ' ', $value);

        return trim((string) $value);
    }

    private function normalizeBirthDate(mixed $rawValue, mixed $formattedValue): ?string
    {
        if (is_numeric($rawValue)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $rawValue)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
        }

        $value = trim((string) $formattedValue);
        if ($value === '') {
            $value = trim((string) $rawValue);
        }

        if ($value === '') {
            return null;
        }

        $formats = ['Y-m-d', 'd-m-Y', 'd/m/Y', 'm/d/Y'];
        foreach ($formats as $format) {
            $dt = \DateTime::createFromFormat($format, $value);
            if ($dt instanceof \DateTime) {
                return $dt->format('Y-m-d');
            }
        }

        $timestamp = strtotime($value);
        if ($timestamp !== false) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    private function getReportDataFromRequest(): array
    {
        $participantModel = new ParticipantModel();

        $workUnit = (string) $this->request->getGet('work_unit');
        $scopedWorkUnit = $this->getScopedWorkUnit();
        if ($scopedWorkUnit !== null) {
            $workUnit = $scopedWorkUnit;
        }
        $position = (string) $this->request->getGet('position');
        $status   = (string) $this->request->getGet('status');
        if ($status === '') {
            $status = 'all';
        }

        $rows = $participantModel->getReportRows($workUnit, $position, $status);

        $hadir = 0;
        foreach ($rows as $row) {
            if (! empty($row['first_scanned_at'])) {
                $hadir++;
            }
        }

        $logoBase64 = null;
        $logoPath = FCPATH . 'images/kemenham_icon.png';
        if (is_file($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode((string) file_get_contents($logoPath));
        }

        return [
            'rows'    => $rows,
            'filters' => [
                'work_unit' => $workUnit,
                'position'  => $position,
                'status'    => $status,
            ],
            'summary' => [
                'total'       => count($rows),
                'hadir'       => $hadir,
                'tidak_hadir' => count($rows) - $hadir,
            ],
            'logoBase64' => $logoBase64,
            'isSuperAdmin' => $this->isSuperAdmin(),
            'adminWorkUnit' => (string) session()->get('admin_work_unit'),
        ];
    }

    private function writeAdminUnitLoginLog(int $adminId, string $status, string $message): void
    {
        $logModel = new AdminLoginLogModel();
        $logModel->insert([
            'admin_id'    => $adminId,
            'admin_role'  => 'admin_unit',
            'ip_address'  => (string) $this->request->getIPAddress(),
            'user_agent'  => substr((string) $this->request->getUserAgent(), 0, 255),
            'status'      => $status,
            'message'     => $message,
            'login_at'    => date('Y-m-d H:i:s'),
        ]);
    }
}
