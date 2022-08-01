<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Menus {
    protected $CI;

	public function __construct()
    {
		$this->CI =& get_instance();
	}
    
    function create_menus()
    {
        $menus = $this->_get_menus();
        
        $output = '';
        
        foreach ($menus['menus'] as $key => $value)
        {
            if ($menus['menus'][$key]['parent'] === 1 && $this->_check_eligible_user_menus($menus['menus'][$key]['index'], $menus['index']) !== 0)
            {
                $output .= '<li class="dropdown '.$this->_set_active_class('parent', $key).'">';
                $output .= '<a href="#" class="nav-link has-dropdown"><i class="'.$menus['menus'][$key]['icon'].'"></i><span>'.$menus['menus'][$key]['text'].'</span></a>';
                $output .= $this->_create_child_menus($key);
                $output .= '</li>';
            }
        }

        return $output;
    }

    function check_eligible_menus()
    {
        $menus = $this->_get_menus();

        $output = 0;

        foreach ($menus['menus'] as $key => $value)
        {
            if ($menus['menus'][$key]['segment_2'] == $this->CI->uri->segment(2) && $this->_check_eligible_user_menus($menus['menus'][$key]['index'], $menus['index']) !== 0)
            {
                $output = 1;
            }
        }

        return $output;
    }

    private function _create_child_menus($parent)
    {
        $menus = $this->_get_menus();

        $output = '';
        $output .= '<ul class="dropdown-menu">';
        
        foreach ($menus['menus'] as $key => $value)
        {
            if ($menus['menus'][$key]['parent'] == $parent && $this->_check_eligible_user_menus($menus['menus'][$key]['index'], $menus['index']) !== 0)
            {
                $output .= '<li class="'.$this->_set_active_class('child', $key).'"><a class="nav-link" href="'.$menus['menus'][$key]['link'].'">'.$menus['menus'][$key]['text'].'</a></li>';
            }
        }

        $output .= '</ul>';

        return $output;
    }

    private function _set_active_class($flag, $parent)
    {
        $menus = $this->_get_menus();

        if ($flag == 'parent' && $this->CI->uri->segment(2) == $menus['menus'][$parent]['segment_2'])
        {
            return 'active';
        }
        
        if ($flag == 'child' && $this->CI->uri->segment(2) == $menus['menus'][$parent]['segment_2'] && $this->CI->uri->segment(3) == $menus['menus'][$parent]['segment_3'] && $this->CI->uri->segment(4) == $menus['menus'][$parent]['segment_4'])
        {
            return 'active';
        }
    }

    private function _check_eligible_user_menus($string, $chars)
    {
        $output = 0;
        
        foreach ($chars as $char)
        {
            if (strpos($string, $char) !== FALSE)
            {
                $output = 1;
            }
        }

        return $output;
    }

    private function _get_menus()
    {
        $menus = $this->menus();

        $user_menus = $this->CI->session->userdata['role_list_menu'];
        $index_user_menus = explode(',', $user_menus);

        $return = array(
            'menus' => $menus,
            'index' => $index_user_menus
        );

        return $return;
    }

    function menus()
    {
        $menus = array(
            'a' => array(
                'parent' => 1,
                'text' => 'Manajemen',
                'icon' => 'fas fa-users-cog',
                'segment_2' => 'management',
                'index' => '12'
            ),
            'a01' => array(
                'parent' => 'a',
                'text' => 'Manajemen Peran',
                'link' => base_url().'dashboard/management/role',
                'segment_2' => 'management',
                'segment_3' => 'role',
                'segment_4' => '',
                'index' => 1

            ),
            'a02' => array(
                'parent' => 'a',
                'text' => 'Manajemen Pengguna',
                'link' => base_url().'dashboard/management/user',
                'segment_2' => 'management',
                'segment_3' => 'user',
                'segment_4' => '',
                'index' => 2

            ),
            'b' => array(
                'parent' => 1,
                'text' => 'Parameter Global',
                'icon' => 'fas fa-globe',
                'segment_2' => 'parameter',
                'index' => '345'
            ),
            'b01' => array(
                'parent' => 'b',
                'text' => 'Kantor Cabang',
                'link' => base_url().'dashboard/parameter/kc',
                'segment_2' => 'parameter',
                'segment_3' => 'kc',
                'segment_4' => '',
                'index' => 3

            ),
            'b02' => array(
                'parent' => 'b',
                'text' => 'Business Partner',
                'link' => base_url().'dashboard/parameter/bp',
                'segment_2' => 'parameter',
                'segment_3' => 'bp',
                'segment_4' => '',
                'index' => 4

            ),
            'b03' => array(
                'parent' => 'b',
                'text' => 'PKS x Business Partner',
                'link' => base_url().'dashboard/parameter/pksxbp',
                'segment_2' => 'parameter',
                'segment_3' => 'pksxbp',
                'segment_4' => '',
                'index' => 5

            ),
            'c' => array(
                'parent' => 1,
                'text' => 'Akseptasi',
                'icon' => 'fas fa-clipboard-check',
                'segment_2' => 'acceptance',
                'index' => '678'
            ),
            'c01' => array(
                'parent' => 'c',
                'text' => 'Unduh Template',
                'link' => base_url().'dashboard/acceptance/form/download',
                'segment_2' => 'acceptance',
                'segment_3' => 'form',
                'segment_4' => 'download',
                'index' => 6

            ),
            'c02' => array(
                'parent' => 'c',
                'text' => 'Unggah Template',
                'link' => base_url().'dashboard/acceptance/form/upload',
                'segment_2' => 'acceptance',
                'segment_3' => 'form',
                'segment_4' => 'upload',
                'index' => 7

            ),
            'c03' => array(
                'parent' => 'c',
                'text' => 'Pemantauan Unggah',
                'link' => base_url().'dashboard/acceptance/monitor',
                'segment_2' => 'acceptance',
                'segment_3' => 'monitor',
                'segment_4' => '',
                'index' => 8

            ),
            'd' => array(
                'parent' => 1,
                'text' => 'Laporan',
                'icon' => 'fas fa-poll',
                'segment_2' => 'report',
                'index' => '9101113'
            ),
            'd01' => array(
                'parent' => 'd',
                'text' => 'Produksi Fully Terbit',
                'link' => base_url().'dashboard/report/upload/successed',
                'segment_2' => 'report',
                'segment_3' => 'upload',
                'segment_4' => 'successed',
                'index' => 9

            ),
            'd02' => array(
                'parent' => 'd',
                'text' => 'Produksi Partial Terbit',
                'link' => base_url().'dashboard/report/upload/partial',
                'segment_2' => 'report',
                'segment_3' => 'upload',
                'segment_4' => 'partial',
                'index' => 10

            ),
            'd03' => array(
                'parent' => 'd',
                'text' => 'Produksi Gagal',
                'link' => base_url().'dashboard/report/upload/failed',
                'segment_2' => 'report',
                'segment_3' => 'upload',
                'segment_4' => 'failed',
                'index' => 11

            ),
            'd04' => array(
                'parent' => 'd',
                'text' => 'Produksi Pending',
                'link' => base_url().'dashboard/report/upload/pending',
                'segment_2' => 'report',
                'segment_3' => 'upload',
                'segment_4' => 'pending',
                'index' => 13

            )
        );

        return $menus;
    }
}