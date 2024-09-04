<?php

use PhpOffice\PhpSpreadsheet\Calculation\MathTrig\Base;

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
        $kost_id = $this->session->userdata('kost_id');
        $act = $this->input->post('act');
        $id = $this->input->post('id');

        switch ($act) {
            case 'add':

                $data = [
                    'no_kamar' => $this->input->post('kamar', true),
                    'km' => $this->input->post('km', true),
                    'status' => 1,
                    'last_update' => date('Y-m-d H:i:s'),
                    'price' => $this->input->post('price'),
                    'lokasi_gedung' => $this->input->post('lokasi', true),
                    'id_kost' => $kost_id
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

                if ($status != 0 || $status != null || $status != '') {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
                        'status' => $status,
                        'price' => $this->input->post('price'),
                        'last_update' => date('Y-m-d H:i:s'),
                        'lokasi_gedung' => $this->input->post('lokasi', true),
                        'id_kost' => $kost_id
                    ];
                } else {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
                        'price' => $this->input->post('price'),
                        'last_update' => date('Y-m-d H:i:s'),
                        'lokasi_gedung' => $this->input->post('lokasi', true),
                        'id_kost' => $kost_id
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
        $kost_id = $this->session->userdata('kost_id');

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
                    'tgl_keluar' => $tgl_keluar,
                    'alamat' => $post['alamat'],
                    'id_kost' => $kost_id
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
                    'tgl_keluar' => $tgl_keluar,
                    'alamat' => $post['alamat']
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
        $this->form_validation->set_rules('jumlah', 'Jumlah pembayaran', 'trim|numeric');
        $this->form_validation->set_rules('via', 'Via pembayaran', 'trim');

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
        $id = $this->input->post('id', true);
        $act = $this->input->post('act', true);



        switch ($act) {
            case 'add':
                $bukti = $_FILES['bukti']['name'];



                if ($bukti) {
                    $id_penghuni = $this->input->post('penghuni_kost');
                    $periode = $this->input->post('periode');
                    $new_filename = $id_penghuni . '_' . $periode . '_' . time();

                    $config['upload_path']          = './assets/bukti/';
                    $config['allowed_types']        = 'gif|jpg|png|jpeg|svg';
                    $config['file_name']            = $new_filename;
                    // $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('bukti')) {
                        $file_width = $this->upload->data('image_width');
                        $file_height = $this->upload->data('image_height');
                        $file_path = $this->upload->data('full_path');

                        if ($file_height > 850 || $file_width > 850) {
                            $this->app->resize_image($file_path, $file_width, $file_height);
                        }

                        $this->app->wm_image($file_path, $id_penghuni, $periode);
                        $bukti_pembayaran = $this->upload->data('file_name');
                    } else {
                        $params = [
                            'status' => true,
                            'msg' => 'Bukti gagal di upload',
                            'token' => $this->security->get_csrf_hash(),
                            'type' => 'result'
                        ];
                        echo json_encode($params);
                        die;
                    }
                } else {
                    $bukti_pembayaran = null;
                }


                $data = [
                    'id_penghuni' => $this->input->post('penghuni_kost'),
                    'periode' => $this->input->post('periode'),
                    'tgl_bayar' => $this->input->post('tgl'),
                    'jml_bayar' => $this->input->post('jumlah'),
                    'via_pembayaran' => $this->input->post('via'),
                    'ket' => $this->input->post('ket'),
                    'bukti' => $bukti_pembayaran
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
                $bukti = $_FILES['bukti']['name'];
                $pembayaran = $this->db->get_where('pembayaran', ['id' => $id])->row();

                if ($bukti) {
                    $id_penghuni = $this->input->post('id_penghuni');
                    $periode = $this->input->post('periode');
                    $new_filename = $id_penghuni . '_' . $periode . '_' . time();

                    $config['upload_path']          = './assets/bukti/';
                    $config['allowed_types']        = 'gif|jpg|png|jpeg|svg';
                    $config['file_name']            = $new_filename;
                    // $this->load->library('upload', $config);
                    $this->upload->initialize($config);

                    if ($this->upload->do_upload('bukti')) {
                        unlink(FCPATH . 'assets/bukti/' . $pembayaran->bukti);
                        $file_width = $this->upload->data('image_width');
                        $file_height = $this->upload->data('image_height');
                        $file_path = $this->upload->data('full_path');

                        if ($file_height > 850 || $file_width > 850) {
                            $this->app->resize_image($file_path, $file_width, $file_height);
                        }

                        $this->app->wm_image($file_path, $id_penghuni, $periode);
                        $bukti_pembayaran = $this->upload->data('file_name');
                    } else {
                        $params = [
                            'status' => true,
                            'msg' => 'Bukti gagal di upload',
                            'token' => $this->security->get_csrf_hash(),
                            'type' => 'result'
                        ];
                        echo json_encode($params);
                        die;
                    }
                } else {
                    $bukti_pembayaran = $pembayaran->bukti;
                }



                $data = [
                    'periode' => $post['periode'],
                    'tgl_bayar' => $post['tgl'],
                    'jml_bayar' => $post['jumlah'],
                    'via_pembayaran' => $post['via'],
                    'ket' => $post['ket'],
                    'bukti' => $bukti_pembayaran
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




    //ajax pengeluaran
    public function valid_pengeluaran()
    {
        cek_ajax();
        $this->form_validation->set_rules('biaya', 'Biaya', 'required|trim');
        $this->form_validation->set_rules('nominal', 'Nominal', 'required|trim|numeric');

        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_biaya' => form_error('biaya'),
                'err_nominal' => form_error('nominal'),
                'token' => $this->security->get_csrf_hash()
            ];
            echo json_encode($params);
            die;
        } else {
            $this->_pengeluaran();
        }
    }

    private function _pengeluaran()
    {
        $post = $this->input->post(null, true);
        $kost_id = $this->session->userdata('kost_id');
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':


                $data = [
                    'tanggal' => $post['date'],
                    'biaya' => $post['biaya'],
                    'nominal' => $post['nominal'],
                    'ket' => $post['ket'],
                    'id_kost' => $kost_id,
                ];
                $this->db->insert('pengeluaran', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'data berhasil di tambahkan'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'data gagal di tambahkan'
                    ];
                }
                $arr_token = ['type' => 'result', 'token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;
                break;
            case 'edit':

                $data = [
                    'tanggal' => $post['date'],
                    'biaya' => $post['biaya'],
                    'nominal' => $post['nominal'],
                    'ket' => $post['ket'],
                ];
                $this->db->where('id', $id)->update('pengeluaran', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'data berhasil di update'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'data gagal di update'
                    ];
                }
                $arr_token = ['type' => 'result', 'token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;

                break;
        }
    }

    public function delete_pengeluaran()
    {
        cek_ajax();
        $id = $this->input->post('id');
        $this->db->where('id', $id)->delete('pengeluaran');

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

    //end ajax pengeluaran



    //ajax setoran
    public function valid_setor()
    {
        cek_ajax();
        $this->form_validation->set_rules('nominal', 'Nominal', 'required|trim|numeric');
        $this->form_validation->set_rules('ket', 'Keterangan', 'required|trim');
        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_nominal' => form_error('nominal'),
                'err_ket' => form_error('ket'),
                'token' => $this->security->get_csrf_hash()
            ];
            echo json_encode($params);
            die;
        } else {
            $this->_setor();
        }
    }

    private function _setor()
    {
        $post = $this->input->post(null, true);
        $kost_id = $this->session->userdata('kost_id');
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':
                $data = [
                    'tanggal' => $post['date'],
                    'ket' => $post['ket'],
                    'nominal' => $post['nominal'],
                    'id_kost' => $kost_id
                ];

                $this->db->insert('setoran', $data);

                if ($this->db->affected_rows() > 0) {
                    $params = ['status' => true, 'msg' => 'Setoran berhasil di tambahkan'];
                } else {
                    $params = ['status' => false, 'msg' => 'Setoran gagal di tambahkan'];
                }
                $arr_token = ['type' => 'result', 'token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;

                break;
            case 'edit':

                $data = [
                    'tanggal' => $post['date'],
                    'ket' => $post['ket'],
                    'nominal' => $post['nominal']
                ];

                $this->db->where('id', $id)->update('setoran', $data);

                if ($this->db->affected_rows() > 0) {
                    $params = ['status' => true, 'msg' => 'Setoran berhasil di update'];
                } else {
                    $params = ['status' => false, 'msg' => 'Setoran gagal di update'];
                }
                $arr_token = ['type' => 'result', 'token' => $this->security->get_csrf_hash()];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;

                break;
        }
    }

    public function delete_setor()
    {
        cek_ajax();
        $id = $this->input->post('id');
        $this->db->where('id', $id)->delete('setoran');

        if ($this->db->affected_rows() > 0) {
            $params = ['status' => true, 'msg' => 'Setoran berhasil di hapus'];
        } else {
            $params = ['status' => false, 'msg' => 'Setoran gagal di hapus'];
        }

        $arr_token = ['token' => $this->security->get_csrf_hash()];
        $output = array_merge($params, $arr_token);
        echo json_encode($output);
    }
    //end ajax setoran



    //ajax data kost
    public function act_kost()
    {
        cek_ajax();
        $this->form_validation->set_rules('kost', 'Nama Kost', 'required|trim');
        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_kost' => form_error('kost'),
                'token' => $this->security->get_csrf_hash()
            ];
            echo json_encode($params);
            die;
        } else {
            $this->_act_kost();
        }
    }

    private function _act_kost()
    {
        $post = $this->input->post(null, true);
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':


                $foto_kost = $_FILES['foto_kost']['name'];

                $nama_kontak = $post['nama_kontak'];
                $no_kontak = $post['no_kontak'];
                $c_kontak = count($nama_kontak);

                if ($c_kontak <= 0 || $nama_kontak == null || $no_kontak == null) {
                    $out = [
                        'status' => false,
                        'msg' =>  'Harap isi bagian kontak',
                        'type' => 'result',
                        'token' => $this->security->get_csrf_hash()
                    ];
                    echo json_encode($out);
                    die;
                }

                $post_kontak = [];
                for ($i = 0; $i < $c_kontak; $i++) {
                    $row = [
                        'name' => $nama_kontak[$i],
                        'no' => $no_kontak[$i]
                    ];
                    $post_kontak[] = $row;
                }
                $data_kontak = json_encode($post_kontak);


                $config['upload_path'] = './assets/img/';
                $config['allowed_types'] = 'gif|jpg|png|jpeg';
                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload('foto_kost')) {
                    $file = $this->upload->data('file_name');
                } else {
                    $out = [
                        'status' => false,
                        'msg' =>  $this->upload->display_errors(),
                        'type' => 'result',
                        'token' => $this->security->get_csrf_hash()
                    ];
                    echo json_encode($out);
                    die;
                }


                $data = [
                    'kost_name' => $post['kost'],
                    'alamat' => $post['alamat'],
                    'foto' => $file,
                    'kontak' => $data_kontak
                ];

                $this->db->insert('kost', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'Data berhasil di tambahkan'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'Data gagal di tambahkan'
                    ];
                }

                $arr_token = [
                    'type' => 'result',
                    'token' => $this->security->get_csrf_hash()
                ];

                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;
                break;
            case 'edit':
                $nama_kontak = $post['nama_kontak'];
                $no_kontak = $post['no_kontak'];
                $c_kontak = count($nama_kontak);

                if ($c_kontak <= 0 || $nama_kontak == null || $no_kontak == null) {
                    $out = [
                        'status' => false,
                        'msg' =>  'Harap isi bagian kontak',
                        'type' => 'result',
                        'token' => $this->security->get_csrf_hash()
                    ];
                    echo json_encode($out);
                    die;
                }
                $post_kontak = [];
                for ($i = 0; $i < $c_kontak; $i++) {
                    $row = [
                        'name' => $nama_kontak[$i],
                        'no' => $no_kontak[$i]
                    ];
                    $post_kontak[] = $row;
                }
                $data_kontak = json_encode($post_kontak);


                $foto_kost = $_FILES['foto_kost']['name'];
                $get_data = $this->db->get_where('kost', ['id' => $id])->row();
                if ($foto_kost != null || $foto_kost != '') {
                    $config['upload_path'] = './assets/img/';
                    $config['allowed_types'] = 'gif|jpg|png|jpeg';
                    $this->load->library('upload', $config);
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload('foto_kost')) {
                        if ($get_data->foto != 'default.png') {

                            unlink(FCPATH . 'assets/img/' . $get_data->foto);
                        }
                        $data_foto = $this->upload->data('file_name');
                    } else {
                        $out = [
                            'status' => false,
                            'msg' =>  $this->upload->display_errors(),
                            'type' => 'result',
                            'token' => $this->security->get_csrf_hash()
                        ];
                        echo json_encode($out);
                        die;
                    }
                } else {
                    if ($get_data->foto == '' || $get_data->foto == null) {
                        $data_foto = 'default.png';
                    } else {
                        $data_foto = $get_data->foto;
                    }
                }






                $data = [
                    'kost_name' => $post['kost'],
                    'alamat' => $post['alamat'],
                    'foto' => $data_foto,
                    'kontak' => $data_kontak
                ];
                $this->db->where('id', $id)->update('kost', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'Data berhasil di update'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'Data gagal di update'
                    ];
                }

                $arr_token = [
                    'type' => 'result',
                    'token' => $this->security->get_csrf_hash()
                ];

                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;
                break;
        }
    }

    public function get_data_kost()
    {
        cek_ajax();
        $post = $this->input->post(null, true);
        $id = $post['id'];
        $get_data = $this->db->get_where('kost', ['id' => $id])->row();



        if ($get_data) {

            if ($get_data->kontak == null || $get_data->kontak == '') {
                $data_kontak = null;
            } else {
                $data_kontak = json_decode($get_data->kontak);
            }

            $data = [
                'alamat' => $get_data->alamat,
                'foto' => base_url('assets/img/') . $get_data->foto,
                'kontak' => $data_kontak
            ];
        } else {
            $data = [
                'alamat' => null,
                'foto' => null,
                'kontak' => null
            ];
        }

        $arr_token = [
            'token' => $this->security->get_csrf_hash()
        ];
        $output = array_merge($arr_token, $data);
        echo json_encode($output);
    }

    public function delete_kost()
    {
        cek_ajax();
        $id = $this->input->post('id');

        $this->db->where('id', $id)->delete('kost');
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
        $arr_token = [
            'token' => $this->security->get_csrf_hash()
        ];
        $output = array_merge($arr_token, $params);
        echo json_encode($output);
    }
    //end data kost



    //ajax data user
    public function action_user()
    {
        cek_ajax();
        $post = $this->input->post(null, true);
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':
                $this->form_validation->set_rules('name', 'Nama User', 'required|trim|min_length[3]');
                $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|is_unique[user.username]');
                $this->form_validation->set_rules('newpass', 'Password Baru', 'required|trim|min_length[5]|matches[repass]');
                $this->form_validation->set_rules('repass', 'Ulangi Password', 'required|trim|matches[repass]');

                if ($this->form_validation->run() == false) {
                    $params = [
                        'type' => 'validation',
                        'token' => $this->security->get_csrf_hash(),
                        'err_name' => form_error('name'),
                        'err_username' => form_error('username'),
                        'err_newpass' => form_error('newpass'),
                        'err_repass' => form_error('repass')
                    ];
                    echo json_encode($params);
                    die;
                } else {
                    $data = [
                        'name' => $post['name'],
                        'username' => $post['username'],
                        'password' => md5(sha1($post['newpass'])),
                        'role' => $post['role'],
                        'status' => 1
                    ];
                    $this->_action_user('add', $id, $data);
                }

                break;
            case 'edit':

                $this->form_validation->set_rules('name', 'Nama User', 'required|trim|min_length[3]');
                $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]');

                if ($this->form_validation->run() == false) {
                    $params = [
                        'type' => 'validation',
                        'err_name' => form_error('name'),
                        'err_username' => form_error('username'),
                        'token' => $this->security->get_csrf_hash()
                    ];
                    echo json_encode($params);
                    die;
                } else {
                    $get_username = $this->db->get_where('user', ['username' => $post['username'], 'id !=' => $id])->num_rows();

                    if ($get_username >= 1) {
                        $params = [
                            'err_username' => 'Username has been registered',
                            'token' => $this->security->get_csrf_hash(),
                            'type' => 'validation'
                        ];
                        echo json_encode($params);
                        die;
                    } else {
                        $data = [
                            'name' => $post['name'],
                            'username' => $post['username'],
                            'role' => $post['role']
                        ];
                        $this->_action_user('edit', $id, $data);
                    }
                }

                break;
            case 'delete':

                $this->db->where('id', $id)->delete('user');
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'User berhasil di hapus'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'User gagal di hapus'
                    ];
                }
                $arr_token = [
                    'token' => $this->security->get_csrf_hash()
                ];
                $output = array_merge($params, $arr_token);
                echo json_encode($output);
                die;

                break;

            case 'status':
                $status = $post['status'];

                if ($status == 'aktif') {
                    $set_status = 1;
                } else if ($status == 'nonaktif') {
                    $set_status = 0;
                } else {
                    $set_status = 0;
                }

                $this->db->set('status', $set_status)->where('id', $id)->update('user');
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'Status user berhasil di ubah'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'Status user gagal di ubah'
                    ];
                }

                $arr_token = [
                    'token' => $this->security->get_csrf_hash()
                ];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;
                break;
        }
    }

    private function _action_user($action, $id, $data)
    {
        switch ($action) {
            case 'add':
                $this->db->insert('user', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'User baru berhasil di tambahkan'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'User baru gagal di tambahkan'
                    ];
                }

                $arr_token = [
                    'token' => $this->security->get_csrf_hash(),
                    'type' => 'result'
                ];
                $output = array_merge($params, $arr_token);
                echo json_encode($output);
                die;
                break;
            case 'edit':

                $this->db->where('id', $id)->update('user', $data);
                if ($this->db->affected_rows() > 0) {
                    $params = [
                        'status' => true,
                        'msg' => 'User baru berhasil di update'
                    ];
                } else {
                    $params = [
                        'status' => false,
                        'msg' => 'User baru gagal di update'
                    ];
                }

                $arr_token = [
                    'token' => $this->security->get_csrf_hash(),
                    'type' => 'result'
                ];
                $output = array_merge($params, $arr_token);
                echo json_encode($output);
                die;

                break;
        }
    }

    public function access_kost()
    {
        cek_ajax();
        $post = $this->input->post(null, true);

        $id = $post['id'];
        $act  = $post['act'];

        switch ($act) {
            case 'get_data':
                $data = [];
                $data_kost = $this->db->get('kost')->result();
                foreach ($data_kost as $dk) {
                    $check_access = $this->db->get_where('access_kost', ['id_user' => $id, 'id_kost' => $dk->id])->row();

                    if ($check_access) {
                        $access = 1;
                    } else {
                        $access = 0;
                    }

                    $row = [
                        'id_kost' => $dk->id,
                        'kost_name' => $dk->kost_name,
                        'access' => $access
                    ];
                    $data[] = $row;
                }
                $output = [
                    'token' => $this->security->get_csrf_hash(),
                    'access' => $data
                ];
                echo json_encode($output);
                die;

                break;
            case 'change':

                $list_kost = $post['kost'];
                $c_kost = count($list_kost);

                if ($c_kost > 0) {
                    $data = [];
                    for ($i = 0; $i < $c_kost; $i++) {
                        $row = [
                            'id_user' => $id,
                            'id_kost' => $list_kost[$i]
                        ];
                        $data[] = $row;
                    }

                    $this->db->trans_begin();

                    $this->db->delete('access_kost', ['id_user' => $id]);
                    $this->db->insert_batch('access_kost', $data);

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        $params = [
                            'status' => false,
                            'msg' => 'Access gagal di perbarui'
                        ];
                    } else {
                        $this->db->trans_commit();
                        $params = [
                            'status' => true,
                            'msg' => 'Access berhasil di perbarui'
                        ];
                    }
                } else {
                    $this->db->delete('access_kost', ['id_user' => $id]);
                    if ($this->db->affected_rows() > 0) {
                        $params = [
                            'status' => true,
                            'msg' => 'Access berhasil di perbarui'
                        ];
                    } else {
                        $params = [
                            'status' => false,
                            'msg' => 'Access gagal di perbarui'
                        ];
                    }
                }

                $arr_token = [
                    'token' => $this->security->get_csrf_hash()
                ];
                $output = array_merge($arr_token, $params);
                echo json_encode($output);
                die;

                break;
        }
    }

    //end data user


    //ajax report
    public function get_data_report()
    {
        cek_ajax();
        $periode_a = $this->input->post('periode_a');
        $periode_b = $this->input->post('periode_b');
        $kost_id = $this->session->userdata('kost_id');


        $pengeluaran = $this->db->where("tanggal BETWEEN '$periode_a' AND '$periode_b'")
            ->where('id_kost', $kost_id)
            ->order_by('tanggal', 'ASC')
            ->get('pengeluaran')->result();

        $setoran = $this->db->where("tanggal BETWEEN '$periode_a' AND '$periode_b'")
            ->where('id_kost', $kost_id)
            ->order_by('tanggal', 'ASC')
            ->get('setoran')->result();

        $penghuni_aktif = $this->app->query_all_data_penghuni($periode_a, $periode_b)->result();


        $table_pemasukan = '';
        $table_pengeluaran = '';
        $table_setoran = '';

        $total_pemasukan_real = 0;
        $total_pemasukan_seharusnya = 0;
        $total_pengeluaran = 0;
        $total_setoran = 0;

        if (!empty($pengeluaran)) {
            $no = 1;
            foreach ($pengeluaran as $d) {
                $tgl = cek_tgl($d->tanggal);
                $nominal = number_format($d->nominal);
                $total_pengeluaran += $d->nominal;
                $table_pengeluaran .= '
                    <tr style="background-color: #FDE9D9; color: black;">
                        <td>' . $no++ . '</td>
                        <td>' . $tgl . '</td>
                        <td>' . $d->biaya . '</td>
                        <td>' . $nominal . '</td>
                        <td>' . $d->ket . '</td>
                    </tr>
                ';
            }
        } else {
            $table_pengeluaran = '<tr style="background-color: #FDE9D9; color: black;"><td class="text-center" colspan="5">No data result</td></tr>';
            $total_pengeluaran = 0;
        }


        if (!empty($setoran)) {
            $no = 1;
            foreach ($setoran as $d) {
                $tgl = cek_tgl($d->tanggal);
                $nominal = number_format($d->nominal);
                $total_setoran += $d->nominal;
                $table_setoran .= '
                    <tr style="background-color: #E6B8B7; color: black;">
                        <td>' . $no++ . '</td>
                        <td>' . $tgl . '</td>
                        <td>' . $d->ket . '</td>
                        <td>' . $nominal . '</td>
                    </tr>
                ';
            }
        } else {
            $table_setoran = '<tr style="background-color: #E6B8B7; color: black;"><td class="text-center" colspan="4">No data result</td></tr>';
            $total_setoran = 0;
        }

        if (!empty($penghuni_aktif)) {
            $no = 1;
            foreach ($penghuni_aktif as $d) {
                $c_periode_a = date_create($periode_a);
                $c_periode_b = date_create($periode_b);

                $periode_a = date_format($c_periode_a, 'Y-m');
                $periode_b = date_format($c_periode_b, 'Y-m');


                $data_pembayaran = $this->db
                    ->where('id_penghuni', $d->id_penghuni)
                    ->where("periode BETWEEN '$periode_a' AND '$periode_b'")
                    ->get('pembayaran')
                    ->row();

                if (!empty($data_pembayaran)) {
                    $tgl_bayar = cek_tgl($data_pembayaran->tgl_bayar);
                    $jml_bayar = number_format($data_pembayaran->jml_bayar);
                    $via = $data_pembayaran->via_pembayaran;
                    $ket = $data_pembayaran->ket;
                    $c_jml_bayar = $data_pembayaran->jml_bayar;
                } else {
                    $tgl_bayar = '-';
                    $jml_bayar = '-';
                    $via = '-';
                    $ket = '-';
                    $c_jml_bayar = 0;
                }


                if ($d->status_penghuni == 1) {
                    $style = 'style="background: #FFFF00; color: black;"';
                    $c_real_pemasukan = $d->price;
                } else if ($d->status_penghuni == 2) {
                    $style = 'style="background: #D8E4BC; color: black;"';
                    $c_real_pemasukan = $d->price;
                } else if ($d->status_penghuni == 3) {
                    $style = 'style="background: #cf795f; color: black;"';
                    $c_real_pemasukan = 0;
                } else {
                    $style = '';
                    $c_real_pemasukan = 0;
                }

                $tgl_harusnya = cek_tgl($d->tgl_penempatan);


                $total_pemasukan_real += $c_jml_bayar;
                $total_pemasukan_seharusnya += $c_real_pemasukan;

                $table_pemasukan .= '
                    <tr ' . $style . '>
                        <td>' . $no++ . '</td>
                        <td>' . $tgl_harusnya . '</td>
                        <td>' . $tgl_bayar . '</td>

                        <td>' . $d->no_kamar . '</td>
                        <td>' . $d->nama_penghuni . '</td>
                        <td>' . number_format($d->price) . '</td>
                        <td>' . $jml_bayar . '</td>
                        <td>' . $via . '</td>
                        <td>' . $ket . '</td>
                    </tr>
                ';
            }
        } else {
            $table_pemasukan = '
                <tr class="text-white" style="background: #9BBB59;">
                    <td class="text-center" colspan="9">No data result</td>
                </tr>
            ';
            $total_pemasukan_real = 0;
            $total_pemasukan_seharusnya = 0;
        }

        $selisih_pemasukan = $total_pemasukan_seharusnya - $total_pemasukan_real;

        $table = '
                    <table class="table table-sm table-bordered my-2">
                        <thead>
                            <tr>
                                <th colspan="5"><u class="text-dark">Pemasukan</u></th>
                            </tr>
                            <tr class="text-white" style="background: #9BBB59;">
                                <th>#</th>
                                <th>Tgl Seharusnya Bayar</th>
                                <th>Tgl Bayar</th>
                                <th>No. Kamar</th>
                                <th>Nama</th>
                                <th>Harga Kamar</th>
                                <th>Bayar</th>
                                <th>Via</th>
                                <th>Ket</th>
                            </tr>
                        </thead>
                        <tbody> 
                            ' . $table_pemasukan . '
                        </tbody>
                        <tfoot>
                            <tr class="text-white" style="background: #9BBB59;">
                                <th colspan="5" class="text-center">Total</th>
                                <th>' . number_format($total_pemasukan_seharusnya) . '</th>
                                <th>' . number_format($total_pemasukan_real) . '</th>
                                <th colspan="2"></th>
                            </tr>
                            <tr class="text-white" style="background: #9BBB59;">
                                <th colspan="5" class="text-center">Selisih</th>
                                <th colspan="2">' . number_format($selisih_pemasukan) . '</th>
                                <th colspan="2"></th>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table table-sm table-bordered my-2">
                        <thead>
                            <tr>
                                <th colspan="5"><u class="text-dark">Pengeluaran</u></th>
                            </tr>
                            <tr class="text-white" style="background: #F79646;">
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Biaya</th>
                                <th>Nominal</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $table_pengeluaran . '
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #F79646; color: white;">
                                <th  colspan="3" class="text-right">Total</th>
                                <th>' . number_format($total_pengeluaran) . '</th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>

                    <table class="table table-sm table-bordered my-2">
                        <thead>
                            <tr>
                                <th colspan="5"><u class="text-dark">Setoran</u></th>
                            </tr>
                            <tr class="text-white" style="background: #C0504D; color: black;">
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Setor</th>
                                <th>Nominal</th>
                            </tr>
                        </thead>
                        <tbody>
                            ' . $table_setoran . '
                        </tbody>
                        <tfoot>
                            <tr style="background-color: #C0504D; color: white;">
                                <th  colspan="3" class="text-right">Total</th>
                                <th>' . number_format($total_setoran) . '</th>
                            </tr>
                        </tfoot>
                    </table>

        ';

        $arr_token = ['token' => $this->security->get_csrf_hash(), 'html' => $table];
        echo json_encode($arr_token);
    }
    //end ajax report


    //ajax settings
    public function validation_settings()
    {
        cek_ajax();
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('name', 'Nama', 'required|trim|min_length[3]');

        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'token' => $this->security->get_csrf_hash(),
                'err_username' => form_error('username'),
                'err_name' => form_error('name')
            ];
            echo json_encode($params);
            die;
        } else {
            $this->_settings();
        }
    }

    private function _settings()
    {
        $post = $this->input->post(null, true);
        $id = $post['id'];
        $username = $post['username'];

        $check_username = $this->db->get_where('user', ['id !=' => $id, 'username' => $username])->num_rows();
        if ($check_username > 0) {
            $params = [
                'status' => false,
                'msg' => 'Username has been registered'
            ];
        } else {

            $data = [
                'username' => $username,
                'name' =>  $post['name'],
            ];

            $this->db->where('id', $id)->update('user', $data);
            if ($this->db->affected_rows() > 0) {
                $params = [
                    'status' => true,
                    'msg' => 'Data berhasil di update'
                ];
            } else {
                $params = [
                    'status' => false,
                    'msg' => 'Data gagal di update'
                ];
            }
        }

        $arr_token = [
            'type' => 'result',
            'token' => $this->security->get_csrf_hash()
        ];
        $output = array_merge($params, $arr_token);
        echo json_encode($output);
    }

    public function validation_password()
    {
        cek_ajax();
        $this->form_validation->set_rules('old_pass', 'Password Lama', 'required|trim');
        $this->form_validation->set_rules('new_pass', 'Password Baru', 'required|trim|min_length[5]|matches[re_newpass]');
        $this->form_validation->set_rules('re_newpass', 'Ulangi Password Baru', 'required|trim|matches[new_pass]');


        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'token' => $this->security->get_csrf_hash(),
                'err_oldpass' => form_error('old_pass'),
                'err_newpass' => form_error('new_pass'),
                'err_re_newpass' => form_error('re_newpass')
            ];
            echo json_encode($params);
            die;
        } else {
            $this->_password();
        }
    }


    private function _password()
    {
        $post = $this->input->post(null, true);
        $id = $post['id'];
        $old_pass = md5(sha1($post['old_pass']));
        $get_user = $this->db->get_where('user', ['id' => $id])->row();
        if ($get_user->password == $old_pass) {
            $new_pass = md5(sha1($post['new_pass']));
            $this->db->set('password', $new_pass)->where('id', $id)->update('user');

            if ($this->db->affected_rows() > 0) {
                $params = [
                    'status' => true,
                    'msg' => 'Password berhasil di update'
                ];
            } else {
                $params = [
                    'status' => false,
                    'msg' => 'Password gagal di update'
                ];
            }
        } else {
            $params = [
                'status' => false,
                'msg' => 'Invalid Password Lama'
            ];
        }
        $arr_token = [
            'type' => 'result',
            'token' => $this->security->get_csrf_hash()
        ];
        $output = array_merge($params, $arr_token);
        echo json_encode($output);
    }
    //end ajax settings


    //get data for dashboard
    public function data_dashboard()
    {
        cek_ajax();
        $this_month = date('m');
        $this_year = date('Y');

        $periode = $this_year . '-' . $this_month;

        $kost_id = $this->session->userdata('kost_id');
        $jml_kost = $this->db->get_where('kamar', ['id_kost' => $kost_id])->num_rows();
        $jml_penghuni = $this->db->get_where('penghuni', ['id_kost' => $kost_id, 'status' => 2])->num_rows();
        $jml_pengeluaran = $this->db->select('SUM(nominal) AS jml')
            ->from('pengeluaran')
            ->where([
                'id_kost' => $kost_id,
                'month(tanggal)' => $this_month,
                'year(tanggal)' => $this_year
            ])
            ->get()->row();
        $jml_pemasukan = $this->db->select('SUM(jml_bayar) AS jml')
            ->from('pembayaran')
            ->join('penghuni', 'pembayaran.id_penghuni = penghuni.id')
            ->where([
                'penghuni.id_kost' => $kost_id,
                'pembayaran.periode' => $periode
            ])->get()->row();


        $output = [
            'token' => $this->security->get_csrf_hash(),
            'jml_kost' => $jml_kost,
            'jml_penghuni' => $jml_penghuni,
            'jml_pengeluaran' => $jml_pengeluaran->jml,
            'jml_pemasukan' => $jml_pemasukan->jml
        ];

        echo json_encode($output);
    }
    //end get data



    //data transaksi
    public function data_transaksi()
    {
        cek_ajax();
        $id = $this->input->post('id');
        $data = $this->app->get_data_pemasukan(null, $id)->row();
        $output = [
            'token' => $this->security->get_csrf_hash(),
            'data' => $data
        ];
        echo json_encode($output);
    }

    public function data_pengeluaran()
    {
        cek_ajax();
        $id = $this->input->post('id');
        $data = $this->db->get_where('pengeluaran', ['id' => $id])->row();
        $output = [
            'data' => $data,
            'token' => $this->security->get_csrf_hash(),
        ];
        echo json_encode($output);
    }

    public function data_setoran()
    {
        cek_ajax();
        $id = $this->input->post('id');
        $data = $this->db->get_where('setoran', ['id' => $id])->row();
        $output = [
            'data' => $data,
            'token' => $this->security->get_csrf_hash(),
        ];
        echo json_encode($output);
    }
    //end data transaksi


    //get sisa saldo pengawas
    public function get_sisa_saldo()
    {
        cek_ajax();
    }
}
