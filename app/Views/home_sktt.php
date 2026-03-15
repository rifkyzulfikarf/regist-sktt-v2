<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <meta name="description" content="Kartu Peserta Seleksi Kompetensi Tertulis Tambahan PPPK KemenHAM">
    <meta name="author" content="Rifky Zulfikar F">

    <meta property="og:site_name" content="Kartu Peserta Seleksi Kompetensi Tertulis Tambahan PPPK KemenHAM" />
	<meta property="og:title" content="Kartu Peserta Seleksi Kompetensi Tertulis Tambahan PPPK KemenHAM"/>
	<meta property="og:description" content="Kartu Peserta Seleksi Kompetensi Tertulis Tambahan PPPK KemenHAM" />

    <title>Kartu Peserta Seleksi Kompetensi Tertulis Tambahan PPPK KemenHAM</title>
    
    <link href="https://fonts.googleapis.com/css?family=Raleway:400,400i,600,700,700i&amp;subset=latin-ext" rel="stylesheet">
    <link href="<?=base_url()?>css/bootstrap.css" rel="stylesheet">
    <link href="<?=base_url()?>css/fontawesome-all.css" rel="stylesheet">
    <link href="<?=base_url()?>css/swiper.css" rel="stylesheet">
	<link href="<?=base_url()?>css/magnific-popup.css" rel="stylesheet">
	<link href="<?=base_url()?>css/styles.css" rel="stylesheet">
	
	<link rel="icon" href="<?=base_url()?>images/kemenham_icon.png">
</head>
<body data-spy="scroll" data-target=".fixed-top">
    
    <!-- Preloader -->
	<div class="spinner-wrapper">
        <div class="spinner">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
    <!-- end of preloader -->
    

    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom fixed-top">
        <a class="navbar-brand logo-image" href="<?=base_url()?>">
            <img src="<?=base_url()?>images/kemenham_lengkap.png" alt="alternative">
        </a>
        
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-awesome fas fa-bars"></span>
            <span class="navbar-toggler-awesome fas fa-times"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link page-scroll" href="#header">Home <span class="sr-only">(current)</span></a>
                </li>
            </ul>
        </div>
    </nav>


    <!-- Header -->
    <header id="header" class="header">
        <div class="header-content">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="text-container">
                            <h1><span class="turquoise">Unduh Kartu Peserta</span> Seleksi SKTT PPPK<br>Kementerian Hak Asasi Manusia</h1>
                        </div> <!-- end of text-container -->
                    </div> <!-- end of col -->
                    <div class="col-lg-6">
                        <div class="image-container">
                            <img src="<?=base_url()?>images/bg.png" alt="alternative" style="height: 450px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    <!-- end of header -->

    <!-- Pendaftaran -->
    <div id="pendaftaran" class="form-1">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="form-container">
                        <?php if (isset($info)) { ?>
                            <div class="alert <?=$info_type?>"><?=$info_pesan?></div>
                        <?php }?>

                        <br>
                        
                        <?=form_open('regist/printcard', ' autocomplete="off" id="requestForm" data-toggle="validator" data-focus="false" novalidate="true" method="POST" '); ?>
                            <div class="form-group">
                                <label>Jabatan</label>
                                <select class="form-control" id="jabatan" name="jabatan" required>
                                    <option value="">-- Pilih Jabatan --</option>
                                    <?php foreach ($jabatanList as $row) {
                                        echo "<option value='".$row->jabatan."'>".$row->jabatan."</option>";
                                    } ?>
                                </select>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <label>Nomor Peserta</label>
                                <input type="text" class="form-control" id="nomor_peserta" name="nomor_peserta" required>
                                <div class="help-block with-errors"></div>
                            </div>

                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>Tanggal</label>
                                        <select class="form-control" id="hari" name="hari" required>
                                            <option value="">-- Pilih Tanggal --</option>
                                            <?php for ($i = 1; $i <= 31; $i++) {
                                                echo "<option value='" . str_pad($i, 2, '0', STR_PAD_LEFT) . "'>" . str_pad($i, 2, '0', STR_PAD_LEFT) . "</option>";
                                            } ?>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Bulan</label>
                                        <select class="form-control" id="bulan" name="bulan" required>
                                            <option value="">-- Pilih Bulan --</option>
                                            <option value="01">Januari</option>
                                            <option value="02">Februari</option>
                                            <option value="03">Maret</option>
                                            <option value="04">April</option>
                                            <option value="05">Mei</option>
                                            <option value="06">Juni</option>
                                            <option value="07">Juli</option>
                                            <option value="08">Agustus</option>
                                            <option value="09">September</option>
                                            <option value="10">Oktober</option>
                                            <option value="11">November</option>
                                            <option value="12">Desember</option>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="col-md-4">
                                        <label>Tahun</label>
                                        <select class="form-control" id="tahun" name="tahun" required>
                                            <option value="">-- Pilih Tahun --</option>
                                            <?php $currentYear = date('Y'); for ($i = $currentYear; $i >= 1950; $i--) {
                                                echo "<option value='$i'>$i</option>";
                                            } ?>
                                        </select>
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>CAPTCHA</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?=$captcha['image']?>
                                    </div>
                                </div>
                                <label>Masukkan kode di atas</label>
                                <input type="text" class="form-control" id="captcha" name="captcha" required>
                                <div class="help-block with-errors"></div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block btn-lg">Unduh Kartu Peserta</button>
                            </div>
                        <?=form_close(); ?>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- end of Pendaftaran -->

    <!-- Copyright -->
    <div class="copyright">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <p class="p-small">Copyright ©2026 Biro Sumber Daya Manusia, Hukum, Organisasi dan Tata Laksana Kementerian Hak Asasi Manusia</p>
                </div>
            </div>
        </div>
    </div>
    <!-- end of copyright -->
    
    	
    <!-- Scripts -->
    <script src="<?=base_url()?>js/jquery.min.js"></script>
    <script src="<?=base_url()?>js/popper.min.js"></script>
    <script src="<?=base_url()?>js/bootstrap.min.js"></script>
    <script src="<?=base_url()?>js/jquery.easing.min.js"></script>
    <script src="<?=base_url()?>js/swiper.min.js"></script>
    <script src="<?=base_url()?>js/jquery.magnific-popup.js"></script>
    <script src="<?=base_url()?>js/scripts.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            
        });
    </script>
</body>
</html>