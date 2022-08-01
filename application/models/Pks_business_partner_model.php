<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pks_business_partner_model extends CI_Model {
    function __construct()
    {
        parent::__construct(); 
    }

    function get()
    {
        $this->db->select('a.id_pks_business_partner as id, a.no_pks_askrindo as pks_askrindo, a.no_pks_eksternal as pks_eksternal, a.kode_produk_eksternal as produk_eksternal, a.kode_bank as bank, a.kode_cabang_bank as bank_cabang, a.list_cabang as list, b.nama_business_partner as nama');
        $this->db->from('t_prm_pks_bsns_ptnr a');
        $this->db->join('t_prm_bsns_ptnr b', 'a.linkage_id_business_partner = b.id_business_partner');
        $this->db->where('a.flag_delete ', 0);
        $this->db->where('b.flag_delete ', 0);

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
        $this->db->select('a.id_pks_business_partner as id, a.no_pks_askrindo as pks_askrindo, a.no_pks_eksternal as pks_eksternal, a.kode_produk_eksternal as produk_eksternal, a.kode_bank as bank, a.kode_cabang_bank as bank_cabang, a.kode_broker_agent as broker_agent, a.list_cabang as list, b.id_business_partner as idbusiness, b.nama_business_partner as nama, b.jenis_business_partner as tipe');
        $this->db->from('t_prm_pks_bsns_ptnr a');
        $this->db->join('t_prm_bsns_ptnr b', 'a.linkage_id_business_partner = b.id_business_partner');
        $this->db->where('a.flag_delete ', 0);
        $this->db->where('b.flag_delete ', 0);
        $this->db->where('a.id_pks_business_partner', $id);

        $query = $this->db->get();

        if (count($query->result_array()) > 0)
        {
            $return = $query->row_array();

            return $return;
        }

        return FALSE;
    }

    function submit($pks_business_partner)
    {
        if ($this->db->insert('t_prm_pks_bsns_ptnr', $pks_business_partner))
        {
            return TRUE;
        }

        return FALSE;
    }

    function update($pks_business_partner)
    {
        $update = array(
            'linkage_id_business_partner' => $pks_business_partner['linkage_id_business_partner'],
            'no_pks_askrindo' => $pks_business_partner['no_pks_askrindo'],
            'no_pks_eksternal' => $pks_business_partner['no_pks_eksternal'],
            'kode_bank' => $pks_business_partner['kode_bank'],
            'kode_cabang_bank' => $pks_business_partner['kode_cabang_bank'],
            'kode_produk_eksternal' => $pks_business_partner['kode_produk_eksternal'],
            'kode_broker_agent' => $pks_business_partner['kode_broker_agent'],
            'list_cabang' => $pks_business_partner['list_cabang'],
            'modified_by' => $pks_business_partner['modified_by'],
            'modified_date' => $pks_business_partner['modified_date']
        );

        $this->db->where('flag_delete ', 0);
        $this->db->where('id_pks_business_partner', $pks_business_partner['id_pks_business_partner']);

        if ($this->db->update('t_prm_pks_bsns_ptnr', $update))
        {
            return TRUE;
        }

        return FALSE;
    }

    function soft_delete($pks_business_partner)
    {
        $update = array(
            'flag_delete' => 1,
            'deleted_by' => $pks_business_partner['deleted_by'],
            'deleted_date' => $pks_business_partner['deleted_date']
        );

        $this->db->where('id_pks_business_partner', $pks_business_partner['id_pks_business_partner']);

        if ($this->db->update('t_prm_pks_bsns_ptnr', $update))
        {
            return TRUE;
        }

        return FALSE;
    }

    function get_by_list($list)
    {
        $this->db->select('a.id_pks_business_partner as id, a.no_pks_askrindo as pks_askrindo, a.no_pks_eksternal as pks_eksternal, a.list_cabang as list, b.nama_business_partner as nama');
        $this->db->from('t_prm_pks_bsns_ptnr a');
        $this->db->join('t_prm_bsns_ptnr b', 'a.linkage_id_business_partner = b.id_business_partner');
        $this->db->where('a.flag_delete ', 0);
        $this->db->where('b.flag_delete ', 0);
        if ($list !== '') {
            $this->db->like('a.list_cabang', $list);
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