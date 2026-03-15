<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="description" content="Aplikasi Pendaftaran Papsmear Gratis - Peringatan HDKD 2024">
    <meta name="author" content="Rifky Zulfikar F">

    <meta property="og:site_name" content="Aplikasi Pendaftaran Papsmear Gratis - Peringatan HDKD 2024" />
	<meta property="og:title" content="Aplikasi Pendaftaran Papsmear Gratis - Peringatan HDKD 2024"/>
	<meta property="og:description" content="Aplikasi Pendaftaran Papsmear Gratis - Peringatan HDKD 2024" />

    <title>Aplikasi Pendaftaran Pemeriksaan Papsmear Gratis</title>

    <link href="<?=base_url()?>css/bootstrap.css" rel="stylesheet">
    <link href="//cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" rel="stylesheet">
	
	<link rel="icon" href="<?=base_url()?>images/kumham.png">
</head>
<body>
    <div class="row">
        <div class="col-lg-12 p-4">
            <h4 class="text-center"><?=$title?></h4>
            <br>
            <table class="table table-bordered" id="tbl-ajuan">
                <thead>
                    <tr>
                        <th class="text-center" valign="middle">No</th>
                        <th class="text-center" valign="middle">Nama Peserta</th>
                        <th class="text-center" valign="middle">Identitas</th>
                        <th class="text-center">Tgl Lahir<br>Umur</th>
                        <th class="text-center" valign="middle">Telp</th>
                        <th class="text-center">Tinggi<br>(cm)</th>
                        <th class="text-center">Berat<br>(Kg)</th>
                        <th class="text-center" valign="middle">Jenis</th>
                        <th class="text-center" valign="middle">Jadwal</th>
                        <th class="text-center">Nama/NIP<br>Pendaftar</th>
                        <th class="text-center" valign="middle">Unit Kerja</th>
                        <th class="text-center" valign="middle">Tgl Daftar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $no = 1;
                    foreach ($ajuan->getResult() as $row) {
                        $dtLahir = new DateTime($row->tgl_lahir);
                        $dtSekarang = new DateTime();
                        $interval = $dtLahir->diff($dtSekarang);

                        if ($row->jenis_id == 1) {
                            $jenis = $row->nama_jenis;
                        } else {
                            $jenis = $row->nama_jenis.'<br>('.$row->hubungan.')';
                        }
                    ?>
                        <tr>
                            <td class="text-center"><?=$no?></td>
                            <td><?=$row->nama?></td>
                            <td><?='No KTP: '.$row->nik.'<br>No BPJS: '.$row->bpjs?></td>
                            <td class="text-center"><?=date('d-m-Y', strtotime($row->tgl_lahir)).'<br>'.$interval->y.' Tahun'?></td>
                            <td class="text-center"><?=$row->telp?></td>
                            <td><?=$row->tinggi?></td>
                            <td><?=$row->berat?></td>
                            <td class="text-center"><?=$jenis?></td>
                            <td class="text-center"><?=$row->hari.', '.date('d-m-Y', strtotime($row->tgl)).'<br>'.$row->nama_sesi.' Pukul '.date('H:i', strtotime($row->jam_awal)).' s/d '.date('H:i', strtotime($row->jam_akhir))?></td>
                            <td><?=$row->nama_pendaftar.'<br>NIP: '.$row->nip_pendaftar?></td>
                            <td><?=$row->unitkerja_pendaftar?></td>
                            <td class="text-center"><?=date('d-m-Y', strtotime($row->tgl_daftar))?></td>
                        </tr>
                    <?php
                        $no++;
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="<?=base_url()?>js/jquery.min.js"></script>
    <script type="text/javascript" src="//cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#tbl-ajuan').DataTable();
        });
    </script>
</body>
</html>