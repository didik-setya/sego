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
        $data = [
            'title' => 'Data Kamar',
            'view' => 'v/data_kamar',
            'data' => $this->db->get('kamar')->result()
        ];
        $this->load->view('dashboard', $data);
    }

    public function data_penghuni()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $data = [
            'title' => 'Data Penghuni',
            'view' => 'v/data_penghuni',
            'kamar' => $this->db->where('status', 1)->get('kamar')->result(),
            'data' => $this->app->get_all_data_pelanggan()->result()
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
        $data = [
            'title' => 'Data Pengeluaran',
            'view' => 'v/data_pengeluaran',
            'data' => $this->db->order_by('tanggal', 'DESC')->get('pengeluaran')->result()
        ];
        $this->load->view('dashboard', $data);
    }

    public function setoran()
    {
        $url = $this->uri->segment(1);
        access_page($url);
        $data = [
            'title' => 'Data Setoran',
            'view' => 'v/data_setoran',
            'data' => $this->db->order_by('tanggal', 'DESC')->get('setoran')->result()
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
}
