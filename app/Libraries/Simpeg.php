<?php 
namespace App\Libraries;

class Simpeg{

	const ws_url = 'https://register.kemenkumham.go.id/Api/pegawaibynip_simpeg';
	const token = 'd41d8cd98f00b204e9800998ecf8427e52bc44814bc711d09ba396ea413c02e1';

	public function getDataPegawai($nip)
	{
		$postdata = http_build_query(
		    array(
		        'token' => self::token,
		        'nip' => $nip
		    )
		);

		$opts = array('http' =>
		    array(
		        'method'  => 'POST',
		        'header'  => 'Content-Type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);

		$context  = stream_context_create($opts);
		$json_response = file_get_contents(self::ws_url, false, $context);

		$result = json_decode($json_response, true);

		return $result;
	}

}