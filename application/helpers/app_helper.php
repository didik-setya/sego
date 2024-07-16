<?php
global $config;
$config = include 'config.php';

function cek_ajax()
{
    $t = get_instance();
    if (!$t->input->is_ajax_request()) {
        exit('No direct script access allowed');
    }
}

function access_page($url = null)
{
    global $config;
    $t = get_instance();
    $user = $t->session->userdata('username');
    $get_user = $t->db->get_where('user', ['username' => $user])->row();

    $menu = $config['menu'];

    if ($get_user && $get_user->status == 1) {

        foreach ($menu as $m) {
            $access = $m['access'];
            $murl = $m['url'];

            if ($url == $murl) {
                $ar = in_array($get_user->role, $access);
                if ($ar == false) {
                    exit('You died');
                }
            }
        }
    } else {
        redirect(base_url('login/logout'));
    }
}

function cek_tgl($tgl = null)
{
    if ($tgl != null || $tgl != '' || $tgl != '0000-00-00') {
        $t = date_create($tgl);
        $st = date_format($t, 'd/m/Y');
        echo $st;
    } else {
        echo '-';
    }
}
