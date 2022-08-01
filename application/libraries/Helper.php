<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Helper {
    protected $CI;

	public function __construct()
    {
		$this->CI =& get_instance();
	}

    function check_connection()
    {
        return (bool)@fsockopen('www.google.com', 80);
    }

    function check_is_login($isset_login_time = 0)
    {
        if ($this->CI->session->has_userdata('logged_in') === FALSE)
        {
            if ($isset_login_time === 1) {
                exit('');
            }

			$this->redirect_to_login_page();
		}
    }

    function redirect_to_login_page()
    {
        redirect(base_url());
        exit();
    }

    function prevent_to_login_page()
    {
        if ($this->CI->session->has_userdata('logged_in'))
        {
			$this->redirect_to_dashboard_page();
		}
    }
    
    function check_eligible_user_menus()
    {
        if ($this->CI->menus->check_eligible_menus() === 0)
        {
            $this->redirect_to_dashboard_page();
		}
    }
    
    function redirect_to_dashboard_page()
    {
        redirect(base_url().'dashboard');
        exit();
    }

    function logging($log)
    {	
	$log['created_from'] = $this->get_ip_address();
        log_message($log['type'], $log['message']);
        $this->_insert_log_to_db($log);
    }

    function get_ip_address()
    {
        return isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
    }

    function curl($flag, $data, $header = '')
    {
        $datas = $this->_set_url_and_post_data($flag, $data);

        $init = curl_init($datas['url']);
        if ($flag !== 'recaptcha' && $header !== '') {
            curl_setopt($init, CURLOPT_HTTPHEADER, $header);
        }
        if ($flag == 'polis_token') {
            curl_setopt($init, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
            curl_setopt($init, CURLOPT_USERPWD, "USER_CREATE_POLIS:PWD_CREATE_POLIS");
        }
		curl_setopt($init, CURLOPT_POST, 1);
        curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($init, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($init, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($init, CURLOPT_POSTFIELDS, $datas['post']);

		$result = curl_exec($init);
		$http_code = curl_getinfo($init, CURLINFO_HTTP_CODE);

		curl_close($init); 

		$final_response = json_decode($result, TRUE);

        $response = array(
            'http_code' => $http_code,
            'response' => $final_response
        );

        return $response;
    }

    private function _insert_log_to_db($log)
    {
        $this->CI->db->insert('t_logging', $log);
    }

    private function _set_url_and_post_data($flag, $data)
    {
        if ($flag == 'recaptcha') {
            $response['url'] = URL_RECAPTCHA;
            $response['post'] = http_build_query($data);
        }
        if ($flag == 'polis_token') {
            $response['url'] = URL_CREATE_POLIS_TOKEN;
            $response['post'] = PARAMS_CREATE_POLIS_TOKEN;
        }
        if ($flag == 'polis') {
            $response['url'] = URL_CREATE_POLIS;
            $response['post'] = json_encode($data);
        }
        if ($flag == 'inquiry_token') {
            $response['url'] = URL_INQUIRY_POLIS_TOKEN;
            $response['post'] = json_encode($data);
        }
        if ($flag == 'inquiry') {
            $response['url'] = URL_INQUIRY_POLIS;
            $response['post'] = json_encode($data);
        }

        return $response;
    }
}