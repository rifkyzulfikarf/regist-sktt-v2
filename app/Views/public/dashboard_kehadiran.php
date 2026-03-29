<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Publik Kehadiran SKTT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('css/sktt-brand.css') ?>" rel="stylesheet">
    <style>
        body.tv-mode .container { max-width: 100% !important; padding-left: 20px; padding-right: 20px; }
        body.tv-mode .kh-brand-title { font-size: 1.25rem; }
        body.tv-mode h4, body.tv-mode h5 { font-weight: 700; }
        body.tv-mode .table { font-size: 1.02rem; }
        body.tv-mode .card { border-radius: 10px; }
    </style>
</head>
<body class="kh-theme <?= !empty($isTvMode) ? 'tv-mode' : '' ?>">
<nav class="navbar navbar-expand-lg kh-navbar">
    <div class="container">
        <div class="d-flex align-items-center gap-3 flex-wrap">
            <img class="kh-logo" src="<?= base_url('images/kemenham_icon.png') ?>" alt="Logo KemenHAM">
            <div class="kh-brand-title">Dashboard Publik Kehadiran SKTT</div>
        </div>
        <div class="d-flex gap-2">
            <?php if (empty($isTvMode)): ?>
                <a href="<?= base_url('dashboard-kehadiran?tv=1') ?>" class="btn btn-outline-light btn-sm">Mode TV</a>
            <?php else: ?>
                <a href="<?= base_url('dashboard-kehadiran') ?>" class="btn btn-outline-light btn-sm">Mode Normal</a>
                <button type="button" id="fullscreenBtn" class="btn btn-warning btn-sm">Masuk Fullscreen</button>
            <?php endif; ?>
        </div>
    </div>
</nav>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h4 class="kh-accent mb-0">Ringkasan Kehadiran Peserta per Unit Kerja dan Sesi</h4>
        <small class="text-muted">Update: <?= esc($generatedAt) ?></small>
    </div>

    <?php if (empty($summary)): ?>
        <div class="alert alert-warning">Belum ada data peserta untuk ditampilkan.</div>
    <?php else: ?>
        <?php foreach ($summary as $unit): ?>
            <div class="card kh-card mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="mb-1"><?= esc($unit['organizer']) ?></h5>
                            <div class="text-muted">Total Peserta: <?= esc((string) $unit['total_participants']) ?> | Hadir: <?= esc((string) $unit['total_present']) ?> | Tidak Hadir: <?= esc((string) $unit['total_absent']) ?></div>
                        </div>
                    </div>

                    <?php foreach ($unit['locations'] as $loc): ?>
                        <div class="border rounded p-3 mb-3">
                            <div><strong>Lokasi Seleksi:</strong> <?= esc($loc['location']) ?></div>
                            <div class="mb-2"><strong>Alamat Seleksi:</strong> <?= esc($loc['address']) ?></div>
                            <div class="mb-2"><strong>Jumlah Sesi:</strong> <?= esc((string) count($loc['sessions'])) ?></div>

                            <div class="table-responsive">
                                <table class="table table-sm table-bordered align-middle mb-0">
                                    <thead>
                                        <tr>
                                            <th style="width:50px;">No</th>
                                            <th>Sesi</th>
                                            <th>Jam Sesi</th>
                                            <th>Jumlah Peserta</th>
                                            <th>Hadir</th>
                                            <th>Tidak Hadir</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $rowNo = 1; foreach ($loc['sessions'] as $session): ?>
                                            <tr>
                                                <td><?= $rowNo++ ?></td>
                                                <td><?= esc($session['session']) ?></td>
                                                <td><?= esc($session['time'] !== '' ? $session['time'] : '-') ?></td>
                                                <td><?= esc((string) $session['participants']) ?></td>
                                                <td class="text-success fw-bold">
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-success fw-bold detail-btn"
                                                        data-presence="hadir"
                                                        data-organizer="<?= esc($unit['organizer'], 'attr') ?>"
                                                        data-location="<?= esc($loc['location'], 'attr') ?>"
                                                        data-session="<?= esc($session['session'], 'attr') ?>"
                                                    >
                                                        <?= esc((string) $session['present']) ?>
                                                    </button>
                                                </td>
                                                <td class="text-danger fw-bold">
                                                    <button
                                                        type="button"
                                                        class="btn btn-link p-0 text-danger fw-bold detail-btn"
                                                        data-presence="tidak_hadir"
                                                        data-organizer="<?= esc($unit['organizer'], 'attr') ?>"
                                                        data-location="<?= esc($loc['location'], 'attr') ?>"
                                                        data-session="<?= esc($session['session'], 'attr') ?>"
                                                    >
                                                        <?= esc((string) $session['absent']) ?>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalTitle">Detail Peserta</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="detailLoading" class="text-muted">Memuat data...</div>
                <div class="table-responsive d-none" id="detailTableWrap">
                    <table class="table table-bordered table-sm align-middle mb-0">
                        <thead>
                            <tr>
                                <th style="width:60px;">No</th>
                                <th>Nomor Peserta</th>
                                <th>Nama</th>
                                <th>Jabatan</th>
                            </tr>
                        </thead>
                        <tbody id="detailTableBody"></tbody>
                    </table>
                </div>
                <div id="detailEmpty" class="alert alert-warning d-none mb-0">Tidak ada data peserta.</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto refresh setiap 60 detik
    setTimeout(function () {
        window.location.reload();
    }, 60000);

    // TV mode fullscreen controls
    (function () {
        const fullscreenBtn = document.getElementById('fullscreenBtn');
        if (!fullscreenBtn) return;

        fullscreenBtn.addEventListener('click', function () {
            const el = document.documentElement;
            if (el.requestFullscreen) {
                el.requestFullscreen().catch(function () {});
            }
        });

        // Coba masuk fullscreen otomatis saat mode TV aktif (browser bisa menolak)
        if (document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(function () {});
        }
    })();

    (function () {
        const modalEl = document.getElementById('detailModal');
        const modal = new bootstrap.Modal(modalEl);
        const titleEl = document.getElementById('detailModalTitle');
        const loadingEl = document.getElementById('detailLoading');
        const tableWrap = document.getElementById('detailTableWrap');
        const tableBody = document.getElementById('detailTableBody');
        const emptyEl = document.getElementById('detailEmpty');

        function setLoadingState() {
            loadingEl.classList.remove('d-none');
            tableWrap.classList.add('d-none');
            emptyEl.classList.add('d-none');
            tableBody.innerHTML = '';
        }

        function setRows(items) {
            if (!items.length) {
                loadingEl.classList.add('d-none');
                tableWrap.classList.add('d-none');
                emptyEl.classList.remove('d-none');
                return;
            }

            let html = '';
            items.forEach(function (item, index) {
                html += '<tr>'
                    + '<td>' + (index + 1) + '</td>'
                    + '<td>' + (item.participant_number || '-') + '</td>'
                    + '<td>' + (item.full_name || '-') + '</td>'
                    + '<td>' + (item.position || '-') + '</td>'
                    + '</tr>';
            });
            tableBody.innerHTML = html;
            loadingEl.classList.add('d-none');
            emptyEl.classList.add('d-none');
            tableWrap.classList.remove('d-none');
        }

        document.querySelectorAll('.detail-btn').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const organizer = btn.getAttribute('data-organizer') || '';
                const location = btn.getAttribute('data-location') || '';
                const session = btn.getAttribute('data-session') || '';
                const presence = btn.getAttribute('data-presence') || '';

                const presenceLabel = presence === 'hadir' ? 'Hadir' : 'Tidak Hadir';
                titleEl.textContent = 'Peserta ' + presenceLabel + ' - ' + organizer + ' - ' + session;
                setLoadingState();
                modal.show();

                const url = new URL('<?= base_url('dashboard-kehadiran/detail') ?>');
                url.searchParams.set('organizer', organizer);
                url.searchParams.set('location', location);
                url.searchParams.set('session', session);
                url.searchParams.set('presence', presence);

                fetch(url.toString(), { headers: { 'Accept': 'application/json' } })
                    .then(function (res) { return res.json(); })
                    .then(function (data) {
                        if (!data || data.success !== true) {
                            setRows([]);
                            return;
                        }
                        setRows(Array.isArray(data.items) ? data.items : []);
                    })
                    .catch(function () {
                        setRows([]);
                    });
            });
        });
    })();
</script>
</body>
</html>
