<?php
defined('BASEPATH') or exit('No direct script access allowed');
date_default_timezone_set('Asia/Jakarta');
class Ajax extends CI_Controller
{

    //ajax data kamar
    public function validation_kamar()
    {
        cek_ajax();
        $this->form_validation->set_rules('kamar', 'Kamar', 'required|trim');
        $this->form_validation->set_rules('price', 'Harga Kamar', 'required|trim|numeric');
        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_kamar' => form_error('kamar'),
                'err_price' => form_error('price'),
                'token' => $this->security->get_csrf_hash()
            ];
            echo json_encode($params);
            die;
        } else {
            $this->form_kamar();
        }
    }

    private function form_kamar()
    {
        $act = $this->input->post('act');
        $id = $this->input->post('id');

        switch ($act) {
            case 'add':

                $data = [
                    'no_kamar' => $this->input->post('kamar', true),
                    'km' => $this->input->post('km', true),
                    'status' => 1,
                    'last_update' => date('Y-m-d H:i:s'),
                    'price' => $this->input->post('price')
                ];
                $this->db->insert('kamar', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'type' => 'result',
                        'msg' => 'Data berhasil di tambahkan'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'type' => 'result',
                        'msg' => 'Data gagal di tambahkan'
                    ];
                }
                $arr_token = ['token' => $this->security->get_csrf_hash()];
                $output = array_merge($params, $arr_token);
                echo json_encode($output);
                die;
                break;

            case 'edit':

                $status = $this->input->post('status', true);

                if ($status == 0 || $status == 1 || $status == 4) {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
                        'status' => $status,
                        'price' => $this->input->post('price'),
                        'last_update' => date('Y-m-d H:i:s')
                    ];
                } else {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
                        'price' => $this->input->post('price'),
                        'last_update' => date('Y-m-d H:i:s')
                    ];
                }

                $this->db->where('id', $id)->update('kamar', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'type' => 'result',
                        'msg' => 'Data berhasil di edit'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'type' => 'result',
                        'msg' => 'Data gagal di edit'
                    ];
                }
                $arr_token = ['token' => $this->security->get_csrf_hash()];
                $output = array_merge($params, $arr_token);
                echo json_encode($output);
                die;


                break;
        }
    }

    public function delete_kamar()
    {
        cek_ajax();
        $id = $this->input->post('id');

        $this->db->where('id', $id)->delete('kamar');
        if ($this->db->affected_rows() > 0) {
            $params = [
                'status' => true,
                'msg' => 'Data berhasil di hapus'
            ];
        } else {
            $params = [
                'status' => false,
                'msg' => 'Data gagal di hapus'
            ];
        }
        $arr_token = ['token' => $this->security->get_csrf_hash()];
        $output = array_merge($params, $arr_token);
        echo json_encode($output);
    }
    //end data kamar



    //ajax data penghuni
    public function action_penghuni()
    {
        cek_ajax();
        $post = $this->input->post(null, true);

        $id = $post['id'];
        $act = $post['act'];
        switch ($act) {
            case 'add':

                $status = $post['status'];
                if ($status == 0) {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = '';
                    $tgl_keluar = '';
                    $status_kamar = 1;
                } else if ($status == 1) {
                    $tgl_pemesanan = date('Y-m-d');
                    $tgl_penempatan = '';
                    $tgl_keluar = '';
                    $status_kamar = 2;
                } else if ($status == 2) {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = date('Y-m-d');
                    $tgl_keluar = '';
                    $status_kamar = 3;
                } else if ($status == 3) {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = '';
                    $tgl_keluar = date('Y-m-d');
                    $status_kamar = 1;
                } else {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = '';
                    $tgl_keluar = '';
                    $status_kamar = 1;
                }

                $data = [
                    'nama_penghuni' => $post['name'],
                    'id_kamar' => $post['kamar'],
                    'status' => $post['status'],
                    'last_update' => date('Y-m-d H:i:s'),
                    'tgl_pemesanan' => $tgl_pemesanan,
                    'tgl_penempatan' => $tgl_penempatan,
                    'tgl_keluar' => $tgl_keluar
                ];

                $this->db->trans_begin();

                $this->db->insert('penghuni', $data);
                $this->db->set('status', $status_kamar)->where('id', $post['kamar'])->update('kamar');

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $params = [
                        'status' => false,
                        'msg' => 'Data gagal di tambahkan'
                    ];
                } else {
                    $this->db->trans_commit();
                    $params = [
                        'status' => true,
                        'msg' => 'Data berhasil di tambahkan'
                    ];
                }
                $arr_token = ['token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);

                die;
                break;
            case 'edit':
                $status = $post['status'];
                $get_data = $this->db->get_where('penghuni', ['id' => $id])->row();

                if ($status == 0) {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = '';
                    $tgl_keluar = '';
                    $status_kamar = 1;
                } else if ($status == 1) {
                    $tgl_pemesanan = date('Y-m-d');
                    $tgl_penempatan = '';
                    $tgl_keluar = '';
                    $status_kamar = 2;
                } else if ($status == 2) {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = date('Y-m-d');
                    $tgl_keluar = '';
                    $status_kamar = 3;
                } else if ($status == 3) {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = '';
                    $tgl_keluar = date('Y-m-d');
                    $status_kamar = 1;
                } else {
                    $tgl_pemesanan = '';
                    $tgl_penempatan = '';
                    $tgl_keluar = '';
                    $status_kamar = 1;
                }

                if ($post['kamar'] == '' || $post['kamar'] == null || $post['kamar'] == 0) {
                    $new_kamar = $get_data->id_kamar;
                } else {
                    if ($post['kamar'] == $get_data->id_kamar) {
                        $new_kamar = $get_data->id_kamar;
                    } else {
                        $new_kamar = $post['kamar'];
                    }
                }
                $data = [
                    'nama_penghuni' => $post['name'],
                    'id_kamar' => $new_kamar,
                    'status' => $post['status'],
                    'last_update' => date('Y-m-d H:i:s'),
                    'tgl_pemesanan' => $tgl_pemesanan,
                    'tgl_penempatan' => $tgl_penempatan,
                    'tgl_keluar' => $tgl_keluar
                ];

                // $this->db->trans_begin();

                if ($post['kamar'] != '' || $post['kamar'] != null || $post['kamar'] != 0) {
                    $this->db->set('status', 1)->where('id', $get_data->id_kamar)->update('kamar');
                    $this->db->set('status', $status_kamar)->where('id', $post['kamar'])->update('kamar');
                } else {
                    $this->db->set('status', $status_kamar)->where('id', $get_data->id_kamar)->update('kamar');
                }

                $this->db->where('id', $id)->update('penghuni', $data);

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $params = [
                        'status' => false,
                        'msg' => 'Data gagal di update'
                    ];
                } else {
                    $this->db->trans_commit();
                    $params = [
                        'status' => true,
                        'msg' => 'Data berhasil di update'
                    ];
                }


                $arr_token = ['token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;

                break;
            case 'delete':

                $get_data = $this->db->get_where('penghuni', ['id' => $id])->row();

                $this->db->trans_begin();

                $this->db->set('status', 1)->where('id', $get_data->id_kamar)->update('kamar');
                $this->db->delete('penghuni', ['id' => $id]);
                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    $params = ['status' => false, 'msg' => 'Penghuni gagal di hapus'];
                } else {
                    $this->db->trans_commit();
                    $params = ['status' => true, 'msg' => 'Penghuni berhasil di hapus'];
                }
                $arr_token = ['token' => $this->security->get_csrf_hash()];
                $output = array_merge($params, $arr_token);
                echo json_encode($output);
                die;
                break;
        }
    }
    //end data penghuni


    //ajax pembayaran
    public function valid_payment()
    {
        cek_ajax();
        $this->form_validation->set_rules('jumlah', 'Jumlah pembayaran', 'required|trim|numeric');
        $this->form_validation->set_rules('via', 'Via pembayaran', 'required|trim');

        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_jumlah' => form_error('jumlah'),
                'err_via' => form_error('via'),
                'token' => $this->security->get_csrf_hash()
            ];
            echo json_encode($params);
            die;
        } else {
            $this->act_payment();
        }
    }

    private function act_payment()
    {
        $post = $this->input->post(null, true);
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':

                $data = [
                    'id_penghuni' => $post['penghuni'],
                    'periode' => $post['periode'],
                    'tgl_bayar' => $post['tgl'],
                    'jml_bayar' => $post['jumlah'],
                    'via_pembayaran' => $post['via'],
                    'ket' => $post['ket']
                ];
                $this->db->insert('pembayaran', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'Pembayaran berhasil di tambahkan'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'Pembayaran gagal di tambahkan'
                    ];
                }
                $arr_token = ['type' => 'result', 'token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);

                echo json_encode($output);
                die;
                break;
            case 'edit':

                $data = [
                    'periode' => $post['periode'],
                    'tgl_bayar' => $post['tgl'],
                    'jml_bayar' => $post['jumlah'],
                    'via_pembayaran' => $post['via'],
                    'ket' => $post['ket']
                ];
                $this->db->where('id', $id)->update('pembayaran', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'Pembayaran berhasil di edit'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'Pembayaran gagal di edit'
                    ];
                }
                $arr_token = ['type' => 'result', 'token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);

                echo json_encode($output);

                die;
                break;
        }
    }

    public function delete_payment()
    {
        cek_ajax();
        $id = $this->input->post('id');

        $this->db->where('id', $id)->delete('pembayaran');
        if ($this->db->affected_rows() > 0) {
            $params = [
                'status' => true,
                'msg' => 'Pembayaran berhasil di hapus'
            ];
        } else {
            $params = [
                'status' => false,
                'msg' => 'Pembayaran gagal di hapus'
            ];
        }

        $arr_token = ['token' => $this->security->get_csrf_hash()];
        $output = array_merge($params, $arr_token);
        echo json_encode($output);
    }

    //end ajax pembayaran
}
