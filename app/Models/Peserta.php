<?php

namespace App\Models;

use CodeIgniter\Model;

class Peserta extends Model
{
    protected $table = 'peserta';
    protected $allowedFields = [
        'jabatan',
        'regcode',
        'formasi',
        'no_peserta',
        'nama',
        'pendidikan',
        'ket',
        'tgl_lahir',
        'tilok_cat',
        'tilok_sktt',
        'dt_regist'
    ];

    public function getDataPeserta($no_peserta, $jabatan, $tgl_lahir)
    {
        $builder = $this->db->table($this->table);
        $builder->where($this->table.'.no_peserta', $no_peserta);
        $builder->where($this->table.'.jabatan', $jabatan);
        $builder->where($this->table.'.tgl_lahir', $tgl_lahir);
        return $builder->select('regcode, no_peserta, nama, jabatan, formasi, tgl_lahir, pendidikan, ket, tilok_cat, tilok_sktt')->get();
    }

    public function getDistinctJabatan()
    {
        $builder = $this->db->table($this->table);
        return $builder->distinct()->select('jabatan')->where('jabatan IS NOT NULL')->get();
    }

    public function updateDtRegist($no_peserta, $dt_regist)
    {
        return $this->where('no_peserta', $no_peserta)->set('dt_regist', $dt_regist)->update();
    }

    public function getByTilokSktt($tilok_sktt)
    {
        return $this->where('tilok_sktt', $tilok_sktt)->findAll();
    }

}