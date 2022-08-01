<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
	public function __construct()
    {
		parent::__construct();		
        date_default_timezone_set('Asia/Jakarta');

		$this->load->model('Auth_model', 'auth');
        $this->load->model('Upload_template_model', 'up');
	}

    public function index()
    {
        $this->helper->prevent_to_login_page();

        $connection = $this->helper->check_connection();
        $for_default_password = $this->_check_logout_cookie();
		
		$data = array(
			'title' => 'Masuk',
            'connection' => $connection,
            'default' => $for_default_password
		);

		$this->load->view('auth/login', $data);
	}

    function get_login_time_string()
    {
        $isset_login_time = 1;
        $this->helper->check_is_login($isset_login_time);
        
        $current_time = time();
        $unix = human_to_unix($this->session->userdata('logged_in_at'));
        $format = timespan($unix, $current_time);
        $format_length = explode(', ', $format);

        if (count($format_length) >= 2)
        {
            $this->_set_string_for_hours($format_length[0]);
        }
        
        $this->_set_string_for_minutes($format);
    }

    function login()
    {
	if (!$this->input->is_ajax_request())
        {
            $this->index();
        }

        $this->_login();
    }

	function logout()
    {
        $this->helper->check_is_login();

        $is_logout = $this->_update_is_logout($this->session->userdata('id'));

        if ($is_logout === FALSE)
        {
            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][logout] - gagal melakukan update is_login = 2 (gagal logout) pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

            $this->session->set_flashdata('error', 'Logout Gagal, Silahkan coba beberapa saat lagi');

            $this->helper->redirect_to_dashboard_page();
        }

        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][logout] - Sistem melakukan update is_login = 2 pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
        $this->helper->logging($log);
        
        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][logout] - User '.$this->session->userdata('username').' berhasil keluar pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
        $this->helper->logging($log);

        session_destroy();

        $this->_set_logout_cookie();  

		$this->helper->redirect_to_login_page();
	}

    private function _check_logout_cookie()
    {
        if (isset($_COOKIE['_log_lst_st_']) && $_COOKIE['_log_lst_st_'] == 1)
        {
            return 1;
        }

        return 0;
    }

    private function _set_string_for_hours($hour)
    {
        $convert_time_unit = explode(' ', $hour);

        if ($convert_time_unit[1] == 'Hour' || $convert_time_unit[1] == 'Hours')
        {
            $convert_time_unit[1] = 'Jam';
        }

        if ($convert_time_unit[0] >= 5)
        {
            exit("Masuk Beberapa Jam yang lalu");
        }

        exit("Masuk $convert_time_unit[0] $convert_time_unit[1] yang lalu");
    }

    private function _set_string_for_minutes($minute)
    {
        $convert_time_unit = explode(' ', $minute);
        $convert_time_unit[1] = ($convert_time_unit[1] == 'Second' || $convert_time_unit[1] == 'Seconds') ? 'Detik' : 'Menit';
        
        exit("Masuk $convert_time_unit[0] $convert_time_unit[1] yang lalu");
    }

	private function _login()
    {
        $recaptcha = htmlspecialchars($this->input->post('recaptcha', TRUE));
        $username = htmlspecialchars($this->input->post('username', TRUE));
		$password = $this->input->post('password', TRUE);
		$remember = htmlspecialchars($this->input->post('remember', TRUE));
		$ip_address = $this->helper->get_ip_address();

        $this->_is_recaptcha_set($recaptcha, $username);

        $this->_run_validation($username);

        $user = $this->_check_username($username);

        $this->_check_password($user, $password);

        $this->_check_recaptcha($username);

        $this->_check_is_locked($user);
        
        $this->_check_is_inactive($user);

        $update_data_login = $this->_update_data_login($user, $ip_address);

        $valid_login = ($user['counter_wrong_pwd'] < 10 && $user['is_active'] === 1 && $update_data_login);

        if ($valid_login)
        {
            $this->_set_session($user);

            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][login] - User '.$this->session->userdata('username').' berhasil masuk pada '.$this->session->userdata('logged_in_at').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

            $this->_set_flashdata_for_first_time_login($user['counter_login']);

            $message = array(
                'success' => TRUE,
                'message' => 'Berhasil Masuk, Menuju Dashboard Setelah 0.8 Detik',
                'login' => $user['counter_login'] === 0 ? 0 : 1
            );

            exit(json_encode($message));
		}
	}

    private function _is_recaptcha_set($recaptcha, $username)
    {
        if ($recaptcha == '')
        {
            $message = array(
                'success' => FALSE,
                'message' => 'Silahkan Ceklis reCaptcha'
            ); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$username.' gagal masuk karena Tidak menceklis reCaptcha pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);
    
            exit(json_encode($message));
        }
    }

    private function _run_validation($username)
    {
        $this->form_validation->set_rules(
			'username',
			'Nama Pengguna',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Nama Pengguna.'
			)
		);
		$this->form_validation->set_rules(
			'password',
			'Kata Sandi',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kata Sandi.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			$error = form_error('username') ? form_error('username') : (form_error('password') ? form_error('password') : '');
			$message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$username.' gagal masuk karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
    }

    private function _check_username($username)
    {
        $user = $this->auth->check_username($username); 
		
        if ($user === FALSE)
        {
            $message = array(
                'success' => FALSE,
				'message' => 'Nama Pengguna Salah'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$username.' gagal masuk karena error: Nama Pengguna Salah pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);
            
			exit(json_encode($message));
		}

        return $user;
    }

    private function _check_password($user, $password)
    {
        $valid_password = $this->auth->check_password($user['username'], $password);
		
        if ($valid_password === FALSE)
        {
			$this->_update_counter_wrong_pwd($user);

			$message = array(
				'success' => FALSE,
				'message' => 'Kata Sandi Salah'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$user['username'].' gagal masuk karena error: Kata Sandi salah pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
    }

    private function _update_counter_wrong_pwd($user)
    {
        $counter_wrong_password = $this->auth->update_counter_wrong_pwd($user);

        if ($counter_wrong_password >= 10 && $this->auth->update_user_locked($user['id_user']))
        {
            $message = array(
                'success' => FALSE,
                'message' => "Akun Anda Terkunci ($counter_wrong_password x Salah), Silahkan Hubungi Admin"
            ); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$user['username'].' gagal masuk karena Akun nya terkunci setelah salah memasukkan kata sandi sebanyak '.$counter_wrong_password.' X pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

            exit(json_encode($message));
        }
    }

    private function _check_recaptcha($username)
    {
		$check = array(
			'secret' => KEY_RECAPTCHA,
			'response' => $this->input->post('recaptcha')
		);

        $response = $this->_curl_recaptcha($check);

        $this->_is_recaptcha_error($response);
        
        $this->_is_recaptcha_not_valid($response);
	}

    private function _curl_recaptcha($data)
    {
        $response = $this->helper->curl('recaptcha', $data);

        return $response;
    }

    private function _is_recaptcha_error($response)
    {
        if ($response['http_code'] != 200)
        {
			$message = array(
				'success' => FALSE,
				'message' => $response['response']['error']
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] -'.$username.' gagal masuk karena error: '.$response['http_code'].'('.$response['response']['error'].') pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
    }

    private function _is_recaptcha_not_valid($response)
    {
        if ($response['response']['success'] === FALSE)
        {
			$message = array(
				'success' => FALSE,
				'message' => 'Periksa Kembali reCaptcha',
				'response' => $response['response']
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] -'.$username.' gagal masuk karena error: Tidak menceklis reCaptcha / reCaptcha timeout pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
    }

    private function _check_is_locked($user)
    {
        if ($user['counter_wrong_pwd'] >= 10 && $user['is_active'] === 3)
        {
			$message = array(
				'success' => FALSE,
				'message' => 'Akun Anda Terkunci, Silahkan Hubungi Admin'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$user['username'].' gagal masuk karena Akun nya terkunci pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
    }

    private function _check_is_inactive($user)
    {
        if ($user['is_active'] === 2)
        {
			$message = array(
				'success' => FALSE,
				'message' => 'Akun Anda Tidak Aktif, Silahkan Hubungi Admin'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$user['username'].' gagal masuk karena Akun nya tidak aktif pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
    }

    private function _update_data_login($user, $ip_address)
    {
        $update_data_login = $this->auth->update_data_login($user, $ip_address);

        if ($update_data_login === FALSE)
        {
            $message = array(
				'success' => FALSE,
				'message' => 'Terjadi Kesalahan, Gagal Masuk, Silahkan Coba Kembali'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][login][validation] - '.$user['username'].' gagal masuk karena gagal melakukan update informasi login ke db pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
        }

        return $update_data_login;
    }

    private function _set_session($user)
    {
        $last_login = $this->auth->get_last_login_date($user['username']);
        $userdata['logged_in'] = TRUE;
        $userdata['logged_in_at'] = $last_login['last_login_date'];
        $userdata['id'] = $user['id_user'];
	$userdata['name'] = $user['nama_user'];
        $userdata['username'] = $user['username'];
        $userdata['kode_cabang'] = $user['linkage_kode_cabang'];
        $userdata['role'] = $user['linkage_id_role'];
        $userdata['role_list_menu'] = $user['list_id_menu_accessbility'];

        $this->session->set_userdata($userdata);
    }

    private function _set_flashdata_for_first_time_login($counter_login)
    {
        if ($counter_login === 0)
        {
            $this->session->set_flashdata('success', 'Silahkan Ganti Kata Sandi Terlebih Dahulu');
        }
    }

    private function _update_is_logout($id)
    {
        if ($this->auth->update_is_logout($id))
        {
            return TRUE;
        }

        return FALSE;
    }

    private function _set_logout_cookie()
    {
        if (isset($_COOKIE['_log_lst_st_']) === FALSE)
        {
            setcookie("_log_lst_st_", "1", time() + (86400 * 365), "/");
        }
    }

    /* CALL API AFTER UPLOADING DATA (RUN IN BACKGROUND) */
    function run_in_background($scheduler = 0, $ip = '', $session_id = '')
    {
	ignore_user_abort(true);

        $this->_check_is_cli_request();

        $datas = $this->_check_data_is_set($scheduler, $ip, $session_id);

	if($datas === 0)
	{
		exit();
	}

	    $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background]'.($scheduler == 1 ? '[scheduler]' : '').' - Eksekusi dimulai pada '.date('Y-m-d H:i:s').($ip !== '' ? ' dari IP Addr: '.$ip : '');
        $this->helper->logging($log);
	
	$log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem memanggil API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        $token_inquiry_responses = $this->_curl_token_inquiry_polis();

        if (($token_inquiry_responses['http_code'] != 200) || ($token_inquiry_responses['http_code'] == 408 || $token_inquiry_responses['http_code'] == 504))
        {
            $token_inquiry = FALSE;
            $error_token_inquiry = 'Timeout / no response from Token Inquiry / status code <> 200 / '.$token_inquiry_responses['response']['error'];
        } 

        if ($token_inquiry_responses['http_code'] == 200)
        {
            $token_inquiry = $token_inquiry_responses['response']['token'];
            $error_token_inquiry = FALSE;
        }

        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][1] - Sistem Mendapat Response API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        $final_no_polis_db = array();
        $final_doc_status_db = array();
        $final_issued_date_db = array();

	$id_file = 0;

        foreach ($datas as $key => $data)
        {
	    if($id_file !== 0 && $data['id_file'] !== $id_file)
	    {
		$this->up->update_status_file_upload($id_file, 2);
                    
        	$log['type'] = 'info';
        	$log['message'] = '[Auth Controller][run_in_background] - Sistem melakukan update nilai status file upload = 2 untuk id_file: '.$id_file.' karena Proses untuk id_file: '.$id_file.' telah Selesai di eksekusi pada '.date('Y-m-d H:i:s');
        	$this->helper->logging($log);
	    }

	    $id_file = $data['id_file'];

	    $this->up->update_status_file_upload($id_file, 3);

            /*$query = $this->up->query_double_pinjaman($data['no_perjanjian_kredit'], $data['no_rek_pinjaman']);

            $results = $query->result_array(); 

            if ($query && count($results) > 0)
            {
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan pengecekan dobel pinjaman ke DB dengan hasil query lebih dari 0 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);
                
                foreach ($results as $key => $result)
                {
                    $final_no_polis_db[] = $result['POLICY_NO'];
                    $final_doc_status_db[] = $result['DOC_STATUS'];
                    $final_issued_date_db[] = $result['ISSUED_DATE'];
                }

                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][1] - response data DB ACS - No Polis: '.implode('|', $final_no_polis_db).' Doc Status: '.implode('|', $final_doc_status_db).' Issued Date: '.implode('|', $final_issued_date_db);
                $this->helper->logging($log);
                
                $no_polis_acs_db = count($final_no_polis_db) === 1 ? $final_no_polis_db[0] : implode('|', $final_no_polis_db);
                
                switch ((int)$final_doc_status_db[0])
                {
                    case 0:
                        $doc_status = 'Sedang Proses di ACS';
                        break;
                    
                    case 1:
                        $doc_status = 'Draft di ACS';
                        break;
                    
                    case 3:
                        $doc_status = 'Terbit di ACS';
                        break;
                        
                    default:
                        $doc_status = $final_doc_status_db[0];
                        break;
                }

                $this->up->update_flag_status_terbit($data['id'], 2);

                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_terbit = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_no_polis_acs($data['id'], $no_polis_acs_db);

                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update no_polis_acs = '.$no_polis_acs_db.' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_keterangan($data['id'], $doc_status);

                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update keterangan dengan nilai doc_status = '.$doc_status.' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_flag_status_validasi_web($data['id'], 2);
                    
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                sleep(1);

                continue;
            }*/
            $query = 0;
            if ($query === 0) // && count($results)
            {
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan pengecekan double pinjaman ke DB dengan hasil query = 0 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_flag_status_terbit($data['id'], 1);
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_terbit = 1 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $token_polis_responses = $this->_curl_token_create_polis();

                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem memanggil API Token Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                if ($token_polis_responses['http_code'] == 408 || $token_polis_responses['http_code'] == 504)
                {
                    $this->up->update_flag_status_terbit($data['id'], 7);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit = 7 karena error saat memanggil API Token Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);
                    
                    $this->up->update_keterangan($data['id'], 'Timeout / no response from ACS');
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = Timeout/ no response from ACS karena error saat memanggil API Token Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][1] - response data API Token Create Polis - timeout: '.implode('|', $token_polis_responses);
                    $this->helper->logging($log);

                    sleep(1);

                    continue;
                }

                if ($token_polis_responses['http_code'] != 200)
                {
                    $this->up->update_flag_status_terbit($data['id'], 4);
                        
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_terbit = 4 karena error saat memanggil API Token Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);
                    
                    $this->up->update_keterangan($data['id'], $token_polis_responses['response']['error']);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = '.$token_polis_responses['response']['error'].' karena error saat memanggil API Token Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);
                    
                    $this->up->update_flag_status_validasi_web($data['id'], 0);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 0 karena error saat memanggil API Token Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][1] - response data API Token Create Polis - status code <> 200: '.$token_polis_responses['http_code'].'|'.implode('|', $token_polis_responses['response']);
                    $this->helper->logging($log);

                    sleep(1);

                    continue;
                }

                $this->up->update_flag_status_terbit($data['id'], 6);

                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_terbit = 6 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

		        $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][1] - request data ke API Create Polis: '.implode('|', $data);
                $this->helper->logging($log);

                $this->_insert_request_api_acs($data);

                $create_polis_responses = $this->_curl_create_polis($token_polis_responses['response']['access_token'], $data);
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][0] - Sistem memanggil API Create Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                if ($create_polis_responses['http_code'] == 408 || $create_polis_responses['http_code'] == 504)
                {
                    $this->up->update_flag_status_terbit($data['id'], 7);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit = 7 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);
                    
                    $this->up->update_keterangan($data['id'], 'Timeout / no response from ACS');
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = Timeout/ no response from ACS untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][1] - response data API Create Polis - timeout: '.$create_polis_responses['http_code'].'|'.implode('|', $create_polis_responses['response']);
                    $this->helper->logging($log);

                    $this->_insert_response_api_acs($data, $create_polis_responses);

                    sleep(1);

                    continue;
                }

                if (($create_polis_responses['response']['message'] && $create_polis_responses['response']['message'] !== 'SUCCESS' && $create_polis_responses['response']['noSertifikat'] === NULL) || ($create_polis_responses['http_code'] != 200))
                {
                    $this->up->update_flag_status_terbit($data['id'], 4);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit = 4 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $keterangan = $create_polis_responses['response']['error'].' / '.$create_polis_responses['response']['message'];
                    
                    $this->up->update_keterangan($data['id'], $keterangan);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = '.$keterangan.' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);
                    
                    $this->up->update_flag_status_validasi_web($data['id'], 0);
                    
                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 0 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $log['type'] = 'error';
                    $log['message'] = '[Auth Controller][run_in_background][1] - response data API Create Polis - gagal/reject: '.$create_polis_responses['http_code'].'|'.strval($create_polis_responses['response']['status']).'|'.strval($create_polis_responses['response']['success']).'|'.$create_polis_responses['response']['message'].'|'.$create_polis_responses['response']['kodeResponse'][0];
                    $this->helper->logging($log);

                    $this->_insert_response_api_acs($data, $create_polis_responses);

                    if ($create_polis_responses['http_code'] != 200)
                    {
                        $log['type'] = 'error';
                        $log['message'] = '[Auth Controller][run_in_background][1] - response data API Create Polis - status code <> 200: '.$create_polis_responses['http_code'].'|'.implode('|', $create_polis_responses['response']);
                        $this->helper->logging($log);

                        $this->_insert_response_api_acs($data, $create_polis_responses);
                    }

                    sleep(1);

                    continue;
                }

                if ($create_polis_responses['response']['success'] && $create_polis_responses['response']['message'] === 'SUCCESS' && $create_polis_responses['response']['noSertifikat'])
                {
                    $no_polis_acs = $create_polis_responses['response']['noSertifikat'];
                    $this->up->update_no_polis_acs($data['id'], $no_polis_acs);

                    $log['type'] = 'info';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update no_polis_acs = '.$no_polis_acs.' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $this->up->update_flag_status_terbit($data['id'], 3);

                    $log['type'] = 'info';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_terbit = 3 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $log['type'] = 'info'; 
                    $log['message'] = '[Auth Controller][run_in_background][1] - response data API Create Polis - sukses: '.$create_polis_responses['http_code'].'|'.strval($create_polis_responses['response']['status']).'|'.strval($create_polis_responses['response']['success']).'|'.$create_polis_responses['response']['message'].'|'.$create_polis_responses['response']['kodeResponse'][0].'|'.$create_polis_responses['response']['noRekening'].'|'.$create_polis_responses['response']['noSertifikat'].'|'.$create_polis_responses['response']['tanggalRekam'].'|'.$create_polis_responses['response']['keteranganResponse'];
                    $this->helper->logging($log);

                    $this->_insert_response_api_acs($data, $create_polis_responses);

		    if($data['counter_inquiry_polis'] === $data['max_counter_inquiry'])
                    {
                        continue;
                    }

		    $this->up->update_counter_inquiry($data['id'], $data['counter_inquiry_polis']);
                        
                    $log['type'] = 'info';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai counter_inquiry_polis = '.($data['counter_inquiry_polis'] + 1).' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    if ($token_inquiry === FALSE && $error_token_inquiry !== FALSE)
                    {
                        $this->up->update_flag_status_inquiry_terbit($data['id'], 7);
                            
                        $log['type'] = 'error';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_inquiry_terbit = 7 karena error saat memanggil API Token Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);
                        
                        $this->up->update_keterangan($data['id'], 'Timeout / no response from Inquiry / status code <> 200 / '.$token_inquiry_responses['response']['error']);
                        
                        $log['type'] = 'error';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = '.$token_inquiry_responses['response']['error'].' karena error saat memanggil API Token Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        $this->up->update_flag_status_validasi_web($data['id'], 2);
                    
                        $log['type'] = 'error';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        sleep(1);

                        continue;
                    }

                    $log['type'] = 'info';
                    $log['message'] = '[Auth Controller][run_in_background][0] - request data ke API Inquiry Polis: '.$no_polis_acs;
                    $this->helper->logging($log);

                    $this->_insert_request_api_inquiry($data, $no_polis_acs);
                    
                    $log['type'] = 'info';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    $inquiry_polis_responses = $this->_curl_inquiry_polis($token_inquiry, $no_polis_acs);

                    if ($inquiry_polis_responses['response']['data'] === NULL && $inquiry_polis_responses['response']['message'] !== 'Sukses')
                    {   
			$this->up->update_keterangan($data['id'], $inquiry_polis_responses['response']['message']);
                        
                        $log['type'] = 'error';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = '.$inquiry_polis_responses['response']['message'].' karena error saat memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        $log['type'] = 'error'; 
                        $log['message'] = '[Auth Controller][run_in_background][1] - response data API Inquiry Polis - tidak ada data / error: '.implode('|', $inquiry_polis_responses['response']);
                        $this->helper->logging($log);

                        $this->_insert_response_api_inquiry($data, $inquiry_polis_responses);

			if ($inquiry_polis_responses['response']['message'] === 'Token Expired' || $inquiry_polis_responses['response']['message'] === 'Invalid Token')
                        {
                            $log['type'] = 'info';
                            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem memanggil Ulang API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
                            $this->helper->logging($log);

                            $token_inquiry_responses = $this->_curl_token_inquiry_polis();

                            if (($token_inquiry_responses['http_code'] != 200) || ($token_inquiry_responses['http_code'] == 408 || $token_inquiry_responses['http_code'] == 504))
                            {
                                $token_inquiry = FALSE;
                                $error_token_inquiry = 'Timeout / no response from Token Inquiry / status code <> 200 / '.$token_inquiry_responses['response']['error'];
                            } 

                            if ($token_inquiry_responses['http_code'] == 200)
                            {
                                $token_inquiry = $token_inquiry_responses['response']['token'];
                                $error_token_inquiry = FALSE;
                            }

                            $log['type'] = 'info';
                            $log['message'] = '[Auth Controller][run_in_background][1] - Sistem Mendapat Response Ulang API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
                            $this->helper->logging($log);

			    $this->_recall_inquiry_polis($data, $no_polis_acs, $token_inquiry, $error_token_inquiry);
                        } 

                        sleep(1);

			continue;
                    }

                    $log['type'] = 'info'; 
                    $log['message'] = '[Auth Controller][run_in_background][1] - response data API Inquiry Polis - sukses: '.$inquiry_polis_responses['response']['trxDateResponse'].'|'.$inquiry_polis_responses['response']['message'].'|'.implode('|', $inquiry_polis_responses['response']['data']).'|'.implode('|', $inquiry_polis_responses['response']['data']['premiums'][0]).'|'.$inquiry_polis_responses['response']['transactionId'].'|'.$inquiry_polis_responses['response']['errorNumber'].'|'.$inquiry_polis_responses['response']['status'];
                    $this->helper->logging($log);

                    $this->_insert_response_api_inquiry($data, $inquiry_polis_responses);

                    if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 3)
                    {
                        $this->up->update_flag_status_terbit($data['id'], 5);
                        
                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit = 5 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);
                        
                        $this->up->update_flag_status_inquiry_terbit($data['id'], 3);
                        
                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = 3 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        $this->up->update_keterangan($data['id'], 'Fully Terbit');
                        
                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = Fully Terbit saat memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        $this->up->update_flag_status_validasi_web($data['id'], 2);
                    
                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        sleep(1);

                        continue;
                    }
                    
                    if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 0)
                    {   
                        $this->up->update_flag_status_inquiry_terbit($data['id'], 0);
                        
                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = 0 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        sleep(1);

                        continue;
                    }
                    
                    if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 1)
                    {   
                        $this->up->update_flag_status_inquiry_terbit($data['id'], 1);
                        
                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = 1 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        sleep(1);

                        continue;
                    }
                    
                    $this->up->update_flag_status_inquiry_terbit($data['id'], $inquiry_polis_responses['response']['data']['statusTerbit']);
                    
                    $log['type'] = 'info';
                    $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = '.$inquiry_polis_responses['response']['data']['statusTerbit'].' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$inquiry_polis_responses['response']['data']['nomorPolis'].' pada '.date('Y-m-d H:i:s');
                    $this->helper->logging($log);

                    sleep(1);
                }
            }
        }

	if ($id_file !== 0)
	{
		$this->up->update_status_file_upload($id_file, 2);
                    
        	$log['type'] = 'info';
        	$log['message'] = '[Auth Controller][run_in_background] - Sistem melakukan update nilai status file upload = 2 untuk id_file: '.$id_file.' karena Proses Selesai di eksekusi pada '.date('Y-m-d H:i:s');
        	$this->helper->logging($log);
	}

        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background]'.($scheduler == 1 ? '[scheduler]' : '').' - Selesai di eksekusi pada '.date('Y-m-d H:i:s').($ip !== '' ? ' dari IP Addr: '.$ip : '');
        $this->helper->logging($log);

	$flag_background = 0;
	if($this->up->get_data_ready_to_process($flag_background) !== FALSE)
	{
		$log['type'] = 'info';
        	$log['message'] = '[Auth Controller][run_in_background][queue] - Antrian ditemukan, menjalankan antrian selanjutnya pada '.date('Y-m-d H:i:s');
        	$this->helper->logging($log);
		
		$this->run_in_background($flag_background);
	}

	$log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][queue] - Tidak Ada Antrian pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        exit();
    }

    private function _check_is_cli_request()
    {
        if (is_cli() === FALSE) 
        {
            $this->helper->redirect_to_dashboard_page();
        }
    }

    private function _check_data_is_set($flag_scheduler, $ip, $session_id)
    {
        $datas = $this->up->get_data_ready_to_process($flag_scheduler, $ip, $session_id);

        if ($datas === FALSE)
        {
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background]'.($flag_scheduler == 1 ? '[scheduler]' : '').' - Tidak Ada Data yang di proses oleh Sistem di background pada '.date('Y-m-d H:i:s').($ip !== '' ? ' dari IP Addr: '.$ip : '');
            $this->helper->logging($log);

            exit(0);
        }

	$data_scheduler_inquiry = array();
        $datas_scheduler_inquiry = array();

        $data_scheduler_acs_inquiry = array();
        $datas_scheduler_acs_inquiry = array();
        
        foreach ($datas as $key => $scheduler)
        {
            if ($scheduler['flag_status_terbit'] === 7 || $scheduler['flag_status_terbit'] === 8 || ($scheduler['flag_status_validasi_web'] === 1 && $scheduler['no_polis_acs'] == '' && $scheduler['flag_status_terbit'] === 0 && $scheduler['flag_status_terbit_inquiry'] === 0 && $scheduler['counter_inquiry_polis'] === 0))
            {
                $data_scheduler_acs_inquiry['id_file'] = $scheduler['id_file'];
                $data_scheduler_acs_inquiry['nama_file'] = $scheduler['nama_file'];
                $data_scheduler_acs_inquiry['no_batch'] = $scheduler['no_batch'];
                $data_scheduler_acs_inquiry['status'] = $scheduler['status'];
                $data_scheduler_acs_inquiry['upload_by'] = $scheduler['upload_by'];
                $data_scheduler_acs_inquiry['upload_date'] = $scheduler['upload_date'];
                $data_scheduler_acs_inquiry['upload_from'] = $scheduler['upload_from'];
                $data_scheduler_acs_inquiry['id'] = $scheduler['id'];
                $data_scheduler_acs_inquiry['kode_bank'] = $scheduler['kode_bank'];
                $data_scheduler_acs_inquiry['kode_cabang_bank'] = $scheduler['kode_cabang_bank'];
                $data_scheduler_acs_inquiry['kode_cabang_askrindo'] = $scheduler['kode_cabang_askrindo'];
                $data_scheduler_acs_inquiry['kode_produksi'] = $scheduler['kode_produksi'];
                $data_scheduler_acs_inquiry['kode_broker_agent'] = $scheduler['kode_broker_agent'];
                $data_scheduler_acs_inquiry['no_rek_pinjaman'] = $scheduler['no_rek_pinjaman'];
                $data_scheduler_acs_inquiry['no_perjanjian_kredit'] = $scheduler['no_perjanjian_kredit'];
                $data_scheduler_acs_inquiry['tgl_awal_pk'] = $scheduler['tgl_awal_pk'];
                $data_scheduler_acs_inquiry['tgl_akhir_pk'] = $scheduler['tgl_akhir_pk'];
                $data_scheduler_acs_inquiry['jk_waktu_kredit'] = $scheduler['jk_waktu_kredit'];
                $data_scheduler_acs_inquiry['id_valuta'] = $scheduler['id_valuta'];
                $data_scheduler_acs_inquiry['kurs_valuta'] = $scheduler['kurs_valuta'];
                $data_scheduler_acs_inquiry['plafond_kredit'] = $scheduler['plafond_kredit'];
                $data_scheduler_acs_inquiry['suku_bunga_kredit'] = $scheduler['suku_bunga_kredit'];
                $data_scheduler_acs_inquiry['jenis_kredit'] = $scheduler['jenis_kredit'];
                $data_scheduler_acs_inquiry['sub_jenis_kredit'] = $scheduler['sub_jenis_kredit'];
                $data_scheduler_acs_inquiry['type_tujuan_kredit'] = $scheduler['type_tujuan_kredit'];
                $data_scheduler_acs_inquiry['kolektibilitas_kredit'] = $scheduler['kolektibilitas_kredit'];
                $data_scheduler_acs_inquiry['sektor_ekonomi'] = $scheduler['sektor_ekonomi'];
                $data_scheduler_acs_inquiry['sumber_pelunasan_kredit'] = $scheduler['sumber_pelunasan_kredit'];
                $data_scheduler_acs_inquiry['sumber_dana_kredit'] = $scheduler['sumber_dana_kredit'];
                $data_scheduler_acs_inquiry['mekanisme_penyaluran'] = $scheduler['mekanisme_penyaluran'];
                $data_scheduler_acs_inquiry['cif_customer'] = $scheduler['cif_customer'];
                $data_scheduler_acs_inquiry['nama_debitur'] = $scheduler['nama_debitur'];
                $data_scheduler_acs_inquiry['no_ktp_debitur'] = $scheduler['no_ktp_debitur'];
                $data_scheduler_acs_inquiry['tmpt_lahir'] = $scheduler['tmpt_lahir'];
                $data_scheduler_acs_inquiry['tgl_lahir'] = $scheduler['tgl_lahir'];
                $data_scheduler_acs_inquiry['jenis_kelamin'] = $scheduler['jenis_kelamin'];
                $data_scheduler_acs_inquiry['alamat_debitur'] = $scheduler['alamat_debitur'];
                $data_scheduler_acs_inquiry['kode_pos'] = $scheduler['kode_pos'];
                $data_scheduler_acs_inquiry['jenis_pekerjaan'] = $scheduler['jenis_pekerjaan'];
                $data_scheduler_acs_inquiry['status_pegawai'] = $scheduler['status_pegawai'];
                $data_scheduler_acs_inquiry['no_tlp'] = $scheduler['no_tlp'];
                $data_scheduler_acs_inquiry['no_hp'] = $scheduler['no_hp'];
                $data_scheduler_acs_inquiry['npwp'] = $scheduler['npwp'];
                $data_scheduler_acs_inquiry['jenis_agunan'] = $scheduler['jenis_agunan'];
                $data_scheduler_acs_inquiry['jenis_pengikatan'] = $scheduler['jenis_pengikatan'];
                $data_scheduler_acs_inquiry['nilai_agunan'] = $scheduler['nilai_agunan'];
                $data_scheduler_acs_inquiry['tgl_kirim'] = $scheduler['tgl_kirim'];
                $data_scheduler_acs_inquiry['lain_1'] = $scheduler['lain_1'];
                $data_scheduler_acs_inquiry['lain_2'] = $scheduler['lain_2'];
                $data_scheduler_acs_inquiry['broker_agent'] = $scheduler['broker_agent'];
                $data_scheduler_acs_inquiry['kode_broker_agent'] = $scheduler['kode_broker_agent'];
                $data_scheduler_acs_inquiry['nama_broker_agent'] = $scheduler['nama_broker_agent'];
                $data_scheduler_acs_inquiry['nilai_tanggungan'] = $scheduler['nilai_tanggungan'];
                $data_scheduler_acs_inquiry['rate_premi'] = $scheduler['rate_premi'];
                $data_scheduler_acs_inquiry['nilai_premi'] = $scheduler['nilai_premi'];
                $data_scheduler_acs_inquiry['tgl_awal_tanggungan'] = $scheduler['tgl_awal_tanggungan'];
                $data_scheduler_acs_inquiry['tgl_akhir_tanggungan'] = $scheduler['tgl_akhir_tanggungan'];
                $data_scheduler_acs_inquiry['jk_waktu_tanggungan'] = $scheduler['jk_waktu_tanggungan'];
                $data_scheduler_acs_inquiry['no_surat_pengantar'] = $scheduler['no_surat_pengantar'];
                $data_scheduler_acs_inquiry['tgl_no_surat_pengantar'] = $scheduler['tgl_no_surat_pengantar'];
                $data_scheduler_acs_inquiry['flag_status_validasi_web'] = $scheduler['flag_status_validasi_web'];
                $data_scheduler_acs_inquiry['no_polis_acs'] = $scheduler['no_polis_acs'];
                $data_scheduler_acs_inquiry['flag_status_terbit'] = $scheduler['flag_status_terbit'];
                $data_scheduler_acs_inquiry['flag_status_terbit_inquiry'] = $scheduler['flag_status_terbit_inquiry'];
                $data_scheduler_acs_inquiry['counter_inquiry_polis'] = $scheduler['counter_inquiry_polis'];
                $data_scheduler_acs_inquiry['keterangan'] = $scheduler['keterangan'];
                $data_scheduler_acs_inquiry['upload_date'] = $scheduler['upload_date'];
                $data_scheduler_acs_inquiry['upload_from'] = $scheduler['upload_from'];
                $data_scheduler_acs_inquiry['max_counter_inquiry'] = $scheduler['max_counter_inquiry'];

                $datas_scheduler_acs_inquiry[] = $data_scheduler_acs_inquiry;
            }
            elseif ($flag_scheduler == 1 && $ip == '' && $scheduler['no_polis_acs'] !== '' && $scheduler['flag_status_terbit'] === 3)
            {
                $data_scheduler_inquiry['id'] = $scheduler['id'];
		$data_scheduler_inquiry['id_file'] = $scheduler['id_file'];
                $data_scheduler_inquiry['no_rek_pinjaman'] = $scheduler['no_rek_pinjaman'];
                $data_scheduler_inquiry['no_perjanjian_kredit'] = $scheduler['no_perjanjian_kredit'];
                $data_scheduler_inquiry['flag_status_validasi_web'] = $scheduler['flag_status_validasi_web'];
                $data_scheduler_inquiry['no_polis_acs'] = $scheduler['no_polis_acs'];
                $data_scheduler_inquiry['counter_inquiry_polis'] = $scheduler['counter_inquiry_polis'];
                $data_scheduler_inquiry['max_counter_inquiry'] = $scheduler['max_counter_inquiry'];

                $datas_scheduler_inquiry[] = $data_scheduler_inquiry;
            }
        }

        if (count($datas_scheduler_inquiry) > 0)
        {
            $status_inquiry = $this->_scheduler_for_inquiry_only($datas_scheduler_inquiry);

            $set_data = $this->_check_datas_scheduler_acs_inquiry($datas_scheduler_acs_inquiry);

            if ($status_inquiry === 0 && $set_data === 0)
            {
                return $status_inquiry;
            }
            
            if ($status_inquiry === 0 && $set_data === 1)
            {
                return $datas_scheduler_acs_inquiry;
            }
        } 
        elseif (count($datas_scheduler_inquiry) === 0 && count($datas_scheduler_acs_inquiry) > 0)
        {
            return $datas_scheduler_acs_inquiry;
        }
    }
    
    private function _scheduler_for_inquiry_only($datas)
    {
        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Eksekusi Scheduler Inquiry dimulai pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

	$log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem memanggil API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        $token_inquiry_responses = $this->_curl_token_inquiry_polis();

        if (($token_inquiry_responses['http_code'] != 200) || ($token_inquiry_responses['http_code'] == 408 || $token_inquiry_responses['http_code'] == 504))
        {
            $token_inquiry = FALSE;
            $error_token_inquiry = 'Timeout / no response from Token Inquiry / status code <> 200 / '.$token_inquiry_responses['response']['error'];
        } 

        if ($token_inquiry_responses['http_code'] == 200)
        {
            $token_inquiry = $token_inquiry_responses['response']['token'];
            $error_token_inquiry = FALSE;
        }

        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem Mendapat Response API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        foreach ($datas as $data)
        {
            $no_polis_acs = $data['no_polis_acs'];

            if($data['counter_inquiry_polis'] === $data['max_counter_inquiry'])
            {
                continue;
            }

            $this->up->update_counter_inquiry($data['id'], $data['counter_inquiry_polis']);
                
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai counter_inquiry_polis = '.($data['counter_inquiry_polis'] + 1).' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            if ($token_inquiry === FALSE && $error_token_inquiry !== FALSE)
            {
                $this->up->update_flag_status_inquiry_terbit($data['id'], 7);
                    
                $log['type'] = 'error';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update flag_status_inquiry_terbit = 7 karena error saat memanggil API Token Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);
                
                $this->up->update_keterangan($data['id'], $error_token_inquiry);
                
                $log['type'] = 'error';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai keterangan = '.$error_token_inquiry.' karena error saat memanggil API Token Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_flag_status_validasi_web($data['id'], 2);
            
                $log['type'] = 'error';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                sleep(1);

                continue;
            }

            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - request data ke API Inquiry Polis: '.$no_polis_acs;
            $this->helper->logging($log);

            $this->_insert_request_api_inquiry($data, $no_polis_acs);

            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

	    $inquiry_polis_responses = $this->_curl_inquiry_polis($token_inquiry_responses['response']['token'], $no_polis_acs);
        
            if ($inquiry_polis_responses['response']['data'] === NULL && $inquiry_polis_responses['response']['message'] !== 'Sukses')
            {   
		$this->up->update_keterangan($data['id'], $inquiry_polis_responses['response']['message']);
                        
                $log['type'] = 'error';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai keterangan = '.$inquiry_polis_responses['response']['message'].' karena error saat memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $log['type'] = 'error'; 
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - response data API Inquiry Polis - tidak ada data / error: '.implode('|', $inquiry_polis_responses['response']);
                $this->helper->logging($log);

                $this->_insert_response_api_inquiry($data, $inquiry_polis_responses);

                if ($inquiry_polis_responses['response']['message'] === 'Token Expired' || $inquiry_polis_responses['response']['message'] === 'Invalid Token')
                {
                	$log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem memanggil Ulang API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

                        $token_inquiry_responses = $this->_curl_token_inquiry_polis();

                        if (($token_inquiry_responses['http_code'] != 200) || ($token_inquiry_responses['http_code'] == 408 || $token_inquiry_responses['http_code'] == 504))
                        {
                        	$token_inquiry = FALSE;
                                $error_token_inquiry = 'Timeout / no response from Token Inquiry / status code <> 200 / '.$token_inquiry_responses['response']['error'];
                        } 

                        if ($token_inquiry_responses['http_code'] == 200)
                        {
                                $token_inquiry = $token_inquiry_responses['response']['token'];
                                $error_token_inquiry = FALSE;
                        }

                        $log['type'] = 'info';
                        $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem Mendapat Response Ulang API Token Inquiry Polis pada '.date('Y-m-d H:i:s');
                        $this->helper->logging($log);

			$this->_recall_inquiry_polis($data, $no_polis_acs, $token_inquiry, $error_token_inquiry);
                } 

                sleep(1);

		continue;

		/*if ($inquiry_polis_responses['response']['message'] !== 'Token Expired')
                {
			continue;
		}*/
            }

            $log['type'] = 'info'; 
            $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - response data API Inquiry Polis - sukses: '.$inquiry_polis_responses['response']['trxDateResponse'].'|'.$inquiry_polis_responses['response']['message'].'|'.implode('|', $inquiry_polis_responses['response']['data']).'|'.implode('|', $inquiry_polis_responses['response']['data']['premiums'][0]).'|'.$inquiry_polis_responses['response']['transactionId'].'|'.$inquiry_polis_responses['response']['errorNumber'].'|'.$inquiry_polis_responses['response']['status'];
            $this->helper->logging($log);

            $this->_insert_response_api_inquiry($data, $inquiry_polis_responses);

            if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 3)
            {
                $this->up->update_flag_status_terbit($data['id'], 5);
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_terbit = 5 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);
                
                $this->up->update_flag_status_inquiry_terbit($data['id'], 3);
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_terbit_inquiry = 3 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_keterangan($data['id'], 'Fully Terbit');
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai keterangan = Fully Terbit saat memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                $this->up->update_flag_status_validasi_web($data['id'], 2);
            
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                sleep(1);

                continue;
            }
            
            if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 0)
            {   
                $this->up->update_flag_status_inquiry_terbit($data['id'], 0);
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_terbit_inquiry = 0 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                sleep(1);

                continue;
            }
            
            if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 1)
            {   
                $this->up->update_flag_status_inquiry_terbit($data['id'], 1);
                
                $log['type'] = 'info';
                $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_terbit_inquiry = 1 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
                $this->helper->logging($log);

                sleep(1);

                continue;
            }
            
            $this->up->update_flag_status_inquiry_terbit($data['id'], $inquiry_polis_responses['response']['data']['statusTerbit']);
            
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Sistem melakukan update nilai flag_status_terbit_inquiry = '.$inquiry_polis_responses['response']['data']['statusTerbit'].' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            sleep(1);
        }

        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][scheduler][inquiry] - Scheduler Inquiry selesai di eksekusi pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        return 0;
    }
    
    private function _check_datas_scheduler_acs_inquiry($datas_scheduler_acs_inquiry)
    {
        if (count($datas_scheduler_acs_inquiry) > 0)
        {
            return 1;
        }
        else 
        {
            return 0;
        }
    }

    private function _recall_inquiry_polis($data, $no_polis_acs, $token_inquiry, $error_token_inquiry)
    {
        if ($token_inquiry === FALSE && $error_token_inquiry !== FALSE) 
        {
            $this->up->update_flag_status_inquiry_terbit($data['id'], 7);
                
            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update flag_status_inquiry_terbit = 7 karena error saat memanggil API Token Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);
            
            $this->up->update_keterangan($data['id'], $error_token_inquiry);
            
            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = '.$error_token_inquiry.' karena error saat memanggil API Token Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            $this->up->update_flag_status_validasi_web($data['id'], 2);
        
            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);
            
            return;
        }
        
        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        $inquiry_polis_responses = $this->_curl_inquiry_polis($token_inquiry, $no_polis_acs);

        if ($inquiry_polis_responses['response']['data'] === NULL && $inquiry_polis_responses['response']['message'] !== 'Sukses')
        {   
            $this->up->update_keterangan($data['id'], $inquiry_polis_responses['response']['message']);
            
            $log['type'] = 'error';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = '.$inquiry_polis_responses['response']['message'].' karena error saat memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);
            
            $log['type'] = 'error'; 
            $log['message'] = '[Auth Controller][run_in_background][1] - response data API Inquiry Polis - tidak ada data / error: '.implode('|', $inquiry_polis_responses['response']);
            $this->helper->logging($log);
            
            $this->_insert_response_api_inquiry($data, $inquiry_polis_responses);

            return;
        }

        $log['type'] = 'info'; 
        $log['message'] = '[Auth Controller][run_in_background][1] - response data API Inquiry Polis - sukses: '.$inquiry_polis_responses['response']['trxDateResponse'].'|'.$inquiry_polis_responses['response']['message'].'|'.implode('|', $inquiry_polis_responses['response']['data']).'|'.implode('|', $inquiry_polis_responses['response']['data']['premiums'][0]).'|'.$inquiry_polis_responses['response']['transactionId'].'|'.$inquiry_polis_responses['response']['errorNumber'].'|'.$inquiry_polis_responses['response']['status'];
        $this->helper->logging($log);

        $this->_insert_response_api_inquiry($data, $inquiry_polis_responses);

        if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 3)
        {
            $this->up->update_flag_status_terbit($data['id'], 5);
            
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit = 5 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);
            
            $this->up->update_flag_status_inquiry_terbit($data['id'], 3);
            
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = 3 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            $this->up->update_keterangan($data['id'], 'Fully Terbit');
            
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai keterangan = Fully Terbit saat memanggil API Inquiry Polis untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            $this->up->update_flag_status_validasi_web($data['id'], 2);
        
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_validasi_web = 2 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            return;
        }
        
        if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 0)
        {   
            $this->up->update_flag_status_inquiry_terbit($data['id'], 0);
            
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = 0 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            return;
        }
        
        if ($inquiry_polis_responses['response']['data']['statusTerbit'] === 1)
        {   
            $this->up->update_flag_status_inquiry_terbit($data['id'], 1);
            
            $log['type'] = 'info';
            $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = 1 untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
            $this->helper->logging($log);

            return;
        }
        
        $this->up->update_flag_status_inquiry_terbit($data['id'], $inquiry_polis_responses['response']['data']['statusTerbit']);
        
        $log['type'] = 'info';
        $log['message'] = '[Auth Controller][run_in_background][0] - Sistem melakukan update nilai flag_status_terbit_inquiry = '.$inquiry_polis_responses['response']['data']['statusTerbit'].' untuk id: '.$data['id'].' dan no rek: '.$data['no_rek_pinjaman'].' dan no perjaanjian kredit: '.$data['no_perjanjian_kredit'].' dan no sertifikat: '.$no_polis_acs.' pada '.date('Y-m-d H:i:s');
        $this->helper->logging($log);

        return;
    }

    private function _curl_token_create_polis()
    {
        $headers = array(
			'Content-Type:application/x-www-form-urlencoded',
			AUTH_CREATE_POLIS_TOKEN
		);

        $response = $this->helper->curl('polis_token', 'data_polis_token', $headers);

        return $response;
    }
    
    private function _curl_create_polis($token, $data)
    {
        $auth_bearer = "Authorization: Bearer $token";
		$headers = array(
			'Content-Type:application/json',
			$auth_bearer
		);

        $body = array(
            "kodeBank" => $data['kode_bank'],
			"kodeCabangBank" => $data['kode_cabang_bank'],
			"kodeCabangAskrindo" => $data['kode_cabang_askrindo'],
			"noRekeningPinjaman" => $data['no_rek_pinjaman'],
			"noPerjanjianKredit" => $data['no_perjanjian_kredit'],
            "tglAwalPK" => $data['tgl_awal_pk'],
			"tglAkhirPK" => $data['tgl_akhir_pk'],
			"jkWaktuKredit" => $data['jk_waktu_kredit'],
			"idvaluta" => $data['id_valuta'],
			"kursValuta" => $data['kurs_valuta'],
			"plafondKredit" => $data['plafond_kredit'],
			"sukuBungaKredit" => $data['suku_bunga_kredit'],
            "jenisKredit" => $data['jenis_kredit'],
			"subJenisKredit" => $data['sub_jenis_kredit'],
			"kodeProduk" => $data['kode_produksi'],
			"typeTujuanKredit" => $data['type_tujuan_kredit'],
			"kolektibilitasKredit" => $data['kolektibilitas_kredit'],
			"sektorEkonomi" => $data['sektor_ekonomi'],
			"sumberPelunasaKredit" => $data['sumber_pelunasan_kredit'],
			"sumberDanaKredit" => $data['sumber_dana_kredit'],
			"mekanismePenyaluran" => $data['mekanisme_penyaluran'],
            "cifCustomer" => $data['cif_customer'],
			"namaDebitur" => $data['nama_debitur'],
			"noKTPDebitur" => $data['no_ktp_debitur'],
			"tempatLahir" => $data['tmpt_lahir'],
			"tanggalLahir" => $data['tgl_lahir'],
			"jenisKelamin" => $data['jenis_kelamin'],
			"alamatDebitur" => $data['alamat_debitur'],
			"kodePos" => $data['kode_pos'],
			"jenisPekerjaan" => $data['jenis_pekerjaan'],
			"statusKepegawaian" => $data['status_pegawai'],
			"noTelepon" => $data['no_tlp'],
			"noHpDebitur" => $data['no_hp'],
            "npwp" => $data['npwp'],
			"jenisAgunan" => $data['jenis_agunan'],
			"jenisPengikatan" => $data['jenis_pengikatan'],
            "nilaiAgunan" => $data['nilai_agunan'],
			"tglKirim" => $data['tgl_kirim'],
			"other1" => $data['lain_1'],
			"other2" => $data['lain_2'],
            "broker_agen" => $data['broker_agent'],
			"kode_broker_agen" => $data['kode_broker_agent'],
			"nama_broker_agen" => $data['nama_broker_agent'],
			"nilaiPertanggungan" => $data['nilai_tanggungan'],
            "ratePremi" => $data['rate_premi'],
			"nilaiPremi" => $data['nilai_premi'],
            "tanggalAwalPertanggungan" => $data['tgl_awal_tanggungan'],
			"tanggalAkhirPertanggungan" => $data['tgl_akhir_tanggungan'],
			"jkWaktuPertanggungan" => $data['jk_waktu_tanggungan']
        );

        $response = $this->helper->curl('polis', $body, $headers);

        return $response;
    }

    private function _curl_token_inquiry_polis()
    {
        $headers = array(
			'Content-Type:application/json'
		);

        $body = array(
            "clientId" => "1120058000046",
            "clientSecret" => "fe31ffc2-da00-4740-8d03-b2e4e7af6133"
        );

        $response = $this->helper->curl('inquiry_token', $body, $headers);

        return $response;
    }
    
    private function _curl_inquiry_polis($token, $no_polis_acs)
    {
        $authBearer = "Authorization: Bearer $token";

		$headers = array(
			'Content-Type:application/json',
			$authBearer,
			SIGN_INQUIRY_POLIS
		);

		$body = array(
			"nomorPolis" => $no_polis_acs,
			"currency" => "IDR"
		);

        $response = $this->helper->curl('inquiry', $body, $headers);

        return $response;
    }

    private function _insert_request_api_acs($request_acs)
    {
        $final_array = $this->_remove_unneeded_data($request_acs);

        $this->db->insert('t_request_api_acs', $final_array);
    }

    private function _remove_unneeded_data($array)
    {
        unset($array['no_batch']);
        unset($array['nama_file']);
        unset($array['status']);
        unset($array['upload_by']);
        unset($array['upload_date']);
        unset($array['upload_from']);
        unset($array['flag_status_validasi_web']);
        unset($array['no_polis_acs']);
        unset($array['flag_status_terbit']);
        unset($array['flag_status_terbit_inquiry']);
        unset($array['counter_inquiry_polis']);
        unset($array['keterangan']);
        unset($array['upload_from']);
	unset($array['no_surat_pengantar']);
	unset($array['tgl_no_surat_pengantar']);
	unset($array['max_counter_inquiry']);

        return $array;
    }

    private function _insert_request_api_inquiry($array, $no_polis_acs)
    {
        $final_array = array(
            'id' => $array['id'],
            'id_file' => $array['id_file'],
            'no_polis_acs' => $no_polis_acs,
        );
        $this->db->insert('t_request_api_inquiry', $final_array);
    }

    private function _insert_response_api_acs($array, $response)
    {
        $final_array = array(
            'id' => $array['id'],
            'id_file' => $array['id_file'],
            'status_code' => $response['http_code'],
            'created_by' => isset($response['response']['createdBy']) ? $response['response']['createdBy'] : '',
            'created_date' => isset($response['response']['createdDate']) ? $response['response']['createdDate'] : '',
            'modified_by' => isset($response['response']['modifiedBy']) ? $response['response']['modifiedBy'] : '',
            'modified_date' => isset($response['response']['modifiedDate']) ? $response['response']['modifiedDate'] : '',
            'status' => isset($response['response']['status']) ? $response['response']['status'] : '',
            'success' => isset($response['response']['success']) ? $response['response']['success'] : '',
            'message' => isset($response['response']['message']) ? $response['response']['message'] : '',
            'kode_response' => isset($response['response']['kodeResponse']) ? $response['response']['kodeResponse'][0] : '',
            'request_date' => isset($response['response']['requestDate']) ? $response['response']['requestDate'] : '',
            'username' => isset($response['response']['username']) ? $response['response']['username'] : '',
            'password' => isset($response['response']['password']) ? $response['response']['password'] : '',
            'request_id' => isset($response['response']['requestId']) ? $response['response']['requestId'] : '',
            'request_id_original' => isset($response['response']['requestIdOriginal']) ? $response['response']['requestIdOriginal'] : '',
            'flag_rehit' => isset($response['response']['flagRehit']) ? $response['response']['flagRehit'] : '',
            'no_rekening' => isset($response['response']['noRekening']) ? $response['response']['noRekening'] : '',
            'no_sertifikat' => isset($response['response']['noSertifikat']) ? $response['response']['noSertifikat'] : '',
            'tanggal_sertifikat' => isset($response['response']['tanggalSertifikat']) ? $response['response']['tanggalSertifikat'] : '',
            'no_urut_lampiran' => isset($response['response']['noUrutLampiran']) ? $response['response']['noUrutLampiran'] : '',
            'tanggal_rekam' => isset($response['response']['tanggalRekam']) ? $response['response']['tanggalRekam'] : '',
            'keterangan_response' => isset($response['response']['keteranganResponse']) ? $response['response']['keteranganResponse'] : '',
            'addtional_data' => isset($response['response']['addtionalData']) ? $response['response']['addtionalData'] : '',
            'timestamp' => isset($response['response']['timestamp']) ? $response['response']['timestamp'] : '',
            'error' => isset($response['response']['error']) ? $response['response']['error'] : '',
            'path' => isset($response['response']['path']) ? $response['response']['path'] : '',
        );
        $this->db->insert('t_response_api_acs', $final_array);
    }

    private function _insert_response_api_inquiry($array, $response)
    {
        $final_array = array(
            'id' => $array['id'],
            'id_file' => $array['id_file'],
            'status_code' => $response['http_code'],
            'trx_date_response' => isset($response['response']['trxDateResponse']) ? $response['response']['trxDateResponse'] : '',
            'data' => isset($response['response']['data']) ? 1 : 0,
            'data_jangka_waktu' => isset($response['response']['data']) ? $response['response']['data']['jangkaWaktu'] : '',
            'data_nama' => isset($response['response']['data']) ? $response['response']['data']['nama'] : '',
            'data_produk' => isset($response['response']['data']) ? $response['response']['data']['produk'] : '',
            'data_status_terbit' => isset($response['response']['data']) ? $response['response']['data']['statusTerbit'] : '',
            'data_premiums' => isset($response['response']['data']['premiums']) ? 1 : 0,
            'data_premiums_currency' => isset($response['response']['data']['premiums']) ? $response['response']['data']['premiums'][0]['currency'] : '',
            'data_premiums_amount' => isset($response['response']['data']['premiums']) ? $response['response']['data']['premiums'][0]['amountPremium'] : '',
            'data_nomor_polis' => isset($response['response']['data']) ? $response['response']['data']['nomorPolis'] : '',
            'data_status' => isset($response['response']['data']) ? $response['response']['data']['status'] : '',
            'message' => isset($response['response']['message']) ? $response['response']['message'] : '',
            'transaction_id' => isset($response['response']['transactionId']) ? $response['response']['transactionId'] : '',
            'error_number' => isset($response['response']['errorNumber']) ? $response['response']['errorNumber'] : '',
            'status' => isset($response['response']['status']) ? $response['response']['status'] : '',
        );
        $this->db->insert('t_response_api_inquiry', $final_array);
    }
    /* CALL API AFTER UPLOADING DATA (RUN IN BACKGROUND) */
}