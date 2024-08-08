<?php
defined('BASEPATH') or exit('No direct script access allowed');
class App_model extends CI_Model
{
    public function get_all_data_pelanggan($kost_id)
    {
        $this->db->select('
            kamar.no_kamar,
            kamar.km,
            kamar.price,
            penghuni.*
        ')
            ->from('penghuni')
            ->join('kamar', 'penghuni.id_kamar = kamar.id')
            ->where('penghuni.id_kost', $kost_id);

        $data = $this->db->get();
        return $data;
    }

    public function query_all_data_penghuni($year_a, $year_b)
    {
        $kost_id = $this->session->userdata('kost_id');

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
            ->where('penghuni.id_kost', $kost_id)
            ->where('penghuni.status !=', 0)
            ->where("penghuni.tgl_pemesanan BETWEEN '$year_a' AND '$year_b'")
            ->or_where("penghuni.tgl_penempatan BETWEEN '$year_a' AND '$year_b'")
            ->or_where("penghuni.tgl_keluar BETWEEN '$year_a' AND '$year_b'");

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

    public function get_data_pemasukan($periode = null, $id_pembayaran = null)
    {
        $kost_id = $this->session->userdata('kost_id');

        $this->db->select('
            penghuni.nama_penghuni,
            penghuni.tgl_penempatan,
            penghuni.status,
            penghuni.id AS id_penghuni,
            kamar.*,
            pembayaran.*,
            pembayaran.id AS id_pembayaran
        ')
            ->from('penghuni')
            ->join('kamar', 'penghuni.id_kamar = kamar.id')
            ->join('pembayaran', 'penghuni.id = pembayaran.id_penghuni')
            ->where('penghuni.id_kost', $kost_id);

        if ($periode) {
            $this->db->where('pembayaran.periode', $periode);
        }

        if ($id_pembayaran) {
            $this->db->where('pembayaran.id', $id_pembayaran);
        }

        $data = $this->db->get();
        return $data;
    }

    public function get_penghuni_not_pay($data_penghuni = null)
    {
        $status = [1, 2];
        $kost_id = $this->session->userdata('kost_id');
        $this->db->select('penghuni.*, kamar.*')
            ->from('penghuni')
            ->join('kamar', 'penghuni.id_kamar = kamar.id')
            ->where('penghuni.id_kost', $kost_id)
            ->where_in('penghuni.status', $status);
        if ($data_penghuni) {
            $this->db->where_not_in('penghuni.id', $data_penghuni);
        }
        $data = $this->db->get()->result();
        return $data;
    }
}
