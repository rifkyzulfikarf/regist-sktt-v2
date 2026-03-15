<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cetak Bukti Pendaftaran</title>

    <style type="text/css">
        @page { size: A6 portrait; }

        body {
            font-size: 16pt;
            font-weight: normal;
            padding: 15px;
        }

        .table-antrian {
            border-collapse: collapse;
        }

        .table-antrian, th, td {
          border: 1px solid black;
          padding: 5px;
        }

        .page_break {
            page-break-before: always;
        }

        .table-no-border {
            border: none;
        }
    </style>
</head>

<body>
    <center>
        <img src="<?=base_url()?>images/kemenham_lengkap.png" style="height: 75px;">
        <h2 style="color: #006ad1;">LOMBA MENGGAMBAR DAN MEWARNAI<br>PERINGATAN HARI HAK ASASI MANUSIA SEDUNIA KE-77</h2>
        <br>
        <strong>Jabatan: <?=$row->nama?></strong>
        <br>
        <strong>Nomor Peserta: <?=$row->nik?></strong>
        <br>
        <strong>Tanggal Lahir: <?=$row->tgl_lahir?></strong>
        <br><br>
        <span style="color: #006ad1; font-size: 50px; font-weight: 300;">KATEGORI<br><?=strtoupper($row->jenis)?></span>
    </center>
    <img src="<?=$qr?>" style="height: 100px;">
    <br><br>
    <strong>SYARAT DAN KETENTUAN</strong>
    <small>
        <ul>
            <li>Peserta wajib membawa perlengkapan mewarnai dan menggambar sendiri;</li>
            <li>Harap capture/screenshot bukti pendaftaran ini dan ditunjukkan pada saat registrasi ulang (Check In);</li>
            <li>Peserta wajib hadir di lokasi lomba minimal 45 menit sebelum lomba dimulai;</li>
        </ul>
    </small>
</body>
</html>