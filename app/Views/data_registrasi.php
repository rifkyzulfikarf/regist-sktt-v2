<?php 
if ($ajuan->getNumRows() > 0) { 
	$peserta = $ajuan->getRow();
?>

<div class="row">
	<div class="col-3"></div>
	<div class="col-2">
		<img src="<?=base_url()?>uploads/foto/<?=$peserta->foto?>" height="150px">
	</div>
	<div class="col-4">
		<table width="100%">
			<tr>
				<td width="5%">NAMA</td>
				<td width="2%">:</td>
				<td><?=$peserta->nama?></td>
			</tr>
			<tr>
				<td width="5%">TTL</td>
				<td width="2%">:</td>
				<td><?=$peserta->tempat_lahir.' / '.date('d-m-Y', strtotime($peserta->tgl_lahir))?></td>
			</tr>
			<tr>
				<td width="5%">KATEGORI</td>
				<td width="2%">:</td>
				<td><?=strtoupper($peserta->jenis)?></td>
			</tr>
			<tr>
				<td width="5%">SEKOLAH</td>
				<td width="2%">:</td>
				<td><?=$peserta->sekolah?></td>
			</tr>
		</table>
		<br>
		<a href="<?=site_url('regist/printregist?rc='.$peserta->registcode)?>" class="btn btn-primary" target="_blank">Cetak Bukti Pendaftaran</a>
	</div>
	<div class="col-3"></div>
</div>

<?php } else { ?>

	Data tidak ditemukan

<?php } ?>