<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Dashboard extends CI_Controller
{
    public function index()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $data = [
            'title' => 'Dashboard',
            'view' => 'v/dashboard'
        ];
        $this->load->view('dashboard', $data);
    }

    public function data_kamar()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $kost_id = $this->session->userdata('kost_id');
        $data = [
            'title' => 'Data Kamar',
            'view' => 'v/data_kamar',
            'data' => $this->db->where('id_kost', $kost_id)->get('kamar')->result()
        ];
        $this->load->view('dashboard', $data);
    }

    public function data_penghuni()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $kost_id = $this->session->userdata('kost_id');
        $data = [
            'title' => 'Data Penghuni',
            'view' => 'v/data_penghuni',
            'kamar' => $this->db->where(['status' =>  1, 'id_kost' => $kost_id])->get('kamar')->result(),
            'data' => $this->app->get_all_data_pelanggan($kost_id)->result()
        ];
        $this->load->view('dashboard', $data);
    }

    public function payment()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $id = $this->input->get('id');
        $penghuni = $this->db->get_where('penghuni', ['md5(sha1(id))' => $id])->row();
        if ($penghuni) {

            $data = [
                'title' => 'Data Pembayaran',
                'view' => 'v/data_payment',
                'penghuni' => $penghuni,
                'data' => $this->db->get_where('pembayaran', ['id_penghuni' => $penghuni->id])->result()
            ];

            $this->load->view('dashboard', $data);
        } else {
            redirect('penghuni');
        }
    }

    public function pengeluaran()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $kost_id = $this->session->userdata('kost_id');
        $data = [
            'title' => 'Data Pengeluaran',
            'view' => 'v/data_pengeluaran',
            'data' => $this->db->order_by('tanggal', 'DESC')->get_where('pengeluaran', ['id_kost' => $kost_id])->result()
        ];
        $this->load->view('dashboard', $data);
    }

    public function setoran()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $kost_id = $this->session->userdata('kost_id');
        $data = [
            'title' => 'Data Setoran',
            'view' => 'v/data_setoran',
            'data' => $this->db->order_by('tanggal', 'DESC')->get_where('setoran', ['id_kost' => $kost_id])->result()
        ];
        $this->load->view('dashboard', $data);
    }

    public function report()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $data = [
            'title' => 'Laporan',
            'view' => 'v/report',
        ];
        $this->load->view('dashboard', $data);
    }

    public function data_kost()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $data = [
            'title' => 'Data Kost',
            'view' => 'v/data_kost',
            'data' => $this->db->get('kost')->result()
        ];
        $this->load->view('dashboard', $data);
    }


    public function access_kost()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $data = [
            'title' => 'User & Access Kost',
            'view' => 'v/access_kost',
            'data' => $this->db->get('user')->result(),

        ];
        $this->load->view('dashboard', $data);
    }


    public function settings()
    {
        $username = $this->session->userdata('username');
        $user = $this->db->get_where('user', ['username' => $username])->row();
        $data = [
            'title' => 'Settings',
            'view' => 'v/settings',
            'data' => $user

        ];
        $this->load->view('dashboard', $data);
    }


    public function transaction()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $kost_id = $this->session->userdata('kost_id');
        $data = [
            'title' => 'Transaksi',
            'view' => 'v/transaction',
            'penghuni' => $this->app->get_all_data_pelanggan($kost_id)->result()
        ];
        $this->load->view('dashboard', $data);
    }
}
