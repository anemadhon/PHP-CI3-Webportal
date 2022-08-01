<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
	date_default_timezone_set('Asia/Jakarta');
    }

    function check_username($username)
    {
        $this->db->from('t_management_user a');
        $this->db->join('t_management_role b', 'a.linkage_id_role = b.id_role');
        $this->db->where('username', $username);

        $query = $this->db->get();

        if (count($query->result_array()) === 1)
        {
            $return = $query->row_array();

            return $return;
        }

        return FALSE;
    }
    
    function check_password($username, $password)
    {
        $this->db->select('password');
        $this->db->from('t_management_user a');
        $this->db->join('t_management_role b', 'a.linkage_id_role = b.id_role');
        $this->db->where('username', $username);

        $query = $this->db->get();

        if (count($query->result_array()) === 1)
        {
            $return = $query->row_array();

            if (password_verify($password, $return['password']))
            {
                return TRUE;
            }

            return FALSE;
        }

        return FALSE;
    }

    function update_counter_wrong_pwd($user)
    {        
        $this->db->set('counter_wrong_pwd', ($user['counter_wrong_pwd']+1));
        $this->db->where('id_user', $user['id_user']);

	    if ($this->db->update('t_management_user'))
        {
		    return ($user['counter_wrong_pwd']+1);
        } 

        return FALSE;
    }
    
    function update_user_locked($id)
    {        
        $this->db->set('is_active', 3);
        $this->db->where('id_user', $id);

	    if ($this->db->update('t_management_user'))
        {
		    return TRUE;
        } 

        return FALSE;
    }
    
    function update_data_login($user, $ip)
    {
        $update = array(
			'is_login' => 1,
			'counter_login' => $user['counter_login']+1,
			'from_ip_address' => $ip,
			'last_login_date' => date('Y-m-d H:i:s')
		);

	    $this->db->where('id_user', $user['id_user']);

	    if ($this->db->update('t_management_user', $update))
        {
		    return TRUE;
        } 
        
        return FALSE;
    }

    function get_last_login_date($username)
    {
        $this->db->select('last_login_date');
        $this->db->from('t_management_user');
        $this->db->where('username', $username);

        $query = $this->db->get();

        if (count($query->result_array()) === 1)
        {
            $return = $query->row_array();

            return $return;
        }

        return FALSE;
    }

    function change_password($password)
    {
        $update = array(
			'password' => password_hash($password['password'], PASSWORD_BCRYPT),
			'modified_by' => $password['username'],
			'modified_date' => date('Y-m-d H:i:s')
		);

	    $this->db->where('id_user', $password['id']);

	    if ($this->db->update('t_management_user', $update))
        {
		    return TRUE;
        } 
        
        return FALSE;
    }

    function update_is_logout($id)
    {        
        $this->db->set('is_login', 2);
        $this->db->where('id_user', $id);

	    if ($this->db->update('t_management_user'))
        {
		    return TRUE;
        } 

        return FALSE;
    }
}