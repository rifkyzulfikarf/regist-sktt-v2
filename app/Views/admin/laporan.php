<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Kehadiran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="<?php echo base_url('admin/dashboard'); ?>">Dashboard</a>
                <a class="nav-link" href="<?php echo base_url('admin/logout'); ?>">Logout</a>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Laporan Kehadiran</h4>
                    </div>
                    <div class="card-body">
                        <form method="get" class="mb-4">
                            <div class="row">
                                <div class="col-md-4">
                                    <label for="tilok_sktt" class="form-label">Pilih Tilok SKTT</label>
                                    <select class="form-select" id="tilok_sktt" name="tilok_sktt" required>
                                        <option value="">-- Pilih --</option>
                                        <?php foreach ($tilok_options as $option): ?>
                                            <option value="<?php echo $option['tilok_sktt']; ?>" <?php echo (isset($tilok_sktt) && $tilok_sktt == $option['tilok_sktt']) ? 'selected' : ''; ?>>
                                                <?php echo $option['tilok_sktt']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label><br>
                                    <button type="submit" class="btn btn-primary">Tampilkan</button>
                                </div>
                            </div>
                        </form>

                        <?php if (isset($peserta) && !empty($peserta)): ?>
                            <div class="mb-3">
                                <a href="<?php echo base_url('admin/printLaporan?tilok_sktt=' . urlencode($tilok_sktt)); ?>" class="btn btn-success" target="_blank">Cetak PDF</a>
                            </div>
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>No Peserta</th>
                                        <th>Nama</th>
                                        <th>Jabatan</th>
                                        <th>Tanggal Registrasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach ($peserta as $p): ?>
                                        <tr>
                                            <td><?php echo $no++; ?></td>
                                            <td><?php echo $p['no_peserta']; ?></td>
                                            <td><?php echo $p['nama']; ?></td>
                                            <td><?php echo $p['jabatan']; ?></td>
                                            <td><?php echo $p['dt_regist'] ? $p['dt_regist'] : '-'; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php elseif (isset($tilok_sktt)): ?>
                            <div class="alert alert-info">Tidak ada data untuk Tilok SKTT: <?php echo $tilok_sktt; ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>