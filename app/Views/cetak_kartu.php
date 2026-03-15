<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .card { border: 1px solid #000; padding: 20px; width: 100%; }
        .title { text-align: center; font-weight: bold; font-size: 16px; margin-bottom: 20px; }
        .field { margin-bottom: 10px; display: flex; }
        .label { width: 150px; font-weight: bold; }
        .value { flex: 1; }
        .barcode { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #000; }
        .barcode img { max-width: 200px; height: auto; }
    </style>
</head>
<body>
    <div class="card">
        <div class="title">KARTU PESERTA</div>
        <div class="field">
            <div class="label">Jabatan:</div>
            <div class="value"><?php echo htmlspecialchars($row->jabatan); ?></div>
        </div>
        <div class="field">
            <div class="label">Nomor Peserta:</div>
            <div class="value"><?php echo htmlspecialchars($row->no_peserta); ?></div>
        </div>
        <div class="field">
            <div class="label">Nama:</div>
            <div class="value"><?php echo htmlspecialchars($row->nama); ?></div>
        </div>
        <div class="field">
            <div class="label">Pendidikan:</div>
            <div class="value"><?php echo htmlspecialchars($row->pendidikan); ?></div>
        </div>
        <div class="field">
            <div class="label">Formasi:</div>
            <div class="value"><?php echo htmlspecialchars($row->formasi); ?></div>
        </div>
        <div class="field">
            <div class="label">Lokasi Ujian:</div>
            <div class="value"><?php echo htmlspecialchars(isset($row->tilok_sktt) ? $row->tilok_sktt : ''); ?></div>
        </div>
        <div class="field">
            <div class="label">Tanggal Ujian:</div>
            <div class="value"><?php echo htmlspecialchars(isset($row->tilok_sktt) ? $row->tilok_sktt : ''); ?></div>
        </div>
        <div class="field">
            <div class="label">Sesi:</div>
            <div class="value"><?php echo htmlspecialchars(isset($row->tilok_sktt) ? $row->tilok_sktt : ''); ?></div>
        </div>
        <div class="field">
            <div class="label">Tanggal Lahir:</div>
            <div class="value"><?php echo htmlspecialchars($tgl_lahir); ?></div>
        </div>
        <div class="barcode">
            <img src="<?php echo $barcodeBase64; ?>" alt="Barcode">
        </div>
    </div>
</body>
</html>