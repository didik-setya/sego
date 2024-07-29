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

                if ($status != 0 || $status != 1 || $status != 4 || $status != null || $status != '') {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
                        'price' => $this->input->post('price'),
                        'last_update' => date('Y-m-d H:i:s')
                    ];
                } else {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
                        'status' => $status,
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
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':
                $data = [
                    'tanggal' => $post['date'],
                    'biaya' => $post['biaya'],
                    'nominal' => $post['nominal'],
                    'ket' => $post['ket']
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
                    'ket' => $post['ket']
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
        $id = $post['id'];
        $act = $post['act'];

        switch ($act) {
            case 'add':
                $data = [
                    'tanggal' => $post['date'],
                    'ket' => $post['ket'],
                    'nominal' => $post['nominal']
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
                $data = [
                    'kost_name' => $post['kost']
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
                $data = [
                    'kost_name' => $post['kost']
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


    //end data user


    //ajax report
    public function get_data_report()
    {
        cek_ajax();
        $periode = $this->input->post('periode');
        $create_date = date_create($periode);
        $month = date_format($create_date, 'm');
        $year = date_format($create_date, 'Y');

        $penghuni_aktif = $this->app->query_all_data_penghuni(2)->result();
        $pengeluaran = $this->db->where([
            'month(tanggal)' => $month,
            'year(tanggal)' => $year
        ])
            ->order_by('tanggal', 'ASC')
            ->get('pengeluaran')->result();

        $setoran = $this->db->where([
            'month(tanggal)' => $month,
            'year(tanggal)' => $year
        ])
            ->order_by('tanggal', 'ASC')
            ->get('setoran')->result();

        $data_pembayaran = [];
        $data_pengeluaran = [];
        $data_setoran = [];

        foreach ($penghuni_aktif as $pa) {
            $pembayaran = $this->db->get_where('pembayaran', [
                'id_penghuni' => $pa->id_penghuni,
                'periode' => $periode
            ])->row();

            if ($pembayaran) {
                $class = '';
                $tgl_bayar = cek_tgl($pembayaran->tgl_bayar);
                $jml_bayar = number_format($pembayaran->jml_bayar);
                $via = $pembayaran->via_pembayaran;
                $ket = $pembayaran->ket;
            } else {
                $class = 'class="bg-danger text-light"';
                $tgl_bayar = '-';
                $jml_bayar = '-';
                $via = '-';
                $ket = '-';
            }

            $tgl_m = date_create($pa->tgl_penempatan);
            $tgl_bayar_seharusnya = date_format($tgl_m, 'd');

            $row = [
                'tgl_seharusnya_bayar' => $tgl_bayar_seharusnya,
                'tgl_bayar' => $tgl_bayar,
                'no_kamar' => $pa->no_kamar,
                'nama' => $pa->nama_penghuni,
                'harga_kamar' => number_format($pa->price),
                'bayar' => $jml_bayar,
                'via' => $via,
                'ket' => $ket,
                'class' => $class
            ];
            $data_pembayaran[] = $row;
        }

        foreach ($pengeluaran as $p) {
            $tgl = cek_tgl($p->tanggal);
            $nominal = number_format($p->nominal);
            $row = [
                'tanggal' => $tgl,
                'biaya' => $p->biaya,
                'nominal' => $nominal,
                'ket' => $p->ket
            ];
            $data_pengeluaran[] = $row;
        }

        foreach ($setoran as $s) {
            $tgl = cek_tgl($s->tanggal);
            $nominal = number_format($s->nominal);
            $row = [
                'tanggal' => $tgl,
                'nominal' => $nominal,
                'ket' => $s->ket
            ];
            $data_setoran[] = $row;
        }

        $data = [
            'pembayaran' => $data_pembayaran,
            'pengeluaran' => $data_pengeluaran,
            'setoran' => $data_setoran
        ];

        $arr_token = ['token' => $this->security->get_csrf_hash()];
        $output = array_merge($data, $arr_token);
        echo json_encode($output);
    }

    //end ajax report
}
