<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
		
        $this->helper->check_is_login();
	}

	public function index()
    {
		$data = array(
			'title' => 'Dashboard'
		);
		$this->load->view('dashboard/index', $data);
	}
}