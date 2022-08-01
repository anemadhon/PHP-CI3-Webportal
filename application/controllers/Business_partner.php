<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Business_partner extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

		$this->load->model('Business_partner_model', 'partner');
		
		$this->helper->check_is_login();
	}

    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Business Partner',
            'sub' => 'List'
		);

		$this->load->view('dashboard/parameter/business_partner/bp', $data);
	}

	function get()
    {
        $business_partners = $this->partner->get();
        
		$jenis_business_partner = $this->_get_business_partner_type();

		$datas = array();
		$data_business_partner = array();

		if ($business_partners)
        {
			foreach ($business_partners as $key => $business_partner)
            {
				$data_business_partner['no'] = ((int)$key+1);
				$data_business_partner['id'] = $business_partner['id'];
				$data_business_partner['nama'] = $business_partner['nama'];
				$data_business_partner['jenis'] = $jenis_business_partner[$business_partner['jenis']];
				$datas[] = $data_business_partner;
			}
		}

		$json_data = array('data' => $datas);
		exit(json_encode($json_data));
	}

    public function form($id = '')
    {
		$this->helper->check_eligible_user_menus();

		$business_partner = $id ? $this->partner->get_by_id($id) : '';

        $jenis = $this->_get_business_partner_type();

		$data = array(
			'title' => 'Business Partner',
            'sub' => 'Form',
            'sub_title' => ($id ? 'Ubah' : 'Tambah'),
			'bp' => $business_partner,
            'jenis_bp' => $jenis
		);

		$this->load->view('dashboard/parameter/business_partner/form', $data);
	}

	function submit()
    {
		$this->form_validation->set_rules(
			'nama',
			'Nama Business Partner',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Nama Business Partner.'
			)
		);
		$this->form_validation->set_rules(
			'jenis',
			'Jenis Business Partner',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Jenis Business Partner.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			$error = form_error('nama') ? form_error('nama') : (form_error('jenis') ? form_error('jenis') : '');
			$message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[Business Partner Controller][submit] - User '.$this->session->userdata('username').' gagal menambah data business partner baru karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		$business_partner['nama_business_partner'] = htmlspecialchars($this->input->post('nama', TRUE));
		$business_partner['jenis_business_partner'] = htmlspecialchars($this->input->post('jenis', TRUE));
		$business_partner['created_by'] = $this->session->userdata['username'];
		$business_partner['created_date'] = date('Y-m-d H:i:s');
        $business_partner['created_from'] = $this->helper->get_ip_address();
		
		if ($this->partner->submit($business_partner))
        {
            $log['type'] = 'info';
            $log['message'] = '[Business Partner Controller][submit] - User '.$this->session->userdata('username').' berhasil menambah data business partner pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $business_partner);
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
        $business_partner['id_business_partner'] = htmlspecialchars($this->input->post('id', TRUE));

        $data_business_partner = $this->partner->get_by_id($business_partner['id_business_partner']);
        
        if ($data_business_partner === FALSE)
        {
            $message = array(
				'success' => FALSE,
				'message' => 'Data Tidak Ditemukan.'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Business Partner Controller][update] - User '.$this->session->userdata('username').' gagal mengubah data business partner dengan id: '.$business_partner['id_business_partner'].' karena data tsb tidak ditemukan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
        }
		
        $this->form_validation->set_rules(
			'id',
			'Id Business Partner',
			'trim|required',
			array(
				'required' => 'Id Business Partner Jangan Diganti.'
			)
		);
		$this->form_validation->set_rules(
			'nama',
			'Nama Business Partner',
			'trim|required',
			array(
				'required' => 'Silahkan Isi Nama Business Partner.'
			)
		);
		$this->form_validation->set_rules(
			'jenis',
			'Jenis Business Partner',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Jenis Business Partner.'
			)
		);

		if ($this->form_validation->run() === FALSE)
        {
			if (!form_error('id') && !form_error('nama') && !form_error('jenis'))
            {
				$error = '';
			} 

			if (!form_error('id') && !form_error('nama'))
            {
				$error = form_error('jenis');
			} 
			
			if (!form_error('id') && !form_error('jenis'))
            {
				$error = form_error('nama');
			} 
			
			if (!form_error('nama') && !form_error('jenis'))
            {
				$error = form_error('id');
			}

			$message = array(
				'success' => FALSE,
				'message' => $error
			); 

            $log['type'] = 'error';
            $log['message'] = '[Business Partner Controller][update] - User '.$this->session->userdata('username').' gagal mengubah data business partner dengan id: '.$business_partner['id_business_partner'].' karena error: '.$error.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
		}

		$business_partner['nama_business_partner'] = htmlspecialchars($this->input->post('nama', TRUE));
		$business_partner['jenis_business_partner'] = htmlspecialchars($this->input->post('jenis', TRUE));
		$business_partner['modified_by'] = $this->session->userdata['username'];
		$business_partner['modified_date'] = date('Y-m-d H:i:s');
		
		if ($this->partner->update($business_partner))
        {
            $log['type'] = 'info';
            $log['message'] = '[Business Partner Controller][update] - User '.$this->session->userdata('username').' berhasil mengubah data business partner dengan id: '.$business_partner['id_business_partner'].' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $business_partner);
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
        $business_partner['id_business_partner'] = htmlspecialchars($this->input->post('id', TRUE));

        $data_business_partner = $this->partner->get_by_id($business_partner['id_business_partner']);
        
        if ($data_business_partner === FALSE)
        {
            $message = array(
				'success' => FALSE,
				'message' => 'Data Tidak Ditemukan.'
			); 

            $log['type'] = 'error';
            $log['message'] = '[Business Partner Controller][soft_delete] - User '.$this->session->userdata('username').' gagal mengubah data business partner dengan id: '.$business_partner['id_business_partner'].' karena data tsb tidak ditemukan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

			exit(json_encode($message));
        }

        $business_partner['deleted_by'] = $this->session->userdata['username'];
		$business_partner['deleted_date'] = date('Y-m-d H:i:s');

        $this->partner->soft_delete($business_partner);

        $log['type'] = 'info';
        $log['message'] = '[Business Partner Controller][soft_delete] - User '.$this->session->userdata('username').' berhasil menghapus data business partner pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $business_partner);
        $this->helper->logging($log);
    }

    private function _get_business_partner_type()
    {
        $jenis = array(
            1 => 'NO AGENT NO BROKER',
            2 => 'AGENT',
            3 => 'BROKER',
            4 => 'BANKS ASSURANCE'
        );

        return $jenis;
    }
}
