<?php include(__DIR__ . "/head.php"); ?>
</head>
<?php 
include(__DIR__ . "/header.php"); 

$arrVerif = ['Rifky', 'Asri', 'Vika', 'Berli', 'Reyhan'];

?>

<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="page-description d-flex align-items-center">
                <div class="page-description-content flex-grow-1">
                    <h1>Check In Peserta Lomba Menggambar dan Mewarnai<br>Hari Hak Asasi Manusia Sedunia ke-77</h1>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($info)) { ?>
        <div class="alert <?=$info_type?>"><?=$info_pesan?></div>
    <?php } ?>

    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 text-center">
                            <img src="<?=base_url()?>uploads/foto/<?=$row->foto?>" style="height: 200px;">
                            <br><br>
                            <h4><?=$row->nama?></h4>
                            <h5>
                                <?=$row->sekolah?><br>
                            </h5>
                            <h3>
                                STATUS: <?=(is_null($row->notif_tolak))?"DITERIMA":"DITOLAK (".$arrVerif[($row->id%5)].")";?>
                            </h3>
                        </div> 
                    </div>
                    <?php 
                    if (is_null($row->notif_tolak)) { 
                        echo form_open('regist/do_checkin', ' autocomplete="off" data-toggle="validator" data-focus="false" novalidate="true" method="POST" '); 
                    ?>
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" name="id" value="<?=$row->id?>">
                            <input type="hidden" name="rc" value="<?=$row->registcode?>">
                            <label>Password Checkin</label>
                            <input class="form-control" type="number" name="key"><br>
                            <button type="submit" class="btn btn-primary btn-block btn-lg">CHECKIN</button>
                        </div>
                    </div>
                    <?php 
                        echo form_close();
                    } 
                    ?>
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