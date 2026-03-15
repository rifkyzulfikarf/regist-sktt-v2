<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Peserta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
</head>
<body class="kh-theme">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">Import Data Peserta</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a class="btn btn-danger btn-sm" href="<?= base_url('admin/logout') ?>">Logout</a>
        </div>
    </div>
</nav>
<div class="container py-4">
    <div class="card kh-card">
        <div class="card-body">
            <h5 class="mb-2 kh-accent">Upload Excel (.xlsx)</h5>
            <p class="text-muted">Master peserta SKTT PPPK Kementerian Hak Asasi Manusia.</p>
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
            <?php endif; ?>
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data" action="<?= base_url('admin/import') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <input class="form-control" type="file" name="excel_file" accept=".xlsx" required>
                </div>
                <button class="btn btn-primary" type="submit">Import Peserta</button>
            </form>
            <hr>
            <small class="text-muted">Kolom wajib: Nomor Peserta, Jabatan, dan Tanggal Lahir.</small>
        </div>
    </div>
</div>
</body>
</html>
