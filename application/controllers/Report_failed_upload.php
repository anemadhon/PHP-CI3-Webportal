<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require('./application/third_party/PHPExcel/PHPExcel.php');

class Report_failed_upload extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');
		
        $this->load->model('Report_failed_upload_model', 'failed');

        $this->helper->check_is_login();
	}

    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Laporan',
            'sub' => 'Produksi Unggah Gagal'
		);
		
		$this->load->view('dashboard/report/failed', $data);
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
            'no_polis_acs' => $search,
            'no_rek_pinjaman' => $search,
            'no_perjanjian_kredit' => $search,
            'keterangan' => $search,
            'nama_debitur' => $search,
            'no_ktp_debitur' => $search,
            'a.upload_by' => $search,
            'b.upload_date' => $search
        );
        $columns_for_order = array(
            1 => 'nama_file',
            2 => 'no_batch',
            3 => 'b.upload_date',
	    4 => 'a.upload_by',
            5 => 'no_rek_pinjaman',
            6 => 'no_perjanjian_kredit',
            7 => 'no_polis_acs',
            10 => 'keterangan',
            27 => 'nama_debitur',
            28 => 'no_ktp_debitur'
        );

        $total_data_no_limits = $this->failed->get($search, $columns_for_search, $order, $columns_for_order);
        $data_failed = $this->failed->get($search, $columns_for_search, $order, $columns_for_order, $length, $start);

        $datas = array();
		$failed_data = array();

        if ($data_failed)
        {
            foreach ($data_failed as $key => $failed)
	    {
                $keterangan = $failed['flag_status_validasi_web'] === 0 ? 'Tidak Lolos Validasi Web / Gagal' : ($failed['flag_status_validasi_web'] === 1 ? 'Dalam Proses' : $failed['keterangan']);
                
                $failed_data['no'] = ((int)($key + 1) + $start);
                $failed_data['id'] = $failed['id_file'];
                $failed_data['file'] = $failed['nama_file'];
                $failed_data['batch'] = $failed['no_batch'];
                $failed_data['uploaded_time'] = $failed['upload_date'];
		$failed_data['uploaded_by'] = $failed['upload_by'];
                $failed_data['rekening'] = $failed['no_rek_pinjaman'];
                $failed_data['perjanjian_kredit'] = $failed['no_perjanjian_kredit'];
                $failed_data['no_polis'] = $failed['no_polis_acs'];
                $failed_data['terbit'] = $failed['flag_status_terbit'];
                $failed_data['terbit_inquiry'] = $failed['flag_status_terbit_inquiry'];
                $failed_data['status'] = $failed['keterangan'] == '' ? $keterangan : $failed['keterangan'];
                $failed_data['tgl_awal_pk'] = $failed['tgl_awal_pk'];
                $failed_data['tgl_akhir_pk'] = $failed['tgl_akhir_pk'];
                $failed_data['jk_waktu_kredit'] = $failed['jk_waktu_kredit'];
                $failed_data['id_valuta'] = $failed['id_valuta'];
                $failed_data['kurs_valuta'] = $failed['kurs_valuta'];
                $failed_data['plafond_kredit'] = $failed['plafond_kredit'];
                $failed_data['suku_bunga_kredit'] = $failed['suku_bunga_kredit'];
                $failed_data['jenis_kredit'] = $failed['jenis_kredit'];
                $failed_data['sub_jenis_kredit'] = $failed['sub_jenis_kredit'];
                $failed_data['type_tujuan_kredit'] = $failed['type_tujuan_kredit'];
                $failed_data['kolektibilitas_kredit'] = $failed['kolektibilitas_kredit'];
                $failed_data['sektor_ekonomi'] = $failed['sektor_ekonomi'];
                $failed_data['sumber_pelunasan_kredit'] = $failed['sumber_pelunasan_kredit'];
                $failed_data['sumber_dana_kredit'] = $failed['sumber_dana_kredit'];
                $failed_data['mekanisme_penyaluran'] = $failed['mekanisme_penyaluran'];
                $failed_data['cif_customer'] = $failed['cif_customer'];
                $failed_data['nama_debitur'] = $failed['nama_debitur'];
                $failed_data['no_ktp_debitur'] = $failed['no_ktp_debitur'];
                $failed_data['ttl'] = $failed['tmpt_lahir'].', '.$failed['tgl_lahir'];
                $failed_data['jk'] = $failed['jenis_kelamin'];
                $failed_data['alamat'] = $failed['alamat_debitur'].' - '.$failed['kode_pos'];
                $failed_data['jenis_pekerjaan'] = $failed['jenis_pekerjaan'];
                $failed_data['status_pegawai'] = $failed['status_pegawai'];
                $failed_data['tlp'] = $failed['no_tlp'];
                $failed_data['hp'] = $failed['no_hp'];
                $failed_data['npwp'] = $failed['npwp'];
                $failed_data['jenis_agunan'] = $failed['jenis_agunan'];
                $failed_data['jenis_pengikatan'] = $failed['jenis_pengikatan'];
                $failed_data['nilai_agunan'] = $failed['nilai_agunan'];
                $failed_data['tgl_kirim'] = $failed['tgl_kirim'];
                $failed_data['lain'] = $failed['lain_1'].' - '.$failed['lain_2'];
                $failed_data['broker_agent'] = $failed['broker_agent'];
                $failed_data['kode_broker_agent'] = $failed['kode_broker_agent'];
                $failed_data['nama_broker_agent'] = $failed['nama_broker_agent'];
                $failed_data['nilai_tanggungan'] = $failed['nilai_tanggungan'];
                $failed_data['rate_premi'] = $failed['rate_premi'];
                $failed_data['nilai_premi'] = $failed['nilai_premi'];
                $failed_data['tgl_awal_tanggungan'] = $failed['tgl_awal_tanggungan'];
                $failed_data['tgl_akhir_tanggungan'] = $failed['tgl_akhir_tanggungan'];
                $failed_data['jk_waktu_tanggungan'] = $failed['jk_waktu_tanggungan'];
                $datas[] = $failed_data;
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

	function download_excel()
    {
        $failed = $this->failed->get();

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
        $excel->setActiveSheetIndex(0)->setCellValue('A2', 'Laporan Gagal');

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
        if ($failed)
        {
            foreach ($failed as $key => $data) 
            {
                $excel->getActiveSheet()->getStyle("A$row:BD$row")->applyFromArray($styles_body);
    
                switch ($data['flag_status_validasi_web']) {
                    case 0:
                        $keterangan = 'Tidak Lolos Validasi Web / Gagal';
                        break;
    
                    case 4:
                        $keterangan = 'Ditolak / Gagal';
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
        $excel->getActiveSheet(0)->setTitle('Laporan Gagal');
        $excel->setActiveSheetIndex(0);
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Gagal.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }
}