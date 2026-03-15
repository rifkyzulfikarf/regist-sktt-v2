<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 10mm;
            font-size: 10pt;
        }
        h1 { 
            text-align: center; 
            font-size: 16pt;
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse;
            margin-top: 20px;
        }
        th { 
            background-color: #f0f0f0;
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
            font-weight: bold;
            font-size: 9pt;
        }
        td { 
            border: 1px solid #000;
            padding: 6px;
            font-size: 8pt;
        }
        .col-no { width: 5%; text-align: center; }
        .col-nbr { width: 15%; text-align: left; }
        .col-nama { width: 20%; text-align: left; }
        .col-jabatan { width: 45%; text-align: left; }
        .col-tgl { width: 15%; text-align: center; }
    </style>
</head>
<body>
    <h1>Laporan Kehadiran - <?php echo htmlspecialchars($tilok_sktt); ?></h1>
    <table>
        <thead>
            <tr>
                <th class="col-no">No</th>
                <th class="col-nbr">No Peserta</th>
                <th class="col-nama">Nama</th>
                <th class="col-jabatan">Jabatan</th>
                <th class="col-tgl">Tgl Registrasi</th>
            </tr>
        </thead>
        <tbody>
            <?php $no = 1; ?>
            <?php foreach ($peserta as $p): ?>
                <?php 
                    $tanggal = '-';
                    if ($p['dt_regist']) {
                        $dt = new \DateTime($p['dt_regist']);
                        $tanggal = $dt->format('d-m-Y H:i:s');
                    }
                ?>
                <tr>
                    <td class="col-no"><?php echo $no++; ?></td>
                    <td class="col-nbr"><?php echo htmlspecialchars($p['no_peserta']); ?></td>
                    <td class="col-nama"><?php echo htmlspecialchars(substr($p['nama'], 0, 35)); ?></td>
                    <td class="col-jabatan"><?php echo htmlspecialchars(substr($p['jabatan'], 0, 55)); ?></td>
                    <td class="col-tgl"><?php echo $tanggal; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>