<?php

namespace App\Controllers;

class PublicDashboardController extends BaseController
{
    public function index()
    {
        $isTvMode = $this->request->getGet('tv') === '1';

        $rows = $this->getPublicRows();

        $summary = $this->buildPublicSummary($rows);

        return view('public/dashboard_kehadiran', [
            'summary' => $summary,
            'generatedAt' => date('Y-m-d H:i:s'),
            'isTvMode' => $isTvMode,
        ]);
    }

    public function detail()
    {
        $organizer = trim((string) $this->request->getGet('organizer'));
        $location = trim((string) $this->request->getGet('location'));
        $session = trim((string) $this->request->getGet('session'));
        $presence = trim((string) $this->request->getGet('presence'));

        if ($organizer === '' || $location === '' || $session === '' || ! in_array($presence, ['hadir', 'tidak_hadir'], true)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Parameter tidak valid.',
                'items' => [],
            ]);
        }

        $rows = $this->getPublicRows();
        $items = [];

        foreach ($rows as $row) {
            $raw = $this->decodeRawData($row['raw_data'] ?? null);

            $rowOrganizer = $this->extractRawValue($raw, [
                'unit kerja penyelenggara',
                'unit kerja pelaksana',
                'unit penyelenggara',
            ]);
            if ($rowOrganizer === null || $rowOrganizer === '') {
                $rowOrganizer = trim((string) ($row['work_unit'] ?? ''));
            }
            if ($rowOrganizer === '') {
                $rowOrganizer = 'Belum terisi';
            }

            $rowLocation = $this->extractRawValue($raw, [
                'lokasi ujian',
                'lokasi seleksi',
                'lokasi',
                'tilok sktt',
                'tilok',
            ]) ?? '-';

            $rowSession = $this->extractRawValue($raw, ['sesi', 'sesi ujian']) ?? '';
            $rowTime = $this->extractRawValue($raw, ['jam', 'jam ujian', 'waktu']) ?? '';
            $rowZone = $this->extractRawValue($raw, ['zona waktu', 'timezone', 'zona']) ?? '';
            $rowSessionLabel = trim($rowSession);
            if ($rowSessionLabel === '' && ($rowTime !== '' || $rowZone !== '')) {
                $rowSessionLabel = trim($rowTime . ($rowZone !== '' ? ' ' . $rowZone : ''));
            }
            if ($rowSessionLabel === '') {
                $rowSessionLabel = 'Belum terisi';
            }

            $isPresent = ! empty($row['first_scanned_at']);
            $isMatchPresence = $presence === 'hadir' ? $isPresent : ! $isPresent;

            if ($rowOrganizer !== $organizer || $rowLocation !== $location || $rowSessionLabel !== $session || ! $isMatchPresence) {
                continue;
            }

            $items[] = [
                'participant_number' => (string) ($row['participant_number'] ?? '-'),
                'full_name' => (string) ($row['full_name'] ?? '-'),
                'position' => (string) ($row['position'] ?? '-'),
            ];
        }

        usort($items, static function (array $a, array $b): int {
            return strcmp($a['participant_number'], $b['participant_number']);
        });

        return $this->response->setJSON([
            'success' => true,
            'items' => $items,
        ]);
    }

    private function getPublicRows(): array
    {
        return db_connect()->table('participants p')
            ->select('p.participant_number, p.full_name, p.position, p.work_unit, p.raw_data, a.first_scanned_at')
            ->join('attendance_logs a', 'a.participant_id = p.id', 'left')
            ->get()
            ->getResultArray();
    }

    private function buildPublicSummary(array $rows): array
    {
        $summary = [];

        foreach ($rows as $row) {
            $raw = $this->decodeRawData($row['raw_data'] ?? null);

            $organizer = $this->extractRawValue($raw, [
                'unit kerja penyelenggara',
                'unit kerja pelaksana',
                'unit penyelenggara',
            ]);
            if ($organizer === null || $organizer === '') {
                $organizer = trim((string) ($row['work_unit'] ?? ''));
            }
            if ($organizer === '') {
                $organizer = 'Belum terisi';
            }

            $location = $this->extractRawValue($raw, [
                'lokasi ujian',
                'lokasi seleksi',
                'lokasi',
                'tilok sktt',
                'tilok',
            ]) ?? '-';

            $address = $this->extractRawValue($raw, [
                'alamat',
                'alamat lokasi ujian',
                'alamat seleksi',
                'alamat lokasi',
            ]) ?? '-';

            $session = $this->extractRawValue($raw, ['sesi', 'sesi ujian']) ?? '';
            $time = $this->extractRawValue($raw, ['jam', 'jam ujian', 'waktu']) ?? '';
            $zone = $this->extractRawValue($raw, ['zona waktu', 'timezone', 'zona']) ?? '';

            $sessionLabel = trim($session);
            if ($sessionLabel === '' && ($time !== '' || $zone !== '')) {
                $sessionLabel = trim($time . ($zone !== '' ? ' ' . $zone : ''));
            }
            if ($sessionLabel === '') {
                $sessionLabel = 'Belum terisi';
            }

            $sessionTime = trim($time . ($zone !== '' ? ' ' . $zone : ''));

            $locationKey = md5($location . '|' . $address);
            $sessionKey = md5($sessionLabel . '|' . $sessionTime);

            if (! isset($summary[$organizer])) {
                $summary[$organizer] = [
                    'organizer' => $organizer,
                    'total_participants' => 0,
                    'total_present' => 0,
                    'total_absent' => 0,
                    'locations' => [],
                ];
            }

            if (! isset($summary[$organizer]['locations'][$locationKey])) {
                $summary[$organizer]['locations'][$locationKey] = [
                    'location' => $location,
                    'address' => $address,
                    'sessions' => [],
                ];
            }

            if (! isset($summary[$organizer]['locations'][$locationKey]['sessions'][$sessionKey])) {
                $summary[$organizer]['locations'][$locationKey]['sessions'][$sessionKey] = [
                    'session' => $sessionLabel,
                    'time' => $sessionTime,
                    'session_order' => $this->extractSessionOrder($sessionLabel),
                    'participants' => 0,
                    'present' => 0,
                    'absent' => 0,
                ];
            }

            $isPresent = ! empty($row['first_scanned_at']);

            $summary[$organizer]['locations'][$locationKey]['sessions'][$sessionKey]['participants']++;
            if ($isPresent) {
                $summary[$organizer]['locations'][$locationKey]['sessions'][$sessionKey]['present']++;
            } else {
                $summary[$organizer]['locations'][$locationKey]['sessions'][$sessionKey]['absent']++;
            }

            $summary[$organizer]['total_participants']++;
            if ($isPresent) {
                $summary[$organizer]['total_present']++;
            } else {
                $summary[$organizer]['total_absent']++;
            }
        }

        ksort($summary);
        foreach ($summary as &$unit) {
            ksort($unit['locations']);
            foreach ($unit['locations'] as &$loc) {
                $loc['sessions'] = array_values($loc['sessions']);
                usort($loc['sessions'], static function (array $a, array $b): int {
                    $aOrder = $a['session_order'] ?? PHP_INT_MAX;
                    $bOrder = $b['session_order'] ?? PHP_INT_MAX;

                    if ($aOrder !== $bOrder) {
                        return $aOrder <=> $bOrder;
                    }

                    return strnatcasecmp((string) ($a['session'] ?? ''), (string) ($b['session'] ?? ''));
                });
            }
            unset($loc);
        }
        unset($unit);

        return array_values($summary);
    }

    private function extractRawValue(array $raw, array $keywords): ?string
    {
        foreach ($raw as $key => $value) {
            $k = $this->normalizeHeader((string) $key);
            foreach ($keywords as $keyword) {
                $nKeyword = $this->normalizeHeader($keyword);
                if ($k === $nKeyword || str_contains($k, $nKeyword)) {
                    $v = trim((string) $value);
                    if ($v !== '') {
                        return $v;
                    }
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

    private function decodeRawData(mixed $rawData): array
    {
        if (! is_string($rawData) || $rawData === '') {
            return [];
        }

        $decoded = json_decode($rawData, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function extractSessionOrder(string $sessionLabel): int
    {
        if (preg_match('/\\d+/', $sessionLabel, $matches)) {
            return (int) $matches[0];
        }

        return PHP_INT_MAX;
    }
}
