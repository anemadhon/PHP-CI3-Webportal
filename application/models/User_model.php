<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
	date_default_timezone_set('Asia/Jakarta');
    }
    
    function get()
    {
        $this->db->select('a.id_user, a.nama_user as name, a.username, a.is_active, b.nama_role as role, c.kode_cabang, c.nama_cabang');
        $this->db->from('t_management_user a');
        $this->db->join('t_management_role b', 'a.linkage_id_role = b.id_role');
        $this->db->join('t_prm_kantor_cbg c', 'a.linkage_kode_cabang = c.kode_cabang','left');
        $this->db->where('a.flag_delete ', 0);

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();
            
            return $return;
        }

        return FALSE;
    }

    function get_by_id($id)
    {
        $this->db->select('a.id_user, a.nama_user as name, a.username, a.password, a.linkage_id_role, a.is_active, b.nama_role as role, c.kode_cabang, c.nama_cabang');
        $this->db->from('t_management_user a');
        $this->db->join('t_management_role b', 'a.linkage_id_role = b.id_role');
        $this->db->join('t_prm_kantor_cbg c', 'a.linkage_kode_cabang = c.kode_cabang', 'left');
        $this->db->where('a.flag_delete ', 0);
		$this->db->where('a.id_user', $id);

		$query = $this->db->get();

		if (count($query->result_array()) === 1)
        {
			$return = $query->row_array();

            return $return;
		}

        return FALSE;
    }

    function submit($user)
    {
        if ($this->db->insert('t_management_user', $user))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update($user)
    {
        $update = array(
			'nama_user' => $user['nama_user'],
			'username' => $user['username'],
			'linkage_id_role' => $user['linkage_id_role'],
			'linkage_kode_cabang' => $user['linkage_kode_cabang'],
			'modified_by' => $user['modified_by'],
			'modified_date' => $user['modified_date']
		);

		$this->db->where('flag_delete', 0);
		$this->db->where('id_user', $user['id_user']);

		if ($this->db->update('t_management_user', $update))
        {
			return TRUE;
        }

        return FALSE;
    }

    function is_unique($username)
    {
	$this->db->select('username');
	$this->db->from('t_management_user');
	$this->db->where('flag_delete', 0);
	$this->db->where('username', $username);

	$query = $this->db->get();

	if (count($query->result_array()) === 1)
	{
		return FALSE;
	}

	return TRUE;
    }

    function soft_delete($user)
    {
        $update = array(
			'flag_delete' => 1,
            'deleted_by' => $user['deleted_by'],
            'deleted_date' => $user['deleted_date']
		);

		$this->db->where('id_user', $user['id_user']);

		if ($this->db->update('t_management_user', $update))
        {
			return TRUE;
        }

        return FALSE;
    }

    function unlock($user)
    {
        $update = array(
			'is_active' => 1,
			'counter_wrong_pwd' => 0,
			'modified_by' => $user['username'],
			'modified_date' => date('Y-m-d H:i:s')
		);

		$this->db->where('id_user', $user['id']);

		if ($this->db->update('t_management_user', $update))
        {
			return TRUE;
        } 
        
        return FALSE;
    }

    function disable($user)
    {
        $update = array(
			'is_active' => 2,
			'modified_by' => $user['username'],
			'modified_date' => date('Y-m-d H:i:s')
		);

		$this->db->where('id_user', $user['id']);

		if ($this->db->update('t_management_user', $update))
        {
			return TRUE;
        } 
        
        return FALSE;
    }
    
    function activate($user)
    {
        $update = array(
			'is_active' => 1,
			'modified_by' => $user['username'],
			'modified_date' => date('Y-m-d H:i:s')
		);

		$this->db->where('id_user', $user['id']);

		if ($this->db->update('t_management_user', $update))
        {
			return TRUE;
        } 
        
        return FALSE;
    }
}