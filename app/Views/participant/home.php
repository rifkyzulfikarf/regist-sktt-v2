<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unduh Kartu Ujian SKTT PPPK KemenHAM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
</head>
<body class="kh-theme">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">SKTT PPPK KemenHAM</div>
        </div>
    </div>
</nav>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card kh-card">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo" style="height:56px;">
                        <div>
                            <h3 class="mb-1 kh-accent">Unduh Kartu Ujian SKTT</h3>
                            <p class="text-muted mb-0">Kementerian Hak Asasi Manusia Republik Indonesia</p>
                        </div>
                    </div>

                    <p class="text-muted mb-4">Isi Nomor Peserta, Jabatan, dan Tanggal Lahir untuk verifikasi data.</p>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
                    <?php endif; ?>

                    <form method="post" action="<?= base_url('card/generate') ?>" autocomplete="off">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label for="participant_number" class="form-label">Nomor Peserta</label>
                            <input type="text" class="form-control" id="participant_number" name="participant_number" required value="<?= esc(old('participant_number')) ?>">
                        </div>

                        <div class="mb-3">
                            <label for="position" class="form-label">Jabatan</label>
                            <select class="form-select" id="position" name="position" required>
                                <option value="">Pilih Jabatan</option>
                                <?php foreach ($positions as $item): ?>
                                    <option value="<?= esc($item['position']) ?>" <?= old('position') === $item['position'] ? 'selected' : '' ?>>
                                        <?= esc($item['position']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label for="birth_date" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" required value="<?= esc(old('birth_date')) ?>">
                        </div>

                        <div class="mb-4">
                            <label for="captcha" class="form-label">Captcha (5 karakter)</label>
                            <div class="mb-2">
                                <img src="<?= $captchaImage ?>" alt="Captcha" style="border:1px solid #c9d7ed; border-radius:8px;">
                            </div>
                            <input type="text" class="form-control" id="captcha" name="captcha" required maxlength="5" autocomplete="off" value="">
                            <small class="text-muted">Jika captcha sulit dibaca, muat ulang halaman untuk captcha baru.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Generate Kartu Ujian (PDF)</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
