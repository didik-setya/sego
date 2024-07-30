<?php
defined('BASEPATH') or exit('No direct script access allowed');
class App_model extends CI_Model
{
    public function get_all_data_pelanggan()
    {
        $this->db->select('
            kamar.no_kamar,
            kamar.km,
            penghuni.*
        ')
            ->from('penghuni')
            ->join('kamar', 'penghuni.id_kamar = kamar.id');

        $data = $this->db->get();
        return $data;
    }

    public function query_all_data_penghuni($status = null, $month = null, $year = null)
    {
        $this->db->select('
        penghuni.id AS id_penghuni,
        penghuni.nama_penghuni,
        penghuni.status AS status_penghuni,
        penghuni.tgl_penempatan,
        kamar.no_kamar,
        kamar.km,
        kamar.price,
        kamar.status AS status_kamar
        ')

            ->from('kamar')
            ->join('penghuni', 'kamar.id = penghuni.id_kamar', 'RIGHT OUTER')
            ->where('penghuni.status', $status);


        $data = $this->db->get();
        return $data;
    }


    public function get_access_kost($id)
    {
        $this->db->select('kost.*')
            ->from('kost')
            ->join('access_kost', 'kost.id = access_kost.id_kost')
            ->where('access_kost.id_user', $id);
        $data = $this->db->get()->result();
        return $data;
    }
}
