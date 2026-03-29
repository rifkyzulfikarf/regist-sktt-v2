<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        .header { border-bottom: 3px solid #f3cb0a; padding-bottom: 8px; margin-bottom: 10px; }
        .logo { width: 52px; }
        .title { font-size: 15px; font-weight: 700; color: #0e2a5a; }
        .subtitle { font-size: 11px; color: #1f4f8d; }
        .meta { margin: 8px 0; font-size: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #8ea8ce; padding: 4px 6px; }
        th { background: #edf3fb; color: #0e2a5a; }
    </style>
</head>
<body>
    <table class="header" style="border:none;">
        <tr style="border:none;">
            <td style="width:60px; border:none; vertical-align:top;">
                <?php if (!empty($logoBase64)): ?><img class="logo" src="<?= $logoBase64 ?>" alt="Logo"><?php endif; ?>
            </td>
            <td style="border:none; vertical-align:middle;">
                <div class="title">Laporan Kehadiran SKTT PPPK KemenHAM</div>
                <div class="subtitle">Kementerian Hak Asasi Manusia Republik Indonesia</div>
            </td>
        </tr>
    </table>

    <div class="meta">
        Dicetak: <?= date('Y-m-d H:i:s') ?> | Total: <?= esc((string) $summary['total']) ?> | Hadir: <?= esc((string) $summary['hadir']) ?> | Tidak Hadir: <?= esc((string) $summary['tidak_hadir']) ?>
    </div>

    <table>
        <thead>
        <tr>
            <th>No</th>
            <th>Nomor Peserta</th>
            <th>Nama</th>
            <th>Jabatan</th>
            <th>Unit Kerja</th>
            <th>Lokasi Seleksi</th>
            <th>Sesi / Jam</th>
            <th>Tgl Lahir</th>
            <th>Status</th>
            <th>Waktu Registrasi Pertama</th>
            <th>Jumlah Scan</th>
        </tr>
        </thead>
        <tbody>
        <?php if (empty($rows)): ?>
            <tr><td colspan="11" style="text-align:center;">Tidak ada data</td></tr>
        <?php else: ?>
            <?php foreach ($rows as $index => $row): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($row['participant_number']) ?></td>
                    <td><?= esc($row['full_name'] ?: '-') ?></td>
                    <td><?= esc($row['position']) ?></td>
                    <td><?= esc($row['work_unit'] ?: '-') ?></td>
                    <td><?= esc($row['location_label'] ?? '-') ?></td>
                    <td><?= esc($row['session_label'] ?? '-') ?></td>
                    <td><?= esc($row['birth_date']) ?></td>
                    <td><?= !empty($row['first_scanned_at']) ? 'Hadir' : 'Tidak Hadir' ?></td>
                    <td><?= esc($row['first_scanned_at'] ?: '-') ?></td>
                    <td><?= esc((string) ($row['scan_count'] ?? 0)) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
