<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Login extends CI_Controller
{
    public function index()
    {
        $this->load->view('login');
    }

    public function validation_login()
    {
        cek_ajax();
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]');
        if ($this->form_validation->run() == false) {
            $params = [
                'type' => 'validation',
                'err_username' => form_error('username'),
                'err_password' => form_error('password'),
                'token' => $this->security->get_csrf_hash()
            ];
            echo json_encode($params);
            die;
        } else {
            $this->_login();
        }
    }

    private function _login()
    {
        $username = $this->input->post('username');
        $password = md5(sha1($this->input->post('password')));
        $user = $this->db->get_where('user', ['username' => $username, 'password' => $password])->row();
        $tokenval = [
            'token' => $this->security->get_csrf_hash()
        ];
        if ($user) {
            if ($user->status == 1) {
                $data = [
                    'username' => $user->username,
                    'status' => $user->status,
                    'role' => $user->role
                ];
                $this->session->set_userdata($data);
                $params = [
                    'type' => 'result',
                    'status' => true,
                    'msg' => 'Success Login',
                    'redirect' => base_url('check_access')
                ];
            } else {
                $params = [
                    'type' => 'result',
                    'status' => false,
                    'msg' => 'Account has been suspended'
                ];
            }
        } else {
            $params = [
                'type' => 'result',
                'status' => false,
                'msg' => 'Invalid username or password'
            ];
        }

        $res = array_merge($params, $tokenval);
        echo json_encode($res);
    }

    public function check_access()
    {
        $username = $this->session->userdata('username');
        $get_user = $this->db->get_where('user', ['username' => $username])->row();

        if ($get_user) {
            if ($get_user->role == 'admin') {
                $data['access'] = $this->db->get('kost')->result();
            } else {
                $data['access'] = $this->app->get_access_kost($get_user->id);
            }
            $this->load->view('check_access', $data);
        } else {
            $this->logout();
        }
    }


    public function access_to_kost()
    {
        $id = $this->input->post('id', true);
        $get_kost = $this->db->get_where('kost', ['id' => $id])->row();
        $data = ['kost_id' => $get_kost->id, 'kost_name' => $get_kost->kost_name];
        $this->session->set_userdata($data);
        redirect(base_url());
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}
