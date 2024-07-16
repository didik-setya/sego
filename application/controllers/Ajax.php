<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Ajax extends CI_Controller
{

    //ajax data kamar
    public function validation_kamar()
    {
        cek_ajax();
        $this->form_validation->set_rules('kamar', 'Kamar', 'required|trim');
        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_kamar' => form_error('kamar'),
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
                    'last_update' => date('Y-m-d H:i:s')
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
                        'last_update' => date('Y-m-d H:i:s')
                    ];
                } else {
                    $data = [
                        'no_kamar' => $this->input->post('kamar', true),
                        'km' => $this->input->post('km', true),
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
}
