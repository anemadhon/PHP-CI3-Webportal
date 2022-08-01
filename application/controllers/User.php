<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class User extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

		$this->load->model('User_model', 'user');
		$this->load->model('Role_model', 'role');

		$this->helper->check_is_login();
	}
    
    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Manajemen Pengguna',
            'sub' => 'List'
		);

		$this->load->view('dashboard/management/user/user', $data);
	}

	function get()
    {
		$users = $this->user->get();

		$datas = array();
		$data_users = array();

		if ($users)
        {
			foreach ($users as $key => $user)
            {
				$data_users['no'] = ((int)$key+1);
				$data_users['id'] = $user['id_user'];
				$data_users['name'] = $user['name'];
				$data_users['username'] = $user['username'];
				$data_users['role'] = $user['role'];
				$data_users['cabang'] = $user['kode_cabang'] ? $user['kode_cabang'].'-'.$user['nama_cabang'] : '';
				$data_users['status'] = $user['is_active'] == 1 ? 'Aktif' : ($user['is_active'] == 2 ? 'Tidak Aktif' : 'Terkunci');
				$datas[] = $data_users;
			}
		}

		$json_data = array('data' => $datas);
		exit(json_encode($json_data));
	}
    
    public function form($id = '')
    {
		$this->helper->check_eligible_user_menus();
		
		$roles = $this->role->get();
		$user = $id ? $this->user->get_by_id($id) : '';

		$data = array(
			'title' => 'Manajemen Pengguna',
            'sub' => 'Form',
            'sub_title' => ($id ? 'Ubah' : 'Tambah'),
            'roles' => $roles,
			'user' => $user
		);

		$this->load->view('dashboard/management/user/form', $data);
	}

	function submit()
    {
		$user['username'] = htmlspecialchars($this->input->post('username', TRUE));

		$this->form_validation->set_rules(
			'name',
			'Nama',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Nama.'
			)
		);
		$this->form_validation->set_rules(
			'username',
            'Nama Pengguna',
            'trim|required|max_length[20]',
            array(
                'required' => 'Silahkan Isi Nama Pengguna.',
                'max_length' => 'Nama Pengguna Maksimal 20 karakter.'
            )
		);
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
			'role',
			'Peran',
			'required',
			array(
				'required' => 'Silahkan Pilih Peran.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			if (!form_error('name') && !form_error('username') && !form_error('password') && !form_error('role'))
            {
				$error = '';
			} 

			if (!form_error('name') && !form_error('username') && !form_error('password'))
            {
				$error = form_error('role');
			} 
			
			if (!form_error('name') && !form_error('username') && !form_error('role'))
            {
				$error = form_error('password');
			} 
			
			if (!form_error('name') && !form_error('password') && !form_error('role'))
            {
				$error = form_error('username');
			} 
			
			if (!form_error('username') && !form_error('password') && !form_error('role'))
            {
				$error = form_error('name');
			} 

			$message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][submit] - User '.$this->session->userdata('username').' gagal menambahkan user baru karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
	
	if ($this->_is_unique($user['username']) === FALSE)
	{
		$message = array(
				'success' => FALSE,
				'message' => 'Nama Pengguna Sudah digunakan'
			); 

            	$log['type'] = 'error';
            	$log['message'] = '[User Controller][submit] - User '.$this->session->userdata('username').' gagal menambahkan user baru karena nama pengguna sudah digunakan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            	$this->helper->logging($log);

		exit(json_encode($message));
	}

        if ($this->input->post('password', TRUE) !== PWD)
        {
            $message = array(
				'success' => FALSE,
				'message' => 'Kata Sandi Harus Default, Silahkan Hub. Admin'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][submit] - User '.$this->session->userdata('username').' gagal menambahkan user baru karena kata sandi yang di input bukan kata sandi default pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
        }

		$user['nama_user'] = htmlspecialchars($this->input->post('name', TRUE));
		$user['password'] = password_hash(PWD, PASSWORD_BCRYPT);
		$user['linkage_id_role'] = htmlspecialchars($this->input->post('role', TRUE));
		$user['linkage_kode_cabang'] = htmlspecialchars($this->input->post('cabang', TRUE));
		$user['created_by'] = $this->session->userdata['username'];
		$user['created_date'] = date('Y-m-d H:i:s');
		$user['created_from'] = $this->helper->get_ip_address();
		$user['is_login'] = 0;
		$user['is_active'] = 1;
		$user['counter_wrong_pwd'] = 0;
		$user['counter_login'] = 0;
		
		if ($this->user->submit($user))
        {
            $log['type'] = 'info';
            $log['message'] = '[User Controller][submit] - User '.$this->session->userdata('username').' berhasil menambah data user baru pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $user);
            $this->helper->logging($log);

            return $this->session->set_flashdata('success', 'Berhasil Menambah Data Baru');
        }
        else
        {
            return $this->session->set_flashdata('failed', 'Gagal Menambah Data Baru');
        }
	}
	
	function update()
    {
		$user['id_user'] = htmlspecialchars($this->input->post('id', TRUE));
		$user['username'] = htmlspecialchars($this->input->post('username', TRUE));

		$data_user = $this->user->get_by_id($user['id_user']);

		if ($data_user === FALSE)
        {
			$message = array(
				'success' => FALSE,
				'message' => 'User Tidak Ditemukan.'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][update] - User '.$this->session->userdata('username').' gagal mengubah data user dengan id: '.$user['id_user'].' karena user tsb tidak ditemukan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		if (strcasecmp($data_user['username'], $user['username']) !== 0)
        {
			if ($this->_is_unique($user['username']) === FALSE)
			{
				$message = array(
						'success' => FALSE,
						'message' => 'Nama Pengguna sudah digunakan.'
				); 

            			$log['type'] = 'error';
            			$log['message'] = '[User Controller][update] - User '.$this->session->userdata('username').' gagal mengubah data user dengan id: '.$user['id_user'].' karena nama pengguna sudah digunakan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            			$this->helper->logging($log);

				exit(json_encode($message));
			}
		}
		
        $this->form_validation->set_rules(
            'name',
            'Nama',
            'trim|required',
            array(
                'required' => 'Silahkan Isi Nama.'
            )
        );
        $this->form_validation->set_rules(
            'username',
            'Nama Pengguna',
            'trim|required',
            array(
                'required' => 'Silahkan Isi Nama Pengguna.'
            )
        );
        $this->form_validation->set_rules(
            'role',
            'Peran',
            'required',
            array(
                'required' => 'Silahkan Pilih Peran.'
            )
        );

        if ($this->form_validation->run() === FALSE)
        {
            if (!form_error('name') && !form_error('username') && !form_error('role'))
            {
                $error = '';
            } 

            if (!form_error('name') && !form_error('username'))
            {
                $error = form_error('role');
            } 
            
            if (!form_error('name') && !form_error('role'))
            {
                $error = form_error('username');
            } 
            
            if (!form_error('username') && !form_error('role'))
            {
                $error = form_error('name');
            } 

            $message = array(
                'success' => FALSE,
                'message' => $error
            ); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][update] - User '.$this->session->userdata('username').' gagal mengubah data user dengan id: '.$user['id_user'].' karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

            exit(json_encode($message));
        }
		
		$user['nama_user'] = htmlspecialchars($this->input->post('name', TRUE));
		$user['linkage_id_role'] = htmlspecialchars($this->input->post('role', TRUE));
		$user['linkage_kode_cabang'] = $this->input->post('cabang', TRUE) ? htmlspecialchars($this->input->post('cabang', TRUE)) : 0;
		$user['modified_by'] = $this->session->userdata['username'];
		$user['modified_date'] = date('Y-m-d H:i:s');
		
		if ($this->user->update($user))
        {
            $log['type'] = 'info';
            $log['message'] = '[User Controller][submit] - User '.$this->session->userdata('username').' berhasil mengubah data user dengan id: '.$user['id_user'].' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $user);
            $this->helper->logging($log);

            return $this->session->set_flashdata('success', 'Berhasil Merubah Data');
        }
        else
        {
            return $this->session->set_flashdata('failed', 'Gagal Merubah Data');
        }
	}

	function soft_delete()
    {
        $user['id_user'] = htmlspecialchars($this->input->post('id', TRUE));

		$data_user = $this->user->get_by_id($user['id_user']);

		if ($data_user === FALSE)
        {
			$message = array(
				'success' => FALSE,
				'message' => 'User Tidak Ditemukan.'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][soft_delete] - User '.$this->session->userdata('username').' gagal menghapus data user dengan id: '.$user['id_user'].' karena user tsb tidak ditemukan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

        $user['deleted_by'] = $this->session->userdata['username'];
		$user['deleted_date'] = date('Y-m-d H:i:s');

        $this->user->soft_delete($user);

        $log['type'] = 'info';
        $log['message'] = '[User Controller][soft_delete] - User '.$this->session->userdata('username').' berhasil menghapus data user dengan id: '.$user['id_user'].' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $user);
        $this->helper->logging($log);
	}

    function unlock()
    {
		$user['id'] = htmlspecialchars($this->input->post('id', TRUE));
		$user['username'] = $this->session->userdata['username'];

		$data_user = $this->user->get_by_id($user['id']);

		if ($data_user && $data_user['is_active'] !== 3)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'User Harus Dalam Keadaan Terkunci'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][unlock] - User '.$this->session->userdata('username').' gagal melakukan unlock data user dengan id: '.$user['id'].' karena user tsb tidak dalam keadaan terkunci pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		if ($this->user->unlock($user) === FALSE)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'Terjadi Kesalahan, Gagal Membuka Akun Pengguna'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][unlock] - User '.$this->session->userdata('username').' gagal melakukan unlock data user dengan id: '.$user['id_user'].' karena gagal mengupdate informasi unlock pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

        $log['type'] = 'info';
        $log['message'] = '[User Controller][unlock] - User '.$this->session->userdata('username').' berhasil membuka akun user pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $user);
        $this->helper->logging($log);

		$message = array(
			'success' => 'success',
			'title' => 'Sukses',
			'message' => 'Membuka Akun Pengguna Berhasil'
		); 
		
		exit(json_encode($message));
	}
	
	function disable()
    {	
		$user['id'] = htmlspecialchars($this->input->post('id', TRUE));
		$user['username'] = $this->session->userdata['username'];

		$data_user = $this->user->get_by_id($user['id']);

		if ($data_user && $data_user['is_active'] === 2)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'User Sudah Tidak Aktif'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][disable] - User '.$this->session->userdata('username').' gagal melakukan disable data user dengan id: '.$user['id'].' karena user tsb tidak sudah tidak aktif pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		if ($this->user->disable($user) === FALSE)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'Terjadi Kesalahan, Gagal Menonaktifkan Akun Pengguna'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][disable] - User '.$this->session->userdata('username').' gagal melakukan disable data user dengan id: '.$user['id'].' karena gagal mengupdate informasi disable pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

        $log['type'] = 'info';
        $log['message'] = '[User Controller][disable] - User '.$this->session->userdata('username').' berhasil menonaktifkan akun user pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $user);
        $this->helper->logging($log);

		$message = array(
			'success' => 'success',
			'title' => 'Sukses',
			'message' => 'Akun Pengguna Berhasil Dinonaktifkan'
		); 
		
		exit(json_encode($message));
	}
	
	function activate()
    {
        $user['id'] = htmlspecialchars($this->input->post('id', TRUE));
		$user['username'] = $this->session->userdata['username'];

		$data_user = $this->user->get_by_id($user['id']);

		if ($data_user && $data_user['is_active'] === 1)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'User Sudah Aktif'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][activate] - User '.$this->session->userdata('username').' gagal melakukan aktivasi data user dengan id: '.$user['id'].' karena user tsb sudah aktif pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		if ($this->user->activate($user) === FALSE)
        {
			$message = array(
				'success' => 'error',
				'title' => 'Gagal',
				'message' => 'Terjadi Kesalahan, Gagal Mengaktifkan Akun Pengguna'
			); 

            $log['type'] = 'error';
            $log['message'] = '[User Controller][activate] - User '.$this->session->userdata('username').' gagal melakukan aktivasi data user dengan id: '.$user['id'].' karena gagal mengupdate informasi activate pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

        $log['type'] = 'info';
        $log['message'] = '[User Controller][activate] - User '.$this->session->userdata('username').' berhasil mengaktifkan akun user pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $user);
        $this->helper->logging($log);

		$message = array(
			'success' => 'success',
			'title' => 'Sukses',
			'message' => 'Akun Pengguna Berhasil Diaktifkan'
		); 
		
		exit(json_encode($message));
	}

	private function _is_unique($username)
	{
		$unique = $this->user->is_unique($username);

		return $unique;
	}
}