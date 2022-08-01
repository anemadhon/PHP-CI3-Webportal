<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Kantor_cabang_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
    }

    function get()
    {
        $this->db->select('kode_cabang, nama_cabang, linkage_kanwil');
        $this->db->from('t_prm_kantor_cbg');

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();
            
            return $return;
        }

        return FALSE;
    }

    function get_by_kode($kode)
    {
        $this->db->select('kode_cabang, nama_cabang');
        $this->db->from('t_prm_kantor_cbg');
        $this->db->where('kode_cabang', $kode);

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->row();
            
            return $return;
        }

        return FALSE;
    }
}