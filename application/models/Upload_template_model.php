<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Upload_template_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
	date_default_timezone_set('Asia/Jakarta');
    }

    function submit_file_upload($file)
    {
        if ($this->db->insert('t_file_upload', $file))
        {
            return $this->db->insert_id();
        }

        return FALSE;
    }
    
    function submit_akseptasi_produksi($akseptasi)
    {
        if ($this->db->insert('t_temp_akseptasi_produksi', $akseptasi))
        {
            return $this->db->insert_id();
        }

        return FALSE;    
    }

    function get_info_file_produksi_by_id($id)
    {
        $this->db->distinct();
        $this->db->select('a.nama_file, a.no_batch');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
        $this->db->where('a.id_file', $id);
       
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->row_array();

            return $return;
        }

        return FALSE;
    }
    
    function get_no_rekening($no_rekeniing)
    {
        $this->db->select('b.id, b.no_rek_pinjaman, b.flag_status_terbit');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
        $this->db->where('b.no_rek_pinjaman', $no_rekeniing);
        $this->db->where('b.flag_status_terbit <>', 4);
	$this->db->where('b.flag_status_validasi_web <>', 0);
	$this->db->or_where('b.flag_status_validasi_web <>', 0);
	$this->db->where('b.no_rek_pinjaman', $no_rekeniing);
       
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->row_array();

            return $return;
        }

        return FALSE;
    }

    function check_status_upload($id)
    {
        $this->db->select('b.flag_status_validasi_web');
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
        $this->db->where('a.id_file', $id);
       
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();

            return $return;
        }

        return FALSE;
    }

    function update_status_file_upload($id, $status)
    {
        $this->db->set('status', $status);
        $this->db->where('id_file', $id);

        if ($this->db->update('t_file_upload'))
        {
            return TRUE;
        }

        return FALSE;
    }
    
    function update_batch_number($id, $batch)
    {
        $this->db->set('no_batch', $batch);
        $this->db->where('id_file', $id);

        if ($this->db->update('t_file_upload'))
        {
            return TRUE;
        }

        return FALSE;
    }

    function check_if_file_exist($file_name)
    {
	$this->db->select('nama_file');
	$this->db->from('t_file_upload');
	$this->db->where('nama_file', $file_name);

	$query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            return TRUE;
        }

        return FALSE;
    }

    /* CALL API AFTER UPLOADING DATA (RUN IN BACKGROUND) */
    function check_if_background_running($flag = '')
    {
	$this->db->from('t_file_upload a');
	if($flag === 1)
	{
		$this->db->where('a.status', 1);
	}
	else
	{
		$this->db->where('a.status', 3);
	}
	$this->db->where('a.upload_date >=', date('Y-m-d').' 00:00:00');
       
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();

            return $return;
        }

        return FALSE;
    }

    function get_data_ready_to_process($flag, $ip = '', $session_id = '')
    {
        /* keterangan flag */
        // 0 = run in background
        // 1 = scheduler
        // 2 = untuk ui (pengecekan wajib menggunakan ip address agar bisa upload file dibeda pc)
	/* keterangan flag */
        $this->db->from('t_file_upload a');
        $this->db->join('t_temp_akseptasi_produksi b', 'a.id_file = b.id_file AND a.upload_from = b.upload_from');
        if ($flag == 1)
        {
            $this->db->where('b.flag_status_terbit_inquiry <>', 3);
        }
        if (($flag === 0 || $flag === 2) && $ip !== '')
        {
	    $this->db->where('b.flag_status_validasi_web', 1);
            //$this->db->where('b.upload_from', $ip);
	    $this->db->where('b.session_id', $session_id);
        }
	if ($flag === 0 && $ip == '')
        {
	    $this->db->where('b.flag_status_validasi_web', 1);
	    $this->db->where('b.flag_status_terbit', 0);
	    $this->db->where('b.flag_status_terbit_inquiry', 0);
	    $this->db->where('b.no_polis_acs', '');
	    $this->db->where('b.counter_inquiry_polis', 0);
            $this->db->where('b.upload_date >=', date('Y-m-d').' 00:00:00');
        }
       
        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->result_array();

            return $return;
        }

        return FALSE;
    }

    /*function query_double_pinjaman($no_perjanjian_kredit, $no_rekening)
    {
        $DB_ACS = $this->load->database('DB_ACS', TRUE);
        $DB_ACS->select('POL.POLICY_NO, POL.ISSUED_DATE, POL.DOC_STATUS');
        $DB_ACS->from('UNDERWRITING.UDW_POLICY POL');
        $DB_ACS->join('UNDERWRITING.UDW_POLICY_OBJECT OBJ', 'POL.POLICY_ID = OBJ.POLICY_ID');
        $DB_ACS->where('POL.DOC_TYPE_ID', 2);
        $DB_ACS->where('OBJ.TXT_DATA1', $no_perjanjian_kredit);
        $DB_ACS->where('OBJ.TXT_DATA2', $no_rekening);
       
        $query = $DB_ACS->get();

        return $query;
    }*/

    function update_flag_status_terbit($id, $status)
    {
        $this->db->set('flag_status_terbit', $status);
        $this->db->where('id', $id);

        if ($this->db->update('t_temp_akseptasi_produksi'))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update_no_polis_acs($id, $no_polis)
    {
        $this->db->set('no_polis_acs', $no_polis);
        $this->db->where('id', $id);

        if ($this->db->update('t_temp_akseptasi_produksi'))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update_keterangan($id, $keterangan)
    {
        $this->db->set('keterangan', $keterangan);
	$this->db->where('id', $id);

        if ($this->db->update('t_temp_akseptasi_produksi'))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update_flag_status_inquiry_terbit($id, $status)
    {
        
        $this->db->set('flag_status_terbit_inquiry', $status);
        $this->db->where('id', $id);

        if ($this->db->update('t_temp_akseptasi_produksi'))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update_counter_inquiry($id, $counter)
    {
        $this->db->set('counter_inquiry_polis', ($counter + 1));
        $this->db->where('id', $id);

        if ($this->db->update('t_temp_akseptasi_produksi'))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update_flag_status_validasi_web($id, $status)
    {
        $this->db->set('flag_status_validasi_web', $status);
        $this->db->where('id', $id);

        if ($this->db->update('t_temp_akseptasi_produksi'))
        {
            return TRUE;
        }

        return FALSE;
    }
    /* CALL API AFTER UPLOADING DATA (RUN IN BACKGROUND) */
}