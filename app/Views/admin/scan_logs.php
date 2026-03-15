<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Scan Admin Unit</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
</head>
<body class="kh-theme">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">Log Scan Barcode Admin Unit Kerja</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/logs/login') ?>">Log Login</a>
            <a class="btn btn-danger btn-sm" href="<?= base_url('admin/logout') ?>">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="card kh-card">
        <div class="card-body table-responsive">
            <table class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Waktu Scan</th>
                        <th>Username Admin</th>
                        <th>Unit Kerja Admin</th>
                        <th>Nomor Peserta</th>
                        <th>Nama Peserta</th>
                        <th>Status</th>
                        <th>Pesan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="8" class="text-center text-muted">Belum ada log scan admin unit kerja.</td></tr>
                    <?php else: ?>
                        <?php foreach ($rows as $idx => $row): ?>
                            <tr>
                                <td><?= $idx + 1 ?></td>
                                <td><?= esc($row['scanned_at']) ?></td>
                                <td><?= esc($row['username'] ?? '-') ?></td>
                                <td><?= esc($row['work_unit'] ?? '-') ?></td>
                                <td><?= esc($row['participant_number'] ?? '-') ?></td>
                                <td><?= esc($row['full_name'] ?? '-') ?></td>
                                <td><?= esc($row['status'] ?? '-') ?></td>
                                <td><?= esc($row['message'] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
