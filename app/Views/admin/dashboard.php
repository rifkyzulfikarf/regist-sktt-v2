<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin SKTT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
</head>
<body class="kh-theme">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">Dashboard Admin SKTT</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/import') ?>">Import Peserta</a>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/scan') ?>">Scan Kehadiran</a>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/report') ?>">Laporan</a>
            <a class="btn btn-danger btn-sm" href="<?= base_url('admin/logout') ?>">Logout</a>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h4 class="mb-4 kh-accent">Ringkasan Kehadiran</h4>
    <div class="row g-3">
        <div class="col-md-4"><div class="card kh-card"><div class="card-body"><div class="text-muted">Total Peserta</div><div class="display-6"><?= esc((string) $totalParticipants) ?></div></div></div></div>
        <div class="col-md-4"><div class="card kh-card"><div class="card-body"><div class="text-muted">Hadir</div><div class="display-6 text-success"><?= esc((string) $hadir) ?></div></div></div></div>
        <div class="col-md-4"><div class="card kh-card"><div class="card-body"><div class="text-muted">Tidak Hadir</div><div class="display-6 text-danger"><?= esc((string) $tidakHadir) ?></div></div></div></div>
    </div>
</div>
</body>
</html>
