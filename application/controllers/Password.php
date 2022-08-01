<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Password extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
		date_default_timezone_set('Asia/Jakarta');

		$this->load->model('Auth_model', 'auth');

        $this->helper->check_is_login();
	}

	public function change_form()
    {
		$data = array(
			'title' => 'Ganti Kata sandi'
		);

		$this->load->view('auth/reset_password', $data);
	}
	
	function change()
    {
        $password['id'] = $this->session->userdata('id');
		$password['username'] = $this->session->userdata('username');
		$password['password'] = $this->input->post('password', TRUE);

		$this->form_validation->set_rules(
			'password', 
			'Kata Sandi', 
			'trim|required|min_length[8]', 
			array(
				'required' => 'Silahkan Isi Kata Sandi.',
				'min_length' => 'Kata Sandi Min. 8 Karakter.'
			)
		);
		$this->form_validation->set_rules(
			'confirm', 
			'Kata Sandi Konfirmasi', 
			'trim|required|min_length[8]|matches[password]',
			array(
				'required' => 'Silahkan Isi Konfirmasi.',
				'matches' => 'Konfirmasi Tidak Sama dengan Kata Sandi.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			$error = form_error('password') ? form_error('password') : (form_error('confirm') ? form_error('confirm') : '');
			$message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[Password Controller][change] - User '.$password['username'].' gagal mengganti kata sandi karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		if ($this->auth->change_password($password) === FALSE)
        {
			$message = array(
				'success' => FALSE,
				'message' => 'Terjadi Kesalahan, Gagal Ganti Kata Sandi, Silahkan Coba Kembali'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Password Controller][change] - User '.$password['username'].' gagal mengganti kata sandi karena gagal mengupdate informasi kata sandi pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

        $log['type'] = 'info';
        $log['message'] = '[Password Controller][change] - User '.$password['username'].' berhasil mengganti kata sandi nya pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
        $this->helper->logging($log);

		$message = array(
			'success' => TRUE,
			'message' => 'Kata Sandi Berhasil Diganti, Menuju Dashboard Setelah 0.8 Detik'
		); 
		
		exit(json_encode($message));
	}

	function reset()
    {
        $password['id'] = htmlspecialchars($this->input->post('id', TRUE));
		$password['username'] = $this->session->userdata('username');
		$password['password'] = $this->input->post('password', TRUE);

		$this->form_validation->set_rules(
			'password', 
			'Kata Sandi', 
			'trim|required|min_length[8]',
			array(
				'required' => 'Kata Sandi Harus Diisi.',
				'min_length' => 'Kata Sandi Min. 8 Karakter.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			$error = form_error('password') ? form_error('password') : '';
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[Password Controller][reset] - User '.$password['username'].' gagal mereset kata sandi karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
		
		if ($this->auth->change_password($password) === FALSE)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'Terjadi Kesalahan, Gagal Ganti Kata Sandi, Silahkan Coba Kembali'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Password Controller][reset] - User '.$password['username'].' gagal mereset kata sandi karena gagal mengupdate informasi utk reset kata sandi pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

        $log['type'] = 'info';
        $log['message'] = '[Password Controller][reset] - User '.$password['username'].' berhasil mereset kata sandi user dengan id: '.$password['id'].' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
        $this->helper->logging($log);

		$message = array(
			'success' => 'success',
			'title' => 'Sukses',
			'message' => 'Kata Sandi Berhasil Diganti'
		); 
		
		exit(json_encode($message));
	}
}