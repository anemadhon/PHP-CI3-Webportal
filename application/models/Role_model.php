<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Role_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
    }

    function get() 
    {
        $this->db->select('id_role, nama_role as role, description, list_id_menu_accessbility as list');
        $this->db->from('t_management_role');

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();
            
            return $return;
        }

        return FALSE;
    }
}