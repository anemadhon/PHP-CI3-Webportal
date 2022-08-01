<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Role extends CI_Controller {
	public function __construct()
    {
		parent::__construct();

		$this->load->model('Role_model', 'role');

		$this->helper->check_is_login();
	}

    public function index()
    {
        $this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Manajemen Peran',
            'sub' => 'List'
		);

		$this->load->view('dashboard/management/role', $data);
	}

	function get()
    {
		$roles = $this->role->get();

		$datas = array();
		$data_roles = array();

		if ($roles)
        {
			foreach ($roles as $key => &$role)
            {
				$lists = explode(',', $role['list']);

                $list_menus = $this->_mapping_menus($lists);
				
				$data_roles['no'] = ((int)$key+1);
				$data_roles['role'] = $role['role'];
				$data_roles['description'] = $role['description'];
				$data_roles['list'] = $list_menus;
				$datas[] = $data_roles;
			}
		}

		$json_data = array('data' => $datas);
		exit(json_encode($json_data));
	}

    private function _mapping_menus($lists)
    {
	$menus = $this->menus->menus();

        foreach ($lists as $list)
        {
            foreach ($menus as $idx => $menu)
            {
                if ($menu['parent'] === 1)
                {
                    $main_menu = $menu['text'];
                }

                if ($menus[$idx]['index'] == $list)
                {
                    $list_menus[] = $main_menu.'-'.$menus[$idx]['text'];
                    break;
                }
            }
        }

        return $list_menus;
    }
}