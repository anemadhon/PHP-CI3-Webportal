<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pks_business_partner extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

		$this->load->model('Pks_business_partner_model', 'pkspartner');
		$this->load->model('Kantor_cabang_model', 'cabang');
		
		$this->helper->check_is_login();
	}

    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Linkage PKS x Business Partner',
            'sub' => 'List'
		);

		$this->load->view('dashboard/parameter/pks_business_partner/pksxbp', $data);
	}

	function get()
    {
		$pks_business_partners = $this->pkspartner->get();

		$datas = array();
		$data_pks_business_partners = array();

		if ($pks_business_partners)
        {
			foreach ($pks_business_partners as $key => $pks_business_partner)
            {
				$lists = explode(',', $pks_business_partner['list']);
                $list_kode_produk = $this->_mapping_lists($pks_business_partner['produk_eksternal']);
                $list_cabang_bank = $this->_mapping_lists($pks_business_partner['bank_cabang']);
                $list_cabang_ask = $this->_mapping_cabang_askrindo($lists);
				
				$data_pks_business_partners['no'] = ((int)$key+1);
				$data_pks_business_partners['id'] = $pks_business_partner['id'];
				$data_pks_business_partners['nama'] = $pks_business_partner['nama'];
				$data_pks_business_partners['pks_askrindo'] = $pks_business_partner['pks_askrindo'];
				$data_pks_business_partners['pks_eksternal'] = $pks_business_partner['pks_eksternal'];
				$data_pks_business_partners['produk_eksternal'] = $list_kode_produk;
				$data_pks_business_partners['bank'] = $pks_business_partner['bank'];
				$data_pks_business_partners['bank_cabang'] = $list_cabang_bank;
				$data_pks_business_partners['list'] = $list_cabang_ask;
				$datas[] = $data_pks_business_partners;
			}
		}

		$json_data = array('data' => $datas);
		exit(json_encode($json_data));
	}

    public function form($id = '')
    {
		$this->helper->check_eligible_user_menus();

        $pks_business_partner = $id ? $this->pkspartner->get_by_id($id) : '';

		$data = array(
			'title' => 'Linkage PKS x Business Partner',
            'sub' => 'Form',
            'sub_title' => ($id ? 'Ubah' : 'Tambah'),
            'pks_bp' => $pks_business_partner
		);

		$this->load->view('dashboard/parameter/pks_business_partner/form', $data);
	}

    function submit()
    {
		$this->form_validation->set_rules(
			'idbusiness',
			'Nama Business Partner',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Nama Business Partner.'
			)
		);
		$this->form_validation->set_rules(
			'askrindo',
			'No. PKS Askrindo',
			'trim|required',
			array(
				'required' => 'Silahkan Isi No. PKS Askrindo.'
			)
		);
		$this->form_validation->set_rules(
			'eksternal',
			'No. PKS Eksternal',
			'trim|required',
			array(
				'required' => 'Silahkan Isi No. PKS Askrindo.'
			)
		);
		$this->form_validation->set_rules(
			'produk',
			'Kode Produk Eksternal',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kode Produk Eksternal.'
			)
		);
		$this->form_validation->set_rules(
			'bank',
			'Kode Bank',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kode Bank.'
			)
		);
		$this->form_validation->set_rules(
			'cabangbank',
			'Kode Cabang Bank',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kode Cabang Bank.'
			)
		);
		$this->form_validation->set_rules(
			'idcabangask[]',
			'Kode Cabang Askrindo',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Kode Cabang Askrindo.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = '';
            } 

            if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank'))
            {
                $error = form_error('idcabangask');
            } 
            
            if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('idcabangask'))
            {
                $error = form_error('cabangbank');
            } 
            
            if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('bank');
            } 
            
            if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('produk');
            } 
            
            if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('eksternal');
            } 
            
            if (!form_error('idbusiness') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('askrindo');
            } 
           
            if (!form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('idbusiness');
            } 
            
            $message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[PKS Business Partner Controller][submit] - User '.$this->session->userdata('username').' gagal menambah data pks business partner baru karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
        
		$list = implode(',', $this->input->post('idcabangask', TRUE));

        $pks_business_partner['linkage_id_business_partner'] = htmlspecialchars($this->input->post('idbusiness', TRUE));
		$pks_business_partner['no_pks_askrindo'] = htmlspecialchars($this->input->post('askrindo', TRUE));
		$pks_business_partner['no_pks_eksternal'] = htmlspecialchars($this->input->post('eksternal', TRUE));
		$pks_business_partner['kode_produk_eksternal'] = htmlspecialchars($this->input->post('produk', TRUE));
		$pks_business_partner['kode_bank'] = htmlspecialchars($this->input->post('bank', TRUE));
		$pks_business_partner['kode_cabang_bank'] = htmlspecialchars($this->input->post('cabangbank', TRUE));
		$pks_business_partner['list_cabang'] = htmlspecialchars($list);
        $pks_business_partner['kode_broker_agent'] = htmlspecialchars($this->input->post('brokeragent', TRUE));
		$pks_business_partner['created_by'] = $this->session->userdata['username'];
		$pks_business_partner['created_date'] = date('Y-m-d H:i:s');
        $pks_business_partner['created_from'] = $this->helper->get_ip_address();

		if ($this->pkspartner->submit($pks_business_partner))
        {
            $log['type'] = 'info';
            $log['message'] = '[PKS Business Partner Controller][submit] - User '.$this->session->userdata('username').' berhasil menambah data pks business partner baru pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $pks_business_partner);
            $this->helper->logging($log);

            return $this->session->set_flashdata('success', 'Berhasil Menambah Data Baru');
        }
        else
        {
            return $this->session->set_flashdata('failed', 'Gagal Menambah Data Baru');
        }
	}
    
    function update()
    {
        $pks_business_partner['id_pks_business_partner'] = htmlspecialchars($this->input->post('id', TRUE));

        $data_pks_business_partner = $this->pkspartner->get_by_id($pks_business_partner['id_pks_business_partner']);
        
        if ($data_pks_business_partner === FALSE)
        {
            $message = array(
				'success' => FALSE,
				'message' => 'Data Tidak Ditemukan.'
			); 

            $log['type'] = 'error';
            $log['message'] = '[PKS Business Partner Controller][update] - User '.$this->session->userdata('username').' gagal menambah data pks business partner dengan id: '.$pks_business_partner['id_pks_business_partner'].' karena data tsb tidak ditemukan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
        }

        $this->form_validation->set_rules(
			'id',
			'Id PKS Business Partner',
			'trim|required',
			array(
				'required' => 'Id PKS Business Partner Jangan Diganti.'
			)
		);
		$this->form_validation->set_rules(
			'idbusiness',
			'Nama Business Partner',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Nama Business Partner.'
			)
		);
		$this->form_validation->set_rules(
			'askrindo',
			'No. PKS Askrindo',
			'trim|required',
			array(
				'required' => 'Silahkan Isi No. PKS Askrindo.'
			)
		);
		$this->form_validation->set_rules(
			'eksternal',
			'No. PKS Eksternal',
			'trim|required',
			array(
				'required' => 'Silahkan Isi No. PKS Askrindo.'
			)
		);
		$this->form_validation->set_rules(
			'produk',
			'Kode Produk Eksternal',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kode Produk Eksternal.'
			)
		);
		$this->form_validation->set_rules(
			'bank',
			'Kode Bank',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kode Bank.'
			)
		);
		$this->form_validation->set_rules(
			'cabangbank',
			'Kode Cabang Bank',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Kode Cabang Bank.'
			)
		);
		$this->form_validation->set_rules(
			'idcabangask[]',
			'Kode Cabang Askrindo',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Kode Cabang Askrindo.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			if (!form_error('id') && !form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = '';
            } 

            if (!form_error('id') && !form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank'))
            {
                $error = form_error('idcabangask');
            } 
            
            if (!form_error('id') && !form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('idcabangask'))
            {
                $error = form_error('cabangbank');
            } 
            
            if (!form_error('id') && !form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('bank');
            } 
            
            if (!form_error('id') && !form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('produk');
            } 
            
            if (!form_error('id') && !form_error('idbusiness') && !form_error('askrindo') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('eksternal');
            } 
            
            if (!form_error('id') && !form_error('idbusiness') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('askrindo');
            } 
           
            if (!form_error('id') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('idbusiness');
            } 
            
            if (!form_error('idbusiness') && !form_error('askrindo') && !form_error('eksternal') && !form_error('produk') && !form_error('bank') && !form_error('cabangbank') && !form_error('idcabangask'))
            {
                $error = form_error('id');
            } 
            
            $message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[PKS Business Partner Controller][update] - User '.$this->session->userdata('username').' gagal menambah data pks business partner dengan id: '.$pks_business_partner['id_pks_business_partner'].' karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}
        
        $list = implode(',', $this->input->post('idcabangask', TRUE));

        $pks_business_partner['linkage_id_business_partner'] = htmlspecialchars($this->input->post('idbusiness', TRUE));
		$pks_business_partner['no_pks_askrindo'] = htmlspecialchars($this->input->post('askrindo', TRUE));
		$pks_business_partner['no_pks_eksternal'] = htmlspecialchars($this->input->post('eksternal', TRUE));
		$pks_business_partner['kode_produk_eksternal'] = htmlspecialchars($this->input->post('produk', TRUE));
		$pks_business_partner['kode_bank'] = htmlspecialchars($this->input->post('bank', TRUE));
		$pks_business_partner['kode_cabang_bank'] = htmlspecialchars($this->input->post('cabangbank', TRUE));
		$pks_business_partner['list_cabang'] = htmlspecialchars($list);
        $pks_business_partner['kode_broker_agent'] = htmlspecialchars($this->input->post('brokeragent', TRUE));
		$pks_business_partner['modified_by'] = $this->session->userdata['username'];
		$pks_business_partner['modified_date'] = date('Y-m-d H:i:s');
		
		if ($this->pkspartner->update($pks_business_partner))
        {
            $log['type'] = 'info';
            $log['message'] = '[PKS Business Partner Controller][update] - User '.$this->session->userdata('username').' berhasil mengubah data pks business partner pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $pks_business_partner);
            $this->helper->logging($log);

            return $this->session->set_flashdata('success', 'Berhasil Mengubah Data');
        }
        else
        {
            return $this->session->set_flashdata('failed', 'Gagal Mengubah Data');
        }
	}

    function soft_delete()
    {
        $pks_business_partner['id_pks_business_partner'] = htmlspecialchars($this->input->post('id', TRUE));

        $data_pks_business_partner = $this->pkspartner->get_by_id($pks_business_partner['id_pks_business_partner']);
        
        if ($data_pks_business_partner === FALSE)
        {
            $message = array(
				'success' => FALSE,
				'message' => 'Data Tidak Ditemukan.'
			); 

            $log['type'] = 'error';
            $log['message'] = '[PKS Business Partner Controller][soft_delete] - User '.$this->session->userdata('username').' gagal menghapus data pks business partner dengan id: '.$pks_business_partner['id_pks_business_partner'].' karena data tsb tidak ditemukan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
        }

        $pks_business_partner['deleted_by'] = $this->session->userdata['username'];
		$pks_business_partner['deleted_date'] = date('Y-m-d H:i:s');
		
		$this->pkspartner->soft_delete($pks_business_partner);

        $log['type'] = 'info';
        $log['message'] = '[PKS Business Partner Controller][soft_delete] - User '.$this->session->userdata('username').' berhasil menghapus data pks business partner pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $pks_business_partner);
        $this->helper->logging($log);
    }

    function get_by_list()
    {
        $list = htmlspecialchars($this->input->post('list', TRUE));
        $pks_business_partners = $this->pkspartner->get_by_list($list);

		$json_data = array('data' => $pks_business_partners);
		exit(json_encode($json_data));
    }

    private function _mapping_lists($lists)
    {
        $list_cabangs = explode(',', $lists);

        return $list_cabangs;
    }
    
    private function _mapping_cabang_askrindo($lists)
    {
        foreach ($lists as $list)
        {
            $cabang = $this->cabang->get_by_kode($list);
            $list_cabangs[] = "$cabang->kode_cabang-$cabang->nama_cabang";
        }

        return $list_cabangs;
    }
}
