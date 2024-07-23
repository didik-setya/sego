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
}
