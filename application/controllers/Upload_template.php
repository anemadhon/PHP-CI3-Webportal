<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require('./application/third_party/PHPExcel/PHPExcel.php');

class Upload_template extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
		
        $this->load->model('Upload_template_model', 'up');

        $this->helper->check_is_login();
	}
    
	public function index()
    {
		$this->helper->check_eligible_user_menus();
        $flag_ui = 2;
        $data_on_process_in_background = $this->up->get_data_ready_to_process($flag_ui, $this->helper->get_ip_address(), session_id());

	$background = $this->_check_data_in_background($data_on_process_in_background);

		$data = array(
			'title' => 'Akseptasi',
            'sub' => 'Unggah Template',
            'background' => $background
		);

		$this->load->view('dashboard/acceptance/form/upload', $data);
	}
    
    function to_temporary_storage()
    {
        $config['upload_path'] = './files';
        $config['allowed_types'] = 'xls|xlsx';

        if ($this->up->check_if_file_exist($_FILES['file']['name']))
        {
            exit('00|berkas '.$_FILES['file']['name'].' sudah pernah diunggah, silahkan coba lagi');
        }

        $this->load->library('upload', $config);

        if ($this->upload->do_upload('file'))
        {
            $data_upload = $this->upload->data();

            $log['type'] = 'info';
            $log['message'] = '[Upload Template Controller][to_temporary_storage] - User '.$this->session->userdata('username').' berhasil menyimpan template produksi akseptasi ('.$data_upload['file_name'].') ke .files/ pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            $this->helper->logging($log);

            exit($data_upload['file_name']);
		}
        else
        {
            exit($this->upload->display_errors());
        }
	}
    
    function excel_to_db()
    {
        $file = htmlspecialchars($this->input->post('file', TRUE));
        $excel_reader = new PHPExcel_Reader_Excel2007();
        $load_excel = $excel_reader->load('files/'.$file);
        $datas_excel = $load_excel->getActiveSheet(0)->toArray(null, true, true ,true);

        $id_file = $this->_insert_into_table_file_upload($file);
        if ($id_file === FALSE)
        {
	    $flag_gagal_t_file = '0';
            exit($flag_gagal_t_file);
        }

        $akseptasi_produksi = $this->_insert_into_table_temp_akseptasi_produksi($id_file, $datas_excel);
        if ($akseptasi_produksi === FALSE)
        {
            $flag_gagal_t_temp = '00';
            exit($flag_gagal_t_temp);
        }

        $this->_check_status_upload($id_file);

        $return_data = $this->_get_info_file_produksi($id_file);
        echo $return_data['nama_file'].'|'.$return_data['no_batch'];

	unlink(FCPATH.'files/'.$return_data['nama_file']);

	if($this->up->check_if_background_running() === FALSE)
	{	
		$this->_run_in_background();
        	exit();
	}
    }

    private function _check_data_in_background($data_in_background)
    {
	if($data_in_background === FALSE)
	{
		return 0;
	}
	
	$status_terbit = 0;
	foreach($data_in_background as $background)
	{
		if($background['flag_status_terbit'] === 3 || $background['flag_status_terbit'] === 7 || $background['flag_status_terbit'] === 8)
		{
			$status_terbit = 1;
		}
		else
		{
			$status_terbit += $background['flag_status_validasi_web'];
		}
	}

	return $status_terbit;
    }

    private function _insert_into_table_file_upload($file_name)
    {
	$flag_data = 1;
	$queue = $this->up->check_if_background_running($flag_data);

        $file['nama_file'] = $file_name;
        
        $file['no_batch'] = 'FU';
        $file['status'] = 1;
	$file['queue'] = $queue === FALSE ? 1 : (count($queue) + 1);
        $file['upload_by'] = $this->session->userdata('username');
        $file['upload_date'] = date('Y-m-d H:i:s');
        $file['upload_from'] = $this->helper->get_ip_address();

        $id_file = $this->up->submit_file_upload($file);
        
        if ($id_file === FALSE)
        {
            $log['type'] = 'error';
            $log['message'] = '[Upload Template Controller][excel_to_db] - User '.$this->session->userdata('username').' gagal insert data template produksi akseptasi ke table t_file_upload pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $file);
            $this->helper->logging($log);

            return FALSE;
        }

        $log['type'] = 'info';
        $log['message'] = '[Upload Template Controller][excel_to_db] - User '.$this->session->userdata('username').' berhasil insert data template produksi akseptasi ke table t_file_upload pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $file);
        $this->helper->logging($log);

        return $id_file;
    }

    private function _insert_into_table_temp_akseptasi_produksi($id, $datas_excel)
    {
        $total_rows = count($datas_excel);
        $index_row = 9;
        
        for ($row_index = $index_row; $row_index <= $total_rows; $row_index++)
        { 
            $akseptasi['id_file'] = $id;
            $status = 1;

            $no_rekening = $this->_check_double_no_rekening(strval($datas_excel[$row_index]['F']));
            $empty_column = $this->_check_empty_column($datas_excel[$row_index]);

            if (($no_rekening['no_rekening'] && $no_rekening['in_handle'] === 0) || ($empty_column['status'] && $empty_column['empty_column'] !== ''))
            {
                $status = 0;
            }

            $akseptasi['kode_bank'] = trim($datas_excel[$row_index]['A']);
            $akseptasi['kode_cabang_bank'] = trim($datas_excel[$row_index]['B']);
            $akseptasi['kode_cabang_askrindo'] = trim($datas_excel[$row_index]['C']);
            $akseptasi['kode_produksi'] = trim($datas_excel[$row_index]['D']);
            $akseptasi['kode_broker_agent'] = trim($datas_excel[$row_index]['E']);
            $akseptasi['no_rek_pinjaman'] = trim($datas_excel[$row_index]['F']);
            $akseptasi['no_perjanjian_kredit'] = trim($datas_excel[$row_index]['G']);
            $akseptasi['tgl_awal_pk'] = trim($datas_excel[$row_index]['H']);
            $akseptasi['tgl_akhir_pk'] = trim($datas_excel[$row_index]['I']);
            $akseptasi['jk_waktu_kredit'] = trim($datas_excel[$row_index]['J']);
            $akseptasi['id_valuta'] = trim($datas_excel[$row_index]['K']);
            $akseptasi['kurs_valuta'] = trim($datas_excel[$row_index]['L']);
            $akseptasi['plafond_kredit'] = trim($datas_excel[$row_index]['M']);
            $akseptasi['suku_bunga_kredit'] = trim($datas_excel[$row_index]['N']);
            $akseptasi['jenis_kredit'] = trim($datas_excel[$row_index]['O']);
            $akseptasi['sub_jenis_kredit'] = trim($datas_excel[$row_index]['P']);
            $akseptasi['type_tujuan_kredit'] = trim($datas_excel[$row_index]['Q']);
            $akseptasi['kolektibilitas_kredit'] = trim($datas_excel[$row_index]['R']);
            $akseptasi['sektor_ekonomi'] = trim($datas_excel[$row_index]['S']);
            $akseptasi['sumber_pelunasan_kredit'] = trim($datas_excel[$row_index]['T']);
            $akseptasi['sumber_dana_kredit'] = trim($datas_excel[$row_index]['U']);
            $akseptasi['mekanisme_penyaluran'] = $datas_excel[$row_index]['V'] ? trim($datas_excel[$row_index]['V']) : 1;
            $akseptasi['cif_customer'] = trim($datas_excel[$row_index]['W']);
            $akseptasi['nama_debitur'] = trim($datas_excel[$row_index]['X']);
            $akseptasi['no_ktp_debitur'] = trim($datas_excel[$row_index]['Y']);
            $akseptasi['tmpt_lahir'] = trim($datas_excel[$row_index]['Z']);
            $akseptasi['tgl_lahir'] = trim($datas_excel[$row_index]['AA']);
            $akseptasi['jenis_kelamin'] = trim($datas_excel[$row_index]['AB']);
            $akseptasi['alamat_debitur'] = trim($datas_excel[$row_index]['AC']);
            $akseptasi['kode_pos'] = trim($datas_excel[$row_index]['AD']);
            $akseptasi['jenis_pekerjaan'] = trim($datas_excel[$row_index]['AE']);
            $akseptasi['status_pegawai'] = trim($datas_excel[$row_index]['AF']);
            $akseptasi['no_tlp'] = trim($datas_excel[$row_index]['AG']);
            $akseptasi['no_hp'] = trim($datas_excel[$row_index]['AH']);
            $akseptasi['npwp'] = trim($datas_excel[$row_index]['AI']);
            $akseptasi['jenis_agunan'] = trim($datas_excel[$row_index]['AJ']);
            $akseptasi['jenis_pengikatan'] = trim($datas_excel[$row_index]['AK']);
            $akseptasi['nilai_agunan'] = trim($datas_excel[$row_index]['AL']);
            $akseptasi['tgl_kirim'] = trim($datas_excel[$row_index]['AM']);
            $akseptasi['lain_1'] = trim($datas_excel[$row_index]['AN']);
            $akseptasi['lain_2'] = trim($datas_excel[$row_index]['AO']);
            $akseptasi['broker_agent'] = $datas_excel[$row_index]['AQ'] ? 3 : trim($datas_excel[$row_index]['AP']);
            $akseptasi['kode_broker_agent'] = trim($datas_excel[$row_index]['AQ']);
            $akseptasi['nama_broker_agent'] = trim($datas_excel[$row_index]['AR']);
            $akseptasi['nilai_tanggungan'] = trim($datas_excel[$row_index]['AS']);
            $akseptasi['rate_premi'] = trim($datas_excel[$row_index]['AT']);
            $akseptasi['nilai_premi'] = trim($datas_excel[$row_index]['AU']);
            $akseptasi['tgl_awal_tanggungan'] = trim($datas_excel[$row_index]['AV']);
            $akseptasi['tgl_akhir_tanggungan'] = trim($datas_excel[$row_index]['AW']);
            $akseptasi['jk_waktu_tanggungan'] = trim($datas_excel[$row_index]['AX']);
	    $akseptasi['no_surat_pengantar'] = trim($datas_excel[$row_index]['AY']);
	    $akseptasi['tgl_no_surat_pengantar'] = trim($datas_excel[$row_index]['AZ']);
            $akseptasi['flag_status_validasi_web'] = $status;
            $akseptasi['no_polis_acs'] = '';
            $akseptasi['flag_status_terbit'] = 0;
            $akseptasi['flag_status_terbit_inquiry'] = 0;
            $akseptasi['counter_inquiry_polis'] = 0;
            $akseptasi['keterangan'] = '';
            $akseptasi['upload_from'] = $this->helper->get_ip_address();
	    $akseptasi['session_id'] = session_id();
            
            $insert = $this->up->submit_akseptasi_produksi($akseptasi);
            
            if ($insert === FALSE)
            {
                $log['type'] = 'error';
                $log['message'] = '[Upload Template Controller][excel_to_db] - User '.$this->session->userdata('username').' gagal insert data template produksi akseptasi ke table t_temp_akseptasi_produksi pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address().' data: '.implode('|', $akseptasi);
                $this->helper->logging($log);

                return FALSE;
            }

	    if($no_rekening['no_rekening'])
	    {
		$this->up->update_keterangan($insert, 'Tidak Lolos Validasi Web (No. Rekening sudah pernah dijaminkan)');

            	$log['type'] = 'error';
            	$log['message'] = '[Upload Template Controller][excel_to_db] - Tidak lolos validasi web karena no rekening '.$no_rekening['no_rekening'].' sudah pernah dijaminkan pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            	$this->helper->logging($log);
	    }
	
	    if($no_rekening['in_handle'] === 1)
	    {
		$this->up->update_flag_status_terbit($insert, 8);
		
	    	$this->up->update_keterangan($insert, 'Tidak Lolos Validasi Web (No. Rekening sedang diproses ulang - kondisi before timeout)');

            	$log['type'] = 'error';
            	$log['message'] = '[Upload Template Controller][excel_to_db] - Tidak lolos validasi web karena, sistem melakukan update flag_status_terbit = 8 untuk id: '.$insert.' dan no rekening '.$no_rekening['no_rekening'].' karena masih dalam proses handling di scheduler pada '.date('Y-m-d H:i:s');
            	$this->helper->logging($log);
	    }

	    if($empty_column['status'] && $empty_column['empty_column'] !== '')
	    {
		$this->up->update_keterangan($insert, 'Tidak Lolos Validasi Web (Kolom '.$empty_column['empty_column'].' di template kosong)');

            	$log['type'] = 'error';
            	$log['message'] = '[Upload Template Controller][excel_to_db] - Tidak lolos validasi web karena data mandatory bernilai kosong untuk id:'.$insert.' kolom: '.$empty_column['empty_column'].' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
            	$this->helper->logging($log);
	    }
        }
        
        $log['type'] = 'info';
        $log['message'] = '[Upload Template Controller][excel_to_db] - User '.$this->session->userdata('username').' berhasil insert semua data template produksi akseptasi ke table t_temp_akseptasi_produksi untuk id_file: '.$id.' pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
        $this->helper->logging($log);

        if ($this->_set_batch_number($id, $akseptasi['kode_cabang_askrindo']) === FALSE)
        {
            return FALSE;
        }
        
        return TRUE;
    }

    private function _set_batch_number($id, $kode_cabang_ask)
    {
        $ip = explode('.', $this->helper->get_ip_address());
        $time = str_split(date('dmyHis'), 2);
        $counter_id = str_pad($id, 8, "0", STR_PAD_LEFT);
        $batch = "FU$kode_cabang_ask$ip[1]$ip[3]$time[4]$time[5]$counter_id";
        
        if ($this->up->update_batch_number($id, $batch))
        {
            return TRUE;
        }

        return FALSE;
    }

    private function _check_status_upload($id)
    {
        $status = $this->up->check_status_upload($id);
        $not_valid_data = array();
        if ($status !== FALSE)
        {
            foreach ($status as $key => $not_valid)
            {
                if ($not_valid['flag_status_validasi_web'] === 0)
                {
                    $not_valid_data[] = $not_valid['flag_status_validasi_web'];
                }
            }

            if (count($status) == count($not_valid_data)) {
                $log['type'] = 'error';
                $log['message'] = '[Upload Template Controller][excel_to_db] - Sistem mengupdate flag_status_validasi_web = 0 karena data file tidak memenuhi validasi web pada '.date('Y-m-d H:i:s').' dari IP Addr: '.$this->helper->get_ip_address();
                $this->helper->logging($log);

                $this->up->update_status_file_upload($id, 0);
            }
        }
    }

    private function _run_in_background()
    {
	$ip = $this->helper->get_ip_address();
	$session_id = session_id();
        $command = "php -f ".FCPATH."index.php auth run_in_background 0 $ip $session_id";
        
        $this->_command($command);
    }

    private function _command($command)
    {
        if (substr(php_uname(), 0, 7) == "Windows")
        {
            pclose(popen("start \"run_in_background\" $command", "r"));
        }
    }

    private function _get_info_file_produksi($id)
    {
        $file_info = $this->up->get_info_file_produksi_by_id($id);

        return $file_info;
    }

    private function _check_double_no_rekening($no_rekening)
    {
	$in_handle = 0;
	$no_rek = FALSE;

        $rekening = $this->up->get_no_rekening($no_rekening);

        if ($rekening['no_rek_pinjaman'])
	{

            $in_handle = $this->_check_no_rekening_is_timeout($rekening);
	    $no_rek = $rekening['no_rek_pinjaman'];
        }

        $return = array(
	    'in_handle' => $in_handle,
	    'no_rekening' => $no_rek
	);

        return $return;
    }

    private function _check_no_rekening_is_timeout($data)
    {
        if ($data['flag_status_terbit'] === 7)
        {
	    return 1;
        }

	return 0;
    }

    private function _check_empty_column($column)
    {
	$status = FALSE;
	$empty_column = '';

        if (trim($column['A']) == '' || 
	    trim($column['A']) == '<isi sesuai kode bank diatas>' ||
            trim($column['B']) == '' ||
	    trim($column['B']) == '<isi sesuai kode cabang bank diatas>' ||
            trim($column['C']) == '' ||
	    trim($column['C']) == '<isi sesuai kode cabang askrindo diatas>' ||
            trim($column['D']) == '' ||
	    trim($column['B']) == '<isi sesuai kode produk diatas>' ||
            trim($column['F']) == '' ||
            trim($column['G']) == '' ||
            trim($column['H']) == '' ||
            trim($column['I']) == '' ||
            trim($column['J']) === '' ||
            trim($column['K']) === '' ||
            trim($column['M']) == '' ||
            trim($column['N']) === '' ||
            trim($column['Q']) === '' ||
            trim($column['R']) === '' ||
            trim($column['X']) == '' ||
            trim($column['Y']) == '' ||
            trim($column['AC']) == '' ||
            trim($column['AD']) == '' ||
            trim($column['AE']) === '' ||
            trim($column['AF']) === '' ||
            trim($column['AI']) == '')
        {
            $empty_column = ($column['A'] == '' ? 'A|' : '').($column['B'] == '' ? 'B|' : '').($column['C'] == '' ? 'C|' : '').($column['D'] == '' ? 'D|' : '').($column['F'] == '' ? 'F|' : '').($column['G'] == '' ? 'G|' : '').($column['H'] == '' ? 'H|' : '').($column['I'] == '' ? 'I|' : '').($column['J'] === '' ? 'J|' : '').($column['K'] === '' ? 'K|' : '').($column['M'] == '' ? 'M|' : '').($column['N'] === '' ? 'N|' : '').($column['Q'] === '' ? 'Q|' : '').($column['R'] === '' ? 'R|' : '').($column['X'] == '' ? 'X|' : '').($column['Y'] == '' ? 'Y|' : '').($column['AC'] == '' ? 'AC|' : '').($column['AD'] == '' ? 'AD|' : '').($column['AE'] === '' ? 'AE|' : '').($column['AF'] === '' ? 'AF|' : '').($column['AI'] == '' ? 'AI' : '');

            $status = TRUE;
        }

	$return = array(
		'status' => $status,
		'empty_column' => $empty_column
	);

	return $return;
    }
}