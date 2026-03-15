<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran SKTT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('adm-template/plugins/datatables/DataTables-1.10.20/css/jquery.dataTables.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
</head>
<body class="kh-theme">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">Laporan Kehadiran SKTT</div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/dashboard') ?>">Dashboard</a>
            <a class="btn btn-outline-light btn-sm" href="<?= base_url('admin/scan') ?>">Scan</a>
            <a class="btn btn-danger btn-sm" href="<?= base_url('admin/logout') ?>">Logout</a>
        </div>
    </div>
</nav>

<div class="container py-4">
    <?php if (! $isSuperAdmin): ?>
        <div class="alert alert-info">Mode Admin Unit Kerja aktif. Laporan dibatasi untuk unit kerja: <strong><?= esc($adminWorkUnit ?: '-') ?></strong>.</div>
    <?php endif; ?>

    <div class="card kh-card mb-3">
        <div class="card-body">
            <form method="get" action="<?= base_url('admin/report') ?>" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Unit Kerja</label>
                    <?php if ($isSuperAdmin): ?>
                        <select class="form-select" name="work_unit">
                            <option value="">Semua</option>
                            <?php foreach ($workUnits as $item): ?>
                                <option value="<?= esc($item['work_unit']) ?>" <?= $filters['work_unit'] === $item['work_unit'] ? 'selected' : '' ?>><?= esc($item['work_unit']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else: ?>
                        <input type="text" class="form-control" value="<?= esc($adminWorkUnit ?: '-') ?>" readonly>
                        <input type="hidden" name="work_unit" value="<?= esc($adminWorkUnit) ?>">
                    <?php endif; ?>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Jabatan</label>
                    <select class="form-select" name="position">
                        <option value="">Semua</option>
                        <?php foreach ($positions as $item): ?>
                            <option value="<?= esc($item['position']) ?>" <?= $filters['position'] === $item['position'] ? 'selected' : '' ?>><?= esc($item['position']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select class="form-select" name="status">
                        <option value="all" <?= $filters['status'] === 'all' ? 'selected' : '' ?>>Semua</option>
                        <option value="hadir" <?= $filters['status'] === 'hadir' ? 'selected' : '' ?>>Hadir</option>
                        <option value="tidak_hadir" <?= $filters['status'] === 'tidak_hadir' ? 'selected' : '' ?>>Tidak Hadir</option>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button class="btn btn-primary" type="submit">Terapkan Filter</button>
                    <a class="btn btn-success" href="<?= base_url('admin/report/pdf?' . http_build_query($filters)) ?>" target="_blank">Export PDF</a>
                    <a class="btn btn-outline-success" href="<?= base_url('admin/report/csv?' . http_build_query($filters)) ?>">Export CSV</a>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-md-4"><div class="card kh-card"><div class="card-body"><small>Total Data</small><h4><?= esc((string) $summary['total']) ?></h4></div></div></div>
        <div class="col-md-4"><div class="card kh-card"><div class="card-body"><small>Hadir</small><h4 class="text-success"><?= esc((string) $summary['hadir']) ?></h4></div></div></div>
        <div class="col-md-4"><div class="card kh-card"><div class="card-body"><small>Tidak Hadir</small><h4 class="text-danger"><?= esc((string) $summary['tidak_hadir']) ?></h4></div></div></div>
    </div>

    <div class="card kh-card">
        <div class="card-body table-responsive">
            <table id="reportTable" class="table table-striped table-bordered align-middle">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Nomor Peserta</th>
                    <th>Nama</th>
                    <th>Jabatan</th>
                    <th>Unit Kerja</th>
                    <th>Tgl Lahir</th>
                    <th>Status</th>
                    <th>Waktu Registrasi Pertama</th>
                    <th>Jumlah Scan</th>
                </tr>
                </thead>
                <tbody>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="9" class="text-center text-muted">Tidak ada data</td></tr>
                <?php else: ?>
                    <?php foreach ($rows as $index => $row): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= esc($row['participant_number']) ?></td>
                            <td><?= esc($row['full_name'] ?: '-') ?></td>
                            <td><?= esc($row['position']) ?></td>
                            <td><?= esc($row['work_unit'] ?: '-') ?></td>
                            <td><?= esc($row['birth_date']) ?></td>
                            <td><?= !empty($row['first_scanned_at']) ? '<span class="badge text-bg-success">Hadir</span>' : '<span class="badge text-bg-danger">Tidak Hadir</span>' ?></td>
                            <td><?= esc($row['first_scanned_at'] ?: '-') ?></td>
                            <td><?= esc((string) ($row['scan_count'] ?? 0)) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="<?= base_url('js/jquery.min.js') ?>"></script>
<script src="<?= base_url('adm-template/plugins/datatables/DataTables-1.10.20/js/jquery.dataTables.min.js') ?>"></script>
<script>
$(function () {
    $('#reportTable').DataTable({
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'Semua']],
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [0] }
        ],
        language: {
            search: 'Cari:',
            lengthMenu: 'Tampilkan _MENU_ data',
            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            zeroRecords: 'Data tidak ditemukan',
            paginate: {
                first: 'Awal',
                last: 'Akhir',
                next: 'Berikut',
                previous: 'Sebelumnya'
            }
        }
    });
});
</script>
</body>
</html>
