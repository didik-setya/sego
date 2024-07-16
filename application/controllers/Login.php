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
                    'redirect' => base_url('dashboard')
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

    public function logout()
    {
        $this->session->sess_destroy();
        redirect(base_url('login'));
    }
}
