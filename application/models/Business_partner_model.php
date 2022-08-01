<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Business_partner_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
    }

    function get()
    {
        $this->db->select('id_business_partner as id, nama_business_partner as nama, jenis_business_partner as jenis');
        $this->db->from('t_prm_bsns_ptnr');
        $this->db->where('flag_delete ', 0);

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
        $this->db->select('id_business_partner as id, nama_business_partner as nama, jenis_business_partner as jenis');
        $this->db->from('t_prm_bsns_ptnr');
        $this->db->where('flag_delete ', 0);
        $this->db->where('id_business_partner', $id);

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->row_array();

            return $return;
        }

        return FALSE;
    }

    function submit($business_partner)
    {
        if ($this->db->insert('t_prm_bsns_ptnr', $business_partner)) {
            return TRUE;
        }

        return FALSE;
    }

    function update($business_partner)
    {
        $update = array(
            'nama_business_partner' => $business_partner['nama_business_partner'],
            'jenis_business_partner' => $business_partner['jenis_business_partner'],
            'modified_by' => $business_partner['modified_by'],
            'modified_date' => $business_partner['modified_date']
        );

        $this->db->where('flag_delete ', 0);
        $this->db->where('id_business_partner', $business_partner['id_business_partner']);

        if ($this->db->update('t_prm_bsns_ptnr', $update)) {
            return TRUE;
        }

        return FALSE;
    }

    function soft_delete($business_partner)
    {
        $update = array(
            'flag_delete' => 1,
            'deleted_by' => $business_partner['deleted_by'],
            'deleted_date' => $business_partner['deleted_date']
        );

        $this->db->where('id_business_partner', $business_partner['id_business_partner']);

        if ($this->db->update('t_prm_bsns_ptnr', $update))
        {
            return TRUE;
        }

        return FALSE;
    }
}