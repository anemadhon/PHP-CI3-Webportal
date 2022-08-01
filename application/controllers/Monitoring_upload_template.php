<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require('./application/third_party/PHPExcel/PHPExcel.php');

class Monitoring_upload_template extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('Monitoring_upload_template_model', 'monitoring');
		
        $this->helper->check_is_login();
	}
    
    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Akseptasi',
            'sub' => 'Pemantauan Proses Unggah'
		);
		
		$this->load->view('dashboard/acceptance/monitor/monitor', $data);
	}

    function get()
    {
        $search = $this->input->post('search')['value'];
        $order = $this->input->post('order');
        $draw = intval($this->input->post('draw'));
        $length = intval($this->input->post('length'));
        $start = intval($this->input->post('start'));

        $columns_for_search = array(
            'no_batch' => $search,
            'nama_file' => $search,
            'upload_by' => $search,
        );
        $columns_for_order = array(
            1 => 'no_batch',
            2 => 'nama_file',
            7 => 'upload_by',
            8 => 'a.upload_date'
        );

        $total_data_no_limits = $this->monitoring->get($search, $columns_for_search, $order, $columns_for_order);
        $data_uploads = $this->monitoring->get($search, $columns_for_search, $order, $columns_for_order, $length, $start);

        $datas = array();
		$data_monitorings = array();

        if ($data_uploads)
        {
            foreach ($data_uploads as $key => $upload) {
                $process = $this->monitoring->get_total_data_on_progress($upload['id_file']);
                $successed = $this->monitoring->get_total_data_successed($upload['id_file']);
                $failed = $this->monitoring->get_failed_data($upload['id_file']);
                $keterangan = $upload['status'] === 3 ? 'Dalam Proses' : ($upload['status'] === 1 ? 'Dalam Proses Antrian' : 'Selesai');
                
                $data_monitorings['no'] = ((int)($key + 1) + $start);
                $data_monitorings['id'] = $upload['id_file'];
                $data_monitorings['batch'] = $upload['no_batch'];
                $data_monitorings['file'] = $upload['nama_file'];
                $data_monitorings['uploader'] = $upload['upload_by'];
                $data_monitorings['uploaded_time'] = $upload['upload_date'];
                $data_monitorings['status'] = $keterangan;
                $data_monitorings['rows'] = $upload['jumlah_data'];
                $data_monitorings['process'] = $process;
                $data_monitorings['successed'] = $successed;
                $data_monitorings['failed'] = $failed ? count($failed) : 0;
                $datas[] = $data_monitorings;
            }
        }

        $json_data = array(
            'draw' => $draw,
            'recordsTotal' => $total_data_no_limits ? count($total_data_no_limits) : 0,
            'recordsFiltered' => $total_data_no_limits ? count($total_data_no_limits) : 0,
            'data' => $datas
        );
		exit(json_encode($json_data));
    }

    function get_total_data()
    {
        $total = $this->monitoring->get_total_data();
        exit(json_encode($total));
    }
    
    function get_total_data_on_progress()
    {
        $total = $this->monitoring->get_total_data_on_progress();
        exit(json_encode($total));
    }
    
    function get_total_data_successed()
    {
        $total = $this->monitoring->get_total_data_successed();
        exit(json_encode($total));
    }
    
    function get_total_data_failed()
    {
        $total = $this->monitoring->get_failed_data();
        $total_data = $total ? count($total) : 0;
        exit(json_encode($total_data));
    }

	function get_failed_data()
    {
        $rejects = $this->monitoring->get_failed_data();

        $datas = array();
		$data_rejects = array();

        if ($rejects)
        {
            foreach ($rejects as $key => $reject) {
                $keterangan = $reject['flag_status_validasi_web'] === 0 ? 'Tidak Lolos Validasi Web' : $reject['keterangan'];
                
                $data_rejects['no'] = ((int)$key+1);
                $data_rejects['id'] = $reject['id_file'];
                $data_rejects['no_rekening'] = $reject['no_rek_pinjaman'];
                $data_rejects['no_perjanian'] = $reject['no_perjanjian_kredit'];
                $data_rejects['alasan_tolak'] = $reject['keterangan'] == '' ? $keterangan : $reject['keterangan'];
                $data_rejects['tgl_awal_pk'] = $reject['tgl_awal_pk'];
                $data_rejects['tgl_akhir_pk'] = $reject['tgl_akhir_pk'];
                $data_rejects['jk_waktu_kredit'] = $reject['jk_waktu_kredit'];
                $data_rejects['id_valuta'] = $reject['id_valuta'];
                $data_rejects['kurs_valuta'] = $reject['kurs_valuta'];
                $data_rejects['plafond_kredit'] = $reject['plafond_kredit'];
                $data_rejects['suku_bunga_kredit'] = $reject['suku_bunga_kredit'];
                $data_rejects['jenis_kredit'] = $reject['jenis_kredit'];
                $data_rejects['sub_jenis_kredit'] = $reject['sub_jenis_kredit'];
                $data_rejects['type_tujuan_kredit'] = $reject['type_tujuan_kredit'];
                $data_rejects['kolektibilitas_kredit'] = $reject['kolektibilitas_kredit'];
                $data_rejects['sektor_ekonomi'] = $reject['sektor_ekonomi'];
                $data_rejects['sumber_pelunasan_kredit'] = $reject['sumber_pelunasan_kredit'];
                $data_rejects['sumber_dana_kredit'] = $reject['sumber_dana_kredit'];
                $data_rejects['mekanisme_penyaluran'] = $reject['mekanisme_penyaluran'];
                $data_rejects['cif_customer'] = $reject['cif_customer'];
                $data_rejects['nama_debitur'] = $reject['nama_debitur'];
                $data_rejects['no_ktp_debitur'] = $reject['no_ktp_debitur'];
                $data_rejects['ttl'] = $reject['tmpt_lahir'].', '.$reject['tgl_lahir'];
                $data_rejects['jk'] = $reject['jenis_kelamin'];
                $data_rejects['alamat'] = $reject['alamat_debitur'].' - '.$reject['kode_pos'];
                $data_rejects['jenis_pekerjaan'] = $reject['jenis_pekerjaan'];
                $data_rejects['status_pegawai'] = $reject['status_pegawai'];
                $data_rejects['tlp'] = $reject['no_tlp'];
                $data_rejects['hp'] = $reject['no_hp'];
                $data_rejects['npwp'] = $reject['npwp'];
                $data_rejects['jenis_agunan'] = $reject['jenis_agunan'];
                $data_rejects['jenis_pengikatan'] = $reject['jenis_pengikatan'];
                $data_rejects['nilai_agunan'] = $reject['nilai_agunan'];
                $data_rejects['tgl_kirim'] = $reject['tgl_kirim'];
                $data_rejects['lain'] = $reject['lain_1'].' - '.$reject['lain_2'];
                $data_rejects['broker_agent'] = $reject['broker_agent'];
                $data_rejects['kode_broker_agent'] = $reject['kode_broker_agent'];
                $data_rejects['nama_broker_agent'] = $reject['nama_broker_agent'];
                $data_rejects['nilai_tanggungan'] = $reject['nilai_tanggungan'];
                $data_rejects['rate_premi'] = $reject['rate_premi'];
                $data_rejects['nilai_premi'] = $reject['nilai_premi'];
                $data_rejects['tgl_awal_tanggungan'] = $reject['tgl_awal_tanggungan'];
                $data_rejects['tgl_akhir_tanggungan'] = $reject['tgl_akhir_tanggungan'];
                $data_rejects['jk_waktu_tanggungan'] = $reject['jk_waktu_tanggungan'];
                $datas[] = $data_rejects;
            }
        }

        $json_data = array('data' => $datas);
		exit(json_encode($json_data));
    }
    
    function download_excel()
    {
        $get_parameter_data = $this->monitoring->get_data_uploaded_all();

        $excel = new PHPExcel();

        //set column width
        $column_length = 56;
        foreach (range(0, $column_length) as $column)
        {
            $excel->getActiveSheet()->getColumnDimensionByColumn($column)->setWidth(30);
        }

        //set row height
        $excel->getActiveSheet()->getRowDimension(4)->setRowHeight(20);
        
        //set style for table
        $styles = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font' => array(
                'size' => 12
            )
        );
        $excel->getActiveSheet()->getStyle('A4:BD4')->applyFromArray($styles);
        
        $excel->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $excel->setActiveSheetIndex(0)->setCellValue('A2', 'Data Monitoring');

        $excel->setActiveSheetIndex(0)->setCellValue('A4', 'No.');
        $excel->setActiveSheetIndex(0)->setCellValue('B4', 'No. Batch');
        $excel->setActiveSheetIndex(0)->setCellValue('C4', 'Nama File');
        $excel->setActiveSheetIndex(0)->setCellValue('D4', 'Tanggal Unggah');
        $excel->setActiveSheetIndex(0)->setCellValue('E4', 'Pengunggah');
        $excel->setActiveSheetIndex(0)->setCellValue('F4', 'Kode Bank');
        $excel->setActiveSheetIndex(0)->setCellValue('G4', 'Kode Cabang Bank');
        $excel->setActiveSheetIndex(0)->setCellValue('H4', 'Kode Cabang Askrindo');
        $excel->setActiveSheetIndex(0)->setCellValue('I4', 'Kode Produk');
        $excel->setActiveSheetIndex(0)->setCellValue('J4', 'No Rekening Pinjaman');
        $excel->setActiveSheetIndex(0)->setCellValue('K4', 'No Perjanjian Kredit (PK)');
        $excel->setActiveSheetIndex(0)->setCellValue('L4', 'Tgl Awal PK');
        $excel->setActiveSheetIndex(0)->setCellValue('M4', 'Tgl Akhir PK');
        $excel->setActiveSheetIndex(0)->setCellValue('N4', 'Jk Waktu Kredit (Dalam Bulan)');
        $excel->setActiveSheetIndex(0)->setCellValue('O4', 'id valuta (Currency)');
        $excel->setActiveSheetIndex(0)->setCellValue('P4', 'Kurs Valuta');
        $excel->setActiveSheetIndex(0)->setCellValue('Q4', 'Plafond Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('R4', 'Suku Bunga Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('S4', 'Jenis Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('T4', 'Sub Jenis Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('U4', 'Type Tujuan KrediT');
        $excel->setActiveSheetIndex(0)->setCellValue('V4', 'Kolektibilitas Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('W4', 'Sektor Ekonomi');
        $excel->setActiveSheetIndex(0)->setCellValue('X4', 'Sumber Pelunasan Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('Y4', 'Sumber Dana Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('Z4', 'Mekanisme Penyaluran');
        $excel->setActiveSheetIndex(0)->setCellValue('AA4', 'CIF Customer');
        $excel->setActiveSheetIndex(0)->setCellValue('AB4', 'Nama Debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('AC4', 'No KTP Debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('AD4', 'Tempat Lahir');
        $excel->setActiveSheetIndex(0)->setCellValue('AE4', 'Tanggal Lahir');
        $excel->setActiveSheetIndex(0)->setCellValue('AF4', 'Jenis Kelamin');
        $excel->setActiveSheetIndex(0)->setCellValue('AG4', 'Alamat Debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('AH4', 'Kode Pos');
        $excel->setActiveSheetIndex(0)->setCellValue('AI4', 'Jenis Pekerjaan');
        $excel->setActiveSheetIndex(0)->setCellValue('AJ4', 'Status Kepegawaian');
        $excel->setActiveSheetIndex(0)->setCellValue('AK4', 'No Telepon');
        $excel->setActiveSheetIndex(0)->setCellValue('AL4', 'No HP debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('AM4', 'NPWP');
        $excel->setActiveSheetIndex(0)->setCellValue('AN4', 'Jenis Agunan');
        $excel->setActiveSheetIndex(0)->setCellValue('AO4', 'Jenis Pengikatan');
        $excel->setActiveSheetIndex(0)->setCellValue('AP4', 'Nilai Agunan');
        $excel->setActiveSheetIndex(0)->setCellValue('AQ4', 'Tgl Kirim');
        $excel->setActiveSheetIndex(0)->setCellValue('AR4', 'Other 1');
        $excel->setActiveSheetIndex(0)->setCellValue('AS4', 'Other 2');
        $excel->setActiveSheetIndex(0)->setCellValue('AT4', 'BROKER_AGENT');
        $excel->setActiveSheetIndex(0)->setCellValue('AU4', 'KODE_BROKER_AGENT');
        $excel->setActiveSheetIndex(0)->setCellValue('AV4', 'NAMA_BROKER_AGENT');
        $excel->setActiveSheetIndex(0)->setCellValue('AW4', 'Nilai Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('AX4', 'Rate Premi');
        $excel->setActiveSheetIndex(0)->setCellValue('AY4', 'Nilai Premi');
        $excel->setActiveSheetIndex(0)->setCellValue('AZ4', 'Tanggal Awal Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('BA4', 'Tanggal Akhir Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('BB4', 'Jk Waktu Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('BC4', 'Status');
        $excel->setActiveSheetIndex(0)->setCellValue('BD4', 'Keterangan');
	$excel->setActiveSheetIndex(0)->setCellValue('BE4', 'No. Polis ACS');

        $styles_body = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            )
        );
        
        $row = 5;
        if ($get_parameter_data)
        {
            foreach ($get_parameter_data as $key => $data) 
            {
                $excel->getActiveSheet()->getStyle("A$row:BD$row")->applyFromArray($styles_body);
    
                switch ($data['flag_status_validasi_web']) {
                    case 0:
                        $keterangan = 'Tidak Lolos Validasi Web / Gagal';
                        break;
    
                    case 1:
                        $keterangan = 'Dalam Proses';
                        break;
    
                    case 2:
                        $keterangan = 'Berhasil';
                        break;
                    
                    default:
                        $keterangan = $data['keterangan'];
                        break;
                }
                
                $excel->setActiveSheetIndex(0)->setCellValue('A'.$row, (int)($key + 1)); 
                $excel->setActiveSheetIndex(0)->setCellValue('B'.$row, $data['no_batch']); 
                $excel->setActiveSheetIndex(0)->setCellValue('C'.$row, $data['nama_file']); 
                $excel->setActiveSheetIndex(0)->setCellValue('D'.$row, $data['upload_date']); 
                $excel->setActiveSheetIndex(0)->setCellValue('E'.$row, $data['upload_by']); 
                $excel->setActiveSheetIndex(0)->setCellValue('F'.$row, $data['kode_bank']); 
                $excel->setActiveSheetIndex(0)->setCellValue('G'.$row, $data['kode_cabang_bank']); 
                $excel->setActiveSheetIndex(0)->setCellValue('H'.$row, $data['kode_cabang_askrindo']); 
                $excel->setActiveSheetIndex(0)->setCellValue('I'.$row, $data['kode_produksi']); 
                $excel->setActiveSheetIndex(0)->setCellValue('J'.$row, $data['no_rek_pinjaman']); 
                $excel->setActiveSheetIndex(0)->setCellValue('K'.$row, $data['no_perjanjian_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('L'.$row, $data['tgl_awal_pk']); 
                $excel->setActiveSheetIndex(0)->setCellValue('M'.$row, $data['tgl_akhir_pk']); 
                $excel->setActiveSheetIndex(0)->setCellValue('N'.$row, $data['jk_waktu_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('O'.$row, $data['id_valuta']); 
                $excel->setActiveSheetIndex(0)->setCellValue('P'.$row, $data['kurs_valuta']); 
                $excel->setActiveSheetIndex(0)->setCellValue('Q'.$row, $data['plafond_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('R'.$row, $data['suku_bunga_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('S'.$row, $data['jenis_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('T'.$row, $data['sub_jenis_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('U'.$row, $data['type_tujuan_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('V'.$row, $data['kolektibilitas_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('W'.$row, $data['sektor_ekonomi']); 
                $excel->setActiveSheetIndex(0)->setCellValue('X'.$row, $data['sumber_pelunasan_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('Y'.$row, $data['sumber_dana_kredit']); 
                $excel->setActiveSheetIndex(0)->setCellValue('Z'.$row, $data['mekanisme_penyaluran']); 
                $excel->setActiveSheetIndex(0)->setCellValue('AA'.$row, $data['cif_customer']);
                $excel->setActiveSheetIndex(0)->setCellValue('AB'.$row, $data['nama_debitur']);
                $excel->setActiveSheetIndex(0)->setCellValue('AC'.$row, $data['no_ktp_debitur']);
                $excel->setActiveSheetIndex(0)->setCellValue('AD'.$row, $data['tmpt_lahir']);
                $excel->setActiveSheetIndex(0)->setCellValue('AE'.$row, $data['tgl_lahir']);
                $excel->setActiveSheetIndex(0)->setCellValue('AF'.$row, $data['jenis_kelamin']);
                $excel->setActiveSheetIndex(0)->setCellValue('AG'.$row, $data['alamat_debitur']);
                $excel->setActiveSheetIndex(0)->setCellValue('AH'.$row, $data['kode_pos']);
                $excel->setActiveSheetIndex(0)->setCellValue('AI'.$row, $data['jenis_pekerjaan']);
                $excel->setActiveSheetIndex(0)->setCellValue('AJ'.$row, $data['status_pegawai']);
                $excel->setActiveSheetIndex(0)->setCellValue('AK'.$row, $data['no_tlp']);
                $excel->setActiveSheetIndex(0)->setCellValue('AL'.$row, $data['no_hp']);
                $excel->setActiveSheetIndex(0)->setCellValue('AM'.$row, $data['npwp']);
                $excel->setActiveSheetIndex(0)->setCellValue('AN'.$row, $data['jenis_agunan']);
                $excel->setActiveSheetIndex(0)->setCellValue('AO'.$row, $data['jenis_pengikatan']);
                $excel->setActiveSheetIndex(0)->setCellValue('AP'.$row, $data['nilai_agunan']);
                $excel->setActiveSheetIndex(0)->setCellValue('AQ'.$row, $data['tgl_kirim']);
                $excel->setActiveSheetIndex(0)->setCellValue('AR'.$row, $data['lain_1']);
                $excel->setActiveSheetIndex(0)->setCellValue('AS'.$row, $data['lain_2']);
                $excel->setActiveSheetIndex(0)->setCellValue('AT'.$row, $data['broker_agent']);
                $excel->setActiveSheetIndex(0)->setCellValue('AU'.$row, $data['kode_broker_agent']);
                $excel->setActiveSheetIndex(0)->setCellValue('AV'.$row, $data['nama_broker_agent']);
                $excel->setActiveSheetIndex(0)->setCellValue('AW'.$row, $data['nilai_tanggungan']);
                $excel->setActiveSheetIndex(0)->setCellValue('AX'.$row, $data['rate_premi']);
                $excel->setActiveSheetIndex(0)->setCellValue('AY'.$row, $data['nilai_premi']);
                $excel->setActiveSheetIndex(0)->setCellValue('AZ'.$row, $data['tgl_awal_tanggungan']);
                $excel->setActiveSheetIndex(0)->setCellValue('BA'.$row, $data['tgl_akhir_tanggungan']);
                $excel->setActiveSheetIndex(0)->setCellValue('BB'.$row, $data['jk_waktu_tanggungan']);
                $excel->setActiveSheetIndex(0)->setCellValue('BC'.$row, $data['flag_status_validasi_web']);
                $excel->setActiveSheetIndex(0)->setCellValue('BD'.$row, $data['keterangan'] == '' ? $keterangan : $data['keterangan']);
		$excel->setActiveSheetIndex(0)->setCellValue('BE'.$row, $data['no_polis_acs']);
                $row++;
            }
        }

        // Set height semua kolom menjadi auto (mengikuti height isi dari kolommnya, jadi otomatis)
        $excel->getActiveSheet()->getDefaultRowDimension()->setRowHeight(-1);
        // Set orientasi kertas jadi LANDSCAPE
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
        // Set judul file excel nya
        $excel->getActiveSheet(0)->setTitle('Data Monitoring');
        $excel->setActiveSheetIndex(0);
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Data Monitoring.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }
}