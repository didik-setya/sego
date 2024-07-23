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
}
