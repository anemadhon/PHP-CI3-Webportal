<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Monitoring_upload_template_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
    }

    function get($search, $columns_for_search, $order, $columns_for_order, $length = 0, $start = 0)
    {
        $this->db->select('a.*, COUNT(a.id_file) AS jumlah_data');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
        if ($search != '')
        {
            $this->db->or_like($columns_for_search);
        }
        $this->db->group_by(array('a.id_file', 'no_batch', 'nama_file', 'status', 'upload_by', 'a.upload_date', 'a.upload_from', 'queue'));
        if ($order)
        {
            $this->db->order_by($columns_for_order[$order['0']['column']], $order['0']['dir']);
        }
        if (!$order)
        {
            $this->db->order_by('a.id_file', 'asc');
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

    function get_total_data()
    {
        $this->db->select('a.id_file');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
	if($this->session->userdata('kode_cabang') != '')
	{
		$this->db->where('kode_cabang_askrindo', $this->session->userdata('kode_cabang'));	
	}
        
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            return count($query->result_array());
        }

        return 0;
    }
    
    function get_total_data_on_progress($id = 0)
    {
        $this->db->select('a.id_file');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
	if($this->session->userdata('kode_cabang') != '')
	{
		$this->db->where('kode_cabang_askrindo', $this->session->userdata('kode_cabang'));	
	}
        if ($id !== 0)
        {
            $this->db->where('a.id_file', $id);
        }
	$this->db->where('b.flag_status_validasi_web', 1);
	
        
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            return count($query->result_array());
        }

        return 0;
    }
    
    function get_total_data_successed($id = 0)
    {
        $this->db->select('a.id_file');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
	if($this->session->userdata('kode_cabang') != '')
	{
		$this->db->where('kode_cabang_askrindo', $this->session->userdata('kode_cabang'));	
	}
        if ($id !== 0)
        {
            $this->db->where('a.id_file', $id);
        }
        $this->db->where('b.flag_status_validasi_web', 2);
        $this->db->where('b.flag_status_terbit', 5);
        $this->db->where('b.flag_status_terbit_inquiry', 3);
        $this->db->or_where('b.flag_status_terbit', 2);
        $this->db->where('b.flag_status_terbit_inquiry', 0);
	$this->db->where('b.flag_status_validasi_web', 2);
        if ($id !== 0)
        {
            $this->db->where('a.id_file', $id);
        }
        $this->db->or_where('b.flag_status_terbit', 3);
        $this->db->where('b.flag_status_terbit_inquiry <>', 3);
	$this->db->where('b.flag_status_validasi_web', 2);
        if ($id !== 0)
        {
            $this->db->where('a.id_file', $id);
        }
        
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            return count($query->result_array());
        }

        return 0;
    }

    function get_failed_data($id = 0)
    {
        $this->db->select('b.*');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
	if($this->session->userdata('kode_cabang') != '')
	{
		$this->db->where('kode_cabang_askrindo', $this->session->userdata('kode_cabang'));	
	}
        if ($id !== 0)
        {
            $this->db->where('a.id_file', $id);
        }
        $this->db->where('b.flag_status_validasi_web', 0);
        $this->db->or_where('b.flag_status_terbit', 4);
        $this->db->where('b.flag_status_validasi_web', 0);
        if ($id !== 0)
        {
            $this->db->where('a.id_file', $id);
        }
        
	$this->db->order_by('b.id', 'asc');

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            return $query->result_array();
        }

        return FALSE;
    }

    function get_data_uploaded_all()
    {
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
	if($this->session->userdata('kode_cabang') != '')
	{
		$this->db->where('kode_cabang_askrindo', $this->session->userdata('kode_cabang'));	
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