<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kantor_cabang extends CI_Controller {
	public function __construct()
    {
		parent::__construct();

		$this->load->model('Kantor_cabang_model', 'cabang');
		
		$this->helper->check_is_login();
	}

    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Kantor Cabang',
            'sub' => 'List'
		);

		$this->load->view('dashboard/parameter/kc', $data);
	}

	function get()
    {
		$cabangs = $this->cabang->get();

		$datas = array();
		$data_cabangs = array();

		if ($cabangs)
        {
			foreach ($cabangs as $key => $cabang)
            {
				$data_cabangs['no'] = ((int)$key+1);
				$data_cabangs['nama_cabang'] = $cabang['nama_cabang'];
				$data_cabangs['kode_cabang'] = $cabang['kode_cabang'];
				$data_cabangs['kanwil'] = $cabang['linkage_kanwil'];
				$datas[] = $data_cabangs;
			}
		}

		$json_data = array('data' => $datas);
		exit(json_encode($json_data));
	}
}
