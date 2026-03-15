<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;
use App\Libraries\Recaptcha;
use App\Libraries\Simpeg;

use App\Models\Jadwal;
use App\Models\Sesi;
use App\Models\Peserta;

class HomeController extends BaseController
{
    protected $recaptcha;
    protected $simpeg;

    function __construct()
    {
        $this->recaptcha = new \App\Libraries\Recaptcha();
        $this->simpeg = new \App\Libraries\Simpeg();
    }

    public function index()
    {
        $data = array();
        return view('landing', $data);
    }

    public function papsmear()
    {
        $data = array();
        
        $data['recaptcha'] = $this->recaptcha->getWidget();
        $data['recaptchaScript'] = $this->recaptcha->getScriptTag();

        $jadwal = new Jadwal;
        $data['jadwal'] = $jadwal->getData();
        $data['m_jadwal'] = $jadwal;

        $sesi = new Sesi;
        $data['sesi'] = $sesi->getData();

        $peserta = new Peserta;
        $data['m_peserta'] = $peserta;

        if (session()->getFlashdata('info') !== null) {
            $data['info'] = session()->getFlashdata('info');
            $data['info_type'] = session()->getFlashdata('info_type');
            $data['info_pesan'] = session()->getFlashdata('info_pesan');
        }

        return view('home', $data);
    }

    public function getDataPegawai()
    {
        $result = array();
        $row = array();

        if (!isset($_POST['nip']) || $_POST['nip'] == '') {
            $row['hasil'] = '0';
            $row['msg'] = 'Harap masukkan NIP terlebih dahulu!';
            array_push($result, $row);
            echo json_encode($result);
            exit;
        }

        $pegawai = $this->simpeg->getDataPegawai(
            $this->request->getPost('nip')
        );

        if ($pegawai['status'][0]['StatusAda'] == '0') {
            $row['hasil'] = '1';
            $row['msg'] = 'Data pegawai tidak ditemukan di SIMPEG.';
        } else {
            $row['hasil'] = '2';
            $row['nama'] = ucwords(strtolower($pegawai['data'][0]['nama_pegawai']));
            $row['kode_satker'] = ucwords(strtolower($pegawai['data'][0]['kode_satker']));
            $row['unitkerja'] = ucwords(strtolower($pegawai['data'][0]['nama_satker']));

            if ($this->request->getPost('nip') == '199412222019012001') {
                $row['msg'] = 'Hai, ' . $row['nama'];
            } else {
                $row['msg'] = '';
            }
        }

        array_push($result, $row);
        echo json_encode($result);
        exit;
    }

    public function simpanRegistrasi()
    {
        $rules = [
            'nip' => 'required',
            'nama' => 'required',
            'kodesatker' => 'required',
            'unitkerja' => 'required',
            'jenis' => 'required',
            'tgl_lahir' => 'required',
            'telp' => 'required',
            'ktp' => 'required',
            'bpjs' => 'required',
            'tinggi' => 'required',
            'berat' => 'required',
            'jadwal' => 'required',
            'sesi' => 'required',
            'g-recaptcha-response' => 'required',
        ];

        $formData = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($formData, $rules)) {
            session()->setFlashdata('info', true);
            session()->setFlashdata('info_type', 'alert-warning');
            session()->setFlashdata('info_pesan', validation_list_errors());

            return redirect()->to(site_url('home/papsmear').'#pendaftaran');
        }

        $response = $this->recaptcha->verifyResponse($this->request->getPost('g-recaptcha-response'));

        if (isset($response['success']) and $response['success'] === true) {

            $jadwal = new Jadwal;
            $peserta = new Peserta;

            $checkPeserta = $peserta->getDatabyNIK($this->request->getPost('ktp'))->getNumRows();
            if ($checkPeserta > 0) {
                session()->setFlashdata('info', true);
                session()->setFlashdata('info_type', 'alert-warning');
                session()->setFlashdata('info_pesan', 'Peserta dengan NIK '.$this->request->getPost('ktp').' sudah terdaftar!');

                return redirect()->to(site_url('home/papsmear').'#pendaftaran');
            }

            $kuota = $jadwal->getDatabyID($this->request->getPost('jadwal'))->getRow()->kuota;
            $jumlah_pendaftar = $peserta->countPendaftarbyJadwal($this->request->getPost('jadwal'))->getRow()->jumlah;

            if ($jumlah_pendaftar >= $kuota) {
                session()->setFlashdata('info', true);
                session()->setFlashdata('info_type', 'alert-warning');
                session()->setFlashdata('info_pesan', 'Maaf, kuota harian untuk tanggal yang dipilih sudah penuh.');

                return redirect()->to(site_url('home/papsmear').'#pendaftaran');
            }

            if ($this->request->getPost('jenis') == '1') {
                $nama = $this->request->getPost('nama');
                $hubungan = '';
            } else {
                $nama = $this->request->getPost('nama_keluarga');
                $hubungan = $this->request->getPost('hubungan');
            }

            $peserta->lockTable();
            $data = [
                'nama' => $nama,
                'nik' => $this->request->getPost('ktp'),
                'bpjs' => $this->request->getPost('bpjs'),
                'tgl_lahir' => $this->request->getPost('tgl_lahir'),
                'telp' => $this->request->getPost('telp'),
                'tinggi' => $this->request->getPost('tinggi'),
                'berat' => $this->request->getPost('berat'),
                'jenis_id' => $this->request->getPost('jenis'),
                'hubungan' => $hubungan,
                'jadwal_id' => $this->request->getPost('jadwal'),
                'sesi_id' => $this->request->getPost('sesi'),
                'nama_pendaftar' => $this->request->getPost('nama'),
                'nip_pendaftar' => $this->request->getPost('nip'),
                'kodesatker_pendaftar' => $this->request->getPost('kodesatker'),
                'unitkerja_pendaftar' => $this->request->getPost('unitkerja'),
                'tgl_daftar' => date('Y-m-d H:i:s')
            ];

            $peserta->saveRegist($data);
            $peserta->unlockTable();

            session()->setFlashdata('info', true);
            session()->setFlashdata('info_type', 'alert-success');

            if ($this->request->getPost('nip') == '199412222019012001') {
                session()->setFlashdata('info_pesan', 'Simpan data berhasil. Semoga sehat selalu.');
            } else {
                session()->setFlashdata('info_pesan', 'Simpan data berhasil!');
            }

            return redirect()->to(site_url('home/papsmear').'#pendaftaran');

        } else {
            if (!$this->validateData($formData, $rules)) {
                session()->setFlashdata('info', true);
                session()->setFlashdata('info_type', 'alert-warning');
                session()->setFlashdata('info_pesan', 'Captcha gagal!');

                return redirect()->to(site_url('home/papsmear').'#pendaftaran');
            }
        }

    }

    public function getDataRegistrasi()
    {
        $rules = [
            'nip' => 'required',
            'g-recaptcha-response' => 'required',
        ];

        $formData = $this->request->getPost(array_keys($rules));

        if (!$this->validateData($formData, $rules)) {
            echo validation_list_errors();
        }

        $response = $this->recaptcha->verifyResponse($this->request->getPost('g-recaptcha-response'));

        if (isset($response['success']) and $response['success'] === true) {

            $peserta = new Peserta;
            $data['ajuan'] = $peserta->getDatabyNIPPendaftar($this->request->getPost('nip'));
            return view('data_registrasi', $data);

        } else {
            echo 'Captcha gagal! Harap muat ulang kembali halaman ini.';
        }

    }

    public function getRekap()
    {
        if (is_numeric($this->request->getGet('jadwal'))) {
            $peserta = new Peserta;
            $ajuan = $peserta->getRekap($this->request->getGet('jadwal'));
            
            if ($ajuan->getNumRows() > 0) {
                $data['title'] = 'Rekap Peserta Pemeriksaan Papsmear Tanggal '.date('d-m-Y', strtotime($ajuan->getRow()->tgl));
            } else {
                $data['title'] = 'Rekap Peserta Pemeriksaan Papsmear';
            }

            $data['ajuan'] = $ajuan;
            return view('rekap', $data);
        }
    }

}
