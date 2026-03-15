<?php include(__DIR__ . "/head.php"); ?>

</head>
<?php include(__DIR__ . "/header.php"); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="page-description d-flex align-items-center">
                <div class="page-description-content flex-grow-1">
                    <h1>Tolak Peserta Lomba Menggambar dan Mewarnai<br>Hari Hak Asasi Manusia Sedunia ke-77</h1>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($info)) { ?>
        <div class="alert <?=$info_type?>"><?=$info_pesan?></div>
    <?php } ?>

    <div class="row">
        <div class="col">
            <h3>Peserta Mewarnai</h3>
            <br>
            <div class="card">
                <div class="card-body">
                    <table id="tbl-peserta-warnai" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th class="text-center" width="45%">Nama</th>
                                <th class="text-center" width="40%">Sekolah</th>
                                <th class="text-center" width="10%">Tgl Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $no = 1;
                            foreach ($peserta->getResult() as $row) { 
                                if ($row->jenis == 'mewarnai') {
                            ?>
                                    <tr>
                                        <td><?=$no?></td>
                                        <td>
                                            <?php 
                                                echo "<strong>".ucwords(strtolower($row->nama))."</strong><br>".
                                                "NIK: ".$row->nik."<br>".
                                                "TTL: ".$row->tempat_lahir." / ".date('d-M-Y', strtotime($row->tgl_lahir))."<br>".
                                                "WA Ortu: ".$row->waparent;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                echo "<strong>".$row->sekolah."</strong><br>".
                                                "Email: ".strtolower($row->emailsekolah)."<br>".
                                                "WA Sekolah: ".$row->wasekolah;
                                            ?>
                                        </td>
                                        <td><?=date('d-M-Y H:i', strtotime($row->tgl_daftar))?></td>
                                    </tr>
                            <?php 
                                    $no++;
                                }
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <br>
            <h3>Peserta Menggambar</h3>
            <br>
            <div class="card">
                <div class="card-body">
                    <table id="tbl-peserta-gambar" class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th class="text-center" width="5%">No</th>
                                <th class="text-center" width="10%">Verif</th>
                                <th class="text-center" width="45%">Nama</th>
                                <th class="text-center" width="40%">Sekolah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $arrVerif = ['Rifky', 'Asri', 'Vika', 'Berli', 'Reyhan'];

                            $no = 1;
                            foreach ($peserta->getResult() as $row) { 
                                if ($row->jenis == 'menggambar') {
                                    $waparent = trim($row->waparent);
                                    $waparent = str_replace(' ', '', $waparent);
                                    $waparent = str_replace('-', '', $waparent);
                                    $waparent = str_replace('+', '', $waparent);
                                    $prefix = substr($waparent, 0, 2);
                                    if ($prefix == '08') {
                                        $waparent = '62' . substr($waparent, 1);
                                    } elseif ($prefix == '62') {
                                        $waparent = $waparent;
                                    } else {
                                        $waparent = null;
                                    }

                                    $button_waparent = '';
                                    if (!is_null($waparent)) {
                                        $pesan = "Selamat siang, sehubungan dengan Surat Kepala Dinas Pendidikan Prov DKI Jakarta terkait daftar sekolah yang dapat mengikuti Lomba Mewarnai dan Menggambar Hari HAM 2025, maka bersama ini kami sampaikan bahwa ananda ".ucwords(strtolower($row->nama))." dari ".$row->sekolah." tidak dapat mengikuti lomba tersebut. Demikian, atas perhatian dan maklumnya kami sampaikan terima kasih.";
                                        $link = "https://wa.me/".$waparent."?text=".urlencode($pesan);
                                        $button_waparent = "<a href='".$link."' class='btn btn-primary btn-xs'>WA Ortu</a>";
                                    }

                                    $wasekolah = trim($row->wasekolah);
                                    $wasekolah = str_replace(' ', '', $wasekolah);
                                    $wasekolah = str_replace('-', '', $wasekolah);
                                    $wasekolah = str_replace('+', '', $wasekolah);
                                    $prefix = substr($wasekolah, 0, 2);
                                    if ($prefix == '08') {
                                        $wasekolah = '62' . substr($wasekolah, 1);
                                    } elseif ($prefix == '62') {
                                        $wasekolah = $wasekolah;
                                    } else {
                                        $wasekolah = null;
                                    }

                                    $button_wasekolah = '';
                                    if (!is_null($wasekolah)) {
                                        $pesan = "Selamat siang, sehubungan dengan Surat Kepala Dinas Pendidikan Prov DKI Jakarta terkait daftar sekolah yang dapat mengikuti Lomba Mewarnai dan Menggambar Hari HAM 2025, maka bersama ini kami sampaikan bahwa ananda ".ucwords(strtolower($row->nama))." dari ".$row->sekolah." tidak dapat mengikuti lomba tersebut. Demikian, atas perhatian dan maklumnya kami sampaikan terima kasih.";
                                        $link = "https://wa.me/".$waparent."?text=".urlencode($pesan);
                                        $button_wasekolah = "<a href='".$link."' class='btn btn-primary btn-xs'>WA Sekolah</a>";
                                    }
                            ?>
                                    <tr>
                                        <td><?=$no?></td>
                                        <td><?=$arrVerif[($row->id%5)]?></td>
                                        <td>
                                            <?php 
                                                echo "<strong>".ucwords(strtolower($row->nama))."</strong><br>".
                                                "NIK: ".$row->nik."<br>".
                                                "TTL: ".$row->tempat_lahir." / ".date('d-M-Y', strtotime($row->tgl_lahir))."<br>".
                                                "WA Ortu: ".$waparent."<br>".
                                                $button_waparent;
                                            ?>
                                        </td>
                                        <td>
                                            <?php 
                                                echo "<strong>".$row->sekolah."</strong><br>".
                                                "Email: ".strtolower($row->emailsekolah)."<br>".
                                                "WA Sekolah: ".$wasekolah."<br>".
                                                $button_wasekolah;
                                            ?>
                                        </td>
                                    </tr>
                            <?php 
                                    $no++;
                                }
                            } 
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include(__DIR__ . "/footer.php"); ?>

<script type="text/javascript">
    $(document).ready(function () {

        

    });
</script>

</body>
</html>