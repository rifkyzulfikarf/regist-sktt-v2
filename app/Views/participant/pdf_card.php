<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 22px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #0f172a; }
        .header { border-bottom: 3px solid #f3cb0a; padding-bottom: 10px; margin-bottom: 12px; }
        .brand-row { width: 100%; }
        .brand-left { width: 70px; vertical-align: top; }
        .brand-left img { width: 60px; }
        .brand-right { vertical-align: middle; }
        .title { font-size: 16px; font-weight: 700; color: #0e2a5a; line-height: 1.3; }
        .subtitle { font-size: 11px; color: #1f4f8d; }
        .section { border: 1px solid #c9d7ed; border-radius: 8px; padding: 10px; margin-top: 10px; }
        .section-title { font-weight: bold; margin-bottom: 7px; font-size: 12px; color: #0e2a5a; }
        table { width: 100%; border-collapse: collapse; }
        td { border: 1px solid #dde7f5; padding: 5px 7px; vertical-align: top; }
        td.label { width: 32%; background: #edf3fb; font-weight: bold; color: #0e2a5a; }
        .barcode { margin-top: 12px; text-align: center; border-top: 1px dashed #8ea8ce; padding-top: 10px; }
        .barcode img { width: 560px; max-width: 100%; height: auto; }
        .code { font-size: 9px; margin-top: 5px; word-wrap: break-word; color: #334155; }
        .footer { margin-top: 10px; font-size: 9px; color: #334155; }
    </style>
</head>
<body>
    <div class="header">
        <table class="brand-row">
            <tr>
                <td class="brand-left">
                    <?php if (!empty($logoBase64)): ?>
                        <img src="<?= $logoBase64 ?>" alt="Logo">
                    <?php endif; ?>
                </td>
                <td class="brand-right">
                    <div class="title">KARTU UJIAN SELEKSI KOMPETENSI TAMBAHAN (TES TERTULIS)</div>
                    <div class="subtitle">PPPK Kementerian Hak Asasi Manusia Republik Indonesia</div>
                </td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Data Verifikasi</div>
        <table>
            <tr><td class="label">Nomor Peserta</td><td><?= esc($participant['participant_number']) ?></td></tr>
            <tr><td class="label">Nama</td><td><?= esc($participant['full_name'] ?: '-') ?></td></tr>
            <tr><td class="label">Jabatan</td><td><?= esc($participant['position']) ?></td></tr>
            <tr><td class="label">Tanggal Lahir</td><td><?= esc($participant['birth_date']) ?></td></tr>
            <tr><td class="label">Unit Kerja</td><td><?= esc($participant['work_unit'] ?: '-') ?></td></tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">Seluruh Data Peserta dari Excel</div>
        <table>
            <?php foreach ($participantData as $key => $value): ?>
                <tr>
                    <td class="label"><?= esc($key) ?></td>
                    <td><?= esc((string) $value) !== '' ? esc((string) $value) : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <div class="barcode">
        <img src="<?= $barcodeBase64 ?>" alt="Code128">
        <div class="code">Payload terenkripsi: <?= esc($encryptedPayload) ?></div>
    </div>

    <div class="footer">
        Barcode Code128 dipakai untuk scan kehadiran peserta oleh admin unit kerja penyelenggara. Scan pertama menetapkan waktu registrasi resmi.
    </div>
</body>
</html>
