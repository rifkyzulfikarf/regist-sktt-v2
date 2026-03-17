<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 10mm; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .card-shell { border: 2px solid #222; }
        .header { width: 100%; border-bottom: 2px solid #222; border-collapse: collapse; }
        .header td { padding: 6px; vertical-align: middle; }
        .logo { width: 44px; height: 44px; }
        .title-wrap { text-align: center; }
        .title-main { font-size: 20px; font-weight: 700; letter-spacing: 0.3px; }
        .title-sub { font-size: 13px; margin-top: 2px; }

        .body { width: 100%; border-collapse: collapse; }
        .body td { vertical-align: top; }
        .left-main { width: 78%; border-right: 2px solid #222; }
        .right-side { width: 22%; }

        .data-table { width: 100%; border-collapse: collapse; }
        .data-table td { border-bottom: 1px solid #222; border-right: 1px solid #222; padding: 4px 6px; }
        .data-table tr td:first-child { width: 28%; border-left: none; font-weight: 700; background: #f3f3f3; }
        .data-table tr td:last-child { border-right: none; }

        .right-wrap { padding: 8px; }
        .barcode-box { border: 1px solid #222; padding: 8px 6px; text-align: center; }
        .barcode-box img { width: 145px; height: auto; }
        .barcode-text { font-size: 8px; margin-top: 4px; word-break: break-all; }

        .section-bottom { width: 100%; border-top: 2px solid #222; border-collapse: collapse; }
        .section-bottom td { border-right: 2px solid #222; vertical-align: top; }
        .section-bottom td:last-child { border-right: none; }

        .notes { padding: 6px 8px; min-height: 88px; }
        .notes-title { font-weight: 700; margin-bottom: 4px; }
        .notes ol { margin: 0 0 0 14px; padding: 0; }
        .notes li { margin-bottom: 2px; }

        .sign { padding: 6px 8px; text-align: center; min-height: 88px; display: flex; flex-direction: column; }
        .sign-title { font-size: 15px; margin-bottom: auto; }

        .pin-row { width: 100%; border-top: 2px solid #222; }
        .pin-box { padding: 8px; font-size: 26px; font-weight: 700; letter-spacing: 1px; }
        .small-muted { font-size: 8px; color: #333; }
    </style>
</head>
<body>
<?php
    $getValueByKeywords = static function (array $source, array $keywords): string {
        foreach ($source as $k => $v) {
            $keyNorm = strtolower(trim((string) $k));
            foreach ($keywords as $keyword) {
                if ($keyNorm === strtolower(trim($keyword))) {
                    $val = trim((string) $v);
                    return $val === '' ? '-' : $val;
                }
            }
        }
        return '-';
    };

    $birthDateFormatted = '-';
    if (! empty($participant['birth_date'])) {
        $birthTimestamp = strtotime((string) $participant['birth_date']);
        if ($birthTimestamp !== false) {
            $birthDateFormatted = date('d-m-Y', $birthTimestamp);
        }
    }

    $hariValue = '';
    $tanggalValue = '';
    $sesiValue = '';
    $jamValue = '';
    $zonaValue = '';

    foreach ($participantData as $k => $v) {
        $keyNorm = strtolower(trim((string) $k));
        $val = trim((string) $v);

        if ($hariValue === '' && in_array($keyNorm, ['hari'])) {
            $hariValue = $val;
        }

        if ($tanggalValue === '' && in_array($keyNorm, ['tanggal', 'tgl', 'tanggal ujian', 'tgl ujian'])) {
            $tanggalValue = $val;
        }

        if ($sesiValue === '' && in_array($keyNorm, ['sesi', 'sesi ujian'])) {
            $sesiValue = $val;
        }

        if ($jamValue === '' && in_array($keyNorm, ['jam', 'jam ujian', 'waktu'])) {
            $jamValue = $val;
        }

        if ($zonaValue === '' && in_array($keyNorm, ['zona waktu', 'zona', 'timezone'])) {
            $zonaValue = $val;
        }
    }

    $hariTanggalGabung = '-';
    if ($hariValue !== '' || $tanggalValue !== '') {
        $hariTanggalGabung = trim($hariValue . ($hariValue !== '' && $tanggalValue !== '' ? ', ' : '') . $tanggalValue);
    }

    $sesiJamGabung = '-';
    if ($sesiValue !== '' || $jamValue !== '' || $zonaValue !== '') {
        $left = 'Sesi ' . trim($sesiValue);
        $right = 'Pukul ' . trim($jamValue . ($zonaValue !== '' ? ' ' . $zonaValue : ''));
        $sesiJamGabung = trim($left . ($left !== '' && $right !== '' ? ' / ' : '') . $right);
    }

    $pendidikanValue = $getValueByKeywords($participantData, ['pendidikan', 'kualifikasi pendidikan']);
    $formasiValue = $getValueByKeywords($participantData, ['formasi']);
    $unitKerjaPenyelenggaraValue = $getValueByKeywords($participantData, ['unit kerja penyelenggara']);
    $lokasiUjianValue = $getValueByKeywords($participantData, ['lokasi ujian', 'tilok sktt', 'tilok']);
    $alamatValue = $getValueByKeywords($participantData, ['alamat', 'alamat domisili']);
?>
<div class="card-shell">
    <table class="header">
        <tr>
            <td style="width:60px; text-align:center;">
                <?php if (! empty($logoBase64)): ?>
                    <img src="<?= $logoBase64 ?>" class="logo" alt="logo">
                <?php endif; ?>
            </td>
            <td class="title-wrap">
                <div class="title-main">KARTU PESERTA UJIAN SKTT PPPK</div>
                <div class="title-sub">KEMENTERIAN HAK ASASI MANUSIA REPUBLIK INDONESIA</div>
            </td>
            <td style="width:220px; text-align:center;">
                <img src="<?= $barcodeBase64 ?>" alt="Code128" style="width:200px;">
            </td>
        </tr>
    </table>

    <table class="body">
        <tr>
            <td class="left-main">
                <table class="data-table">
                    <tr><td>Nomor Peserta</td><td><?= esc($participant['participant_number']) ?></td></tr>
                    <tr><td>Nama</td><td><?= esc($participant['full_name'] ?: '-') ?></td></tr>
                    <tr><td>Tanggal Lahir</td><td><?= esc($birthDateFormatted) ?></td></tr>
                    <tr><td>Pendidikan</td><td><?= esc($pendidikanValue) ?></td></tr>
                    <tr><td>Jabatan</td><td><?= esc($participant['position']) ?></td></tr>
                    <tr><td>Formasi</td><td><?= esc($formasiValue) ?></td></tr>
                    <tr><td>Unit Kerja Penyelenggara</td><td><?= esc($unitKerjaPenyelenggaraValue) ?></td></tr>
                    <tr><td>Lokasi Ujian</td><td><?= esc($lokasiUjianValue) ?></td></tr>
                    <tr><td>Alamat</td><td><?= esc($alamatValue) ?></td></tr>
                    <tr><td>Hari / Tanggal</td><td><?= esc($hariTanggalGabung) ?></td></tr>
                    <tr><td>Sesi / Jam</td><td><?= esc($sesiJamGabung) ?></td></tr>
                </table>
            </td>
            <td class="right-side">
                <div class="right-wrap">
                    <div class="barcode-box">
                        <div style="font-weight:700; margin-bottom:6px;">BARCODE ABSENSI</div>
                        <img src="<?= $barcodeBase64 ?>" alt="Code128">
                        <div class="barcode-text"><?= esc($barcodePayload) ?></div>
                    </div>
                </div>
            </td>
        </tr>
    </table>

    <table class="section-bottom">
        <tr>
            <td style="width:60%;">
                <div class="notes">
                    <div class="notes-title">Keterangan:</div>
                    <ol>
                        <li>Kartu ini wajib dibawa saat pelaksanaan Seleksi Kompetensi Tambahan (Tes Tertulis).</li>
                        <li>Peserta wajib menunjukkan kartu dan identitas resmi saat registrasi kehadiran.</li>
                        <li>Peserta hadir paling lambat 60 (enam puluh) menit sebelum sesi dimulai.</li>
                        <li>Cetak Kartu Peserta dengan kualitas tinggi karena barcode pada Kartu Ujian digunakan untuk scan kehadiran oleh panitia.</li>
                    </ol>
                </div>
            </td>
            <td style="width:40%;">
                <div class="sign">
                    <div class="sign-title">Tanda Tangan Peserta</div>
                </div>
            </td>
        </tr>
    </table>

    <div class="pin-row">
        <div class="pin-box">PIN PESERTA : ____________</div>
    </div>
</div>
</body>
</html>
