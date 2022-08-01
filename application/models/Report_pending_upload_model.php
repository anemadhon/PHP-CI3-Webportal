<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Report_pending_upload_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
    }

    function get($search, $columns_for_search, $order, $columns_for_order, $length = 0, $start = 0)
    {
	$this->db->select('a.nama_file, a.no_batch, a.upload_by, b.*');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
	if($this->session->userdata('kode_cabang') != '')
        {
            $this->db->where('kode_cabang_askrindo', $this->session->userdata('kode_cabang'));	
        }
	$this->db->group_start();
        $this->db->where('b.flag_status_validasi_web', 2);
        $this->db->where('b.flag_status_terbit', 3);
        $this->db->where('b.flag_status_terbit_inquiry <>', 3);
	if ($search != '')
        {
            $this->db->group_start();
            $this->db->or_like($columns_for_search);
            $this->db->group_end();
        }
	$this->db->group_end();
	$this->db->or_group_start();
        $this->db->where('b.flag_status_validasi_web', 1);
	$this->db->where_in('b.flag_status_terbit', array(3,7,8));
        $this->db->where("ISNULL(b.flag_status_terbit_inquiry,'') <>", 3);
	if ($search != '')
        {
            $this->db->group_start();
            $this->db->or_like($columns_for_search);
            $this->db->group_end();
        }
        $this->db->group_end();

	if ($order)
        {
            $this->db->order_by($columns_for_order[$order['0']['column']], $order['0']['dir']);
        }
        if (!$order)
        {
            $this->db->order_by('b.id_file', 'asc');
        }
        if ($length !== 0)
        {
            $this->db->limit($length, $start);
        }
       
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();

            return $return;
        }

        return FALSE;
    }
}