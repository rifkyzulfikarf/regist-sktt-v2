<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
</head>
<body class="kh-theme">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">Scan Kehadiran SKTT</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/report') ?>">Laporan</a>
            <a class="btn btn-danger btn-sm" href="<?= base_url('admin/logout') ?>">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card kh-card">
                <div class="card-body">
                    <h5 class="mb-3 kh-accent">Input / Scan Barcode</h5>
                    <form method="post" action="<?= base_url('admin/scan') ?>">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label" for="barcode_value">Barcode Value</label>
                            <input class="form-control" id="barcode_value" name="barcode_value" required autofocus>
                        </div>
                        <button class="btn btn-primary" type="submit">Proses Scan</button>
                    </form>
                    <small class="text-muted d-block mt-3">Gunakan scanner handheld (keyboard wedge) atau kamera browser di panel kanan.</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card kh-card">
                <div class="card-body">
                    <h5 class="mb-3 kh-accent">Scan via Kamera (BarcodeDetector API)</h5>
                    <button id="start-camera" class="btn btn-secondary btn-sm mb-3" type="button">Mulai Kamera</button>
                    <video id="video" class="w-100 border rounded" style="max-height: 300px; display:none;" playsinline></video>
                    <div id="camera-status" class="small text-muted mt-2">Kamera belum aktif.</div>
                </div>
            </div>
        </div>
    </div>

    <?php if (! empty($result)): ?>
        <?php
            $alertClass = 'alert-info';
            if ($result['type'] === 'success') $alertClass = 'alert-success';
            if ($result['type'] === 'warning') $alertClass = 'alert-warning';
            if ($result['type'] === 'error') $alertClass = 'alert-danger';
        ?>
        <div class="alert <?= $alertClass ?> mt-4">
            <strong><?= esc($result['title']) ?></strong><br>
            <?= esc($result['message']) ?>
            <?php if (! empty($result['participant'])): ?>
                <hr>
                <div>Nomor Peserta: <strong><?= esc($result['participant']['participant_number']) ?></strong></div>
                <div>Nama: <strong><?= esc($result['participant']['full_name'] ?: '-') ?></strong></div>
                <div>Jabatan: <strong><?= esc($result['participant']['position']) ?></strong></div>
                <div>Waktu Registrasi Pertama: <strong><?= esc($result['registeredAt'] ?? '-') ?></strong></div>
                <div>Jumlah Scan: <strong><?= esc((string) ($result['scanCount'] ?? 0)) ?></strong></div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
(() => {
    const startBtn = document.getElementById('start-camera');
    const video = document.getElementById('video');
    const statusEl = document.getElementById('camera-status');
    const input = document.getElementById('barcode_value');

    if (!('BarcodeDetector' in window)) {
        statusEl.textContent = 'Browser tidak mendukung BarcodeDetector API. Gunakan scanner handheld/manual input.';
        startBtn.disabled = true;
        return;
    }

    const detector = new BarcodeDetector({ formats: ['code_128'] });
    let timer = null;

    async function tick() {
        if (!video || video.readyState !== 4) return;

        try {
            const barcodes = await detector.detect(video);
            if (barcodes.length > 0) {
                const value = (barcodes[0].rawValue || '').trim();
                if (value) {
                    input.value = value;
                    document.forms[0].submit();
                }
            }
        } catch (err) {
            statusEl.textContent = 'Gagal mendeteksi barcode: ' + err;
        }
    }

    startBtn.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' }, audio: false });
            video.srcObject = stream;
            video.style.display = 'block';
            await video.play();
            statusEl.textContent = 'Kamera aktif. Arahkan barcode Code128 ke kamera.';
            if (timer) clearInterval(timer);
            timer = setInterval(tick, 400);
        } catch (err) {
            statusEl.textContent = 'Tidak dapat mengakses kamera: ' + err;
        }
    });
})();
</script>
</body>
</html>
