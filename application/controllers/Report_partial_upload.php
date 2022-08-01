<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require('./application/third_party/PHPExcel/PHPExcel.php');

class Report_partial_upload extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('Report_partial_upload_model', 'partial');
		
        $this->helper->check_is_login();
	}

    public function index()
    {
		$this->helper->check_eligible_user_menus();

		$data = array(
			'title' => 'Laporan',
            'sub' => 'Produksi Unggah Partial Terbit'
		);

		$this->load->view('dashboard/report/partial', $data);
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

        $total_data_no_limits = $this->partial->get($search, $columns_for_search, $order, $columns_for_order);
        $data_partial = $this->partial->get($search, $columns_for_search, $order, $columns_for_order, $length, $start);

        $datas = array();
		$partial_data = array();

        if ($data_partial)
        {
            foreach ($data_partial as $key => $partial)
	    {
                $keterangan = $partial['flag_status_validasi_web'] === 2 ? 'Berhasil' : $partial['keterangan'];
                
                $partial_data['no'] = ((int)($key + 1) + $start);
                $partial_data['id'] = $partial['id_file'];
                $partial_data['file'] = $partial['nama_file'];
                $partial_data['batch'] = $partial['no_batch'];
                $partial_data['uploaded_time'] = $partial['upload_date'];
		$partial_data['uploaded_by'] = $partial['upload_by'];
                $partial_data['rekening'] = $partial['no_rek_pinjaman'];
                $partial_data['perjanjian_kredit'] = $partial['no_perjanjian_kredit'];
                $partial_data['no_polis'] = $partial['no_polis_acs'];
                $partial_data['terbit'] = $partial['flag_status_terbit'];
                $partial_data['terbit_inquiry'] = $partial['flag_status_terbit_inquiry'];
                $partial_data['status'] = $partial['keterangan'] == '' ? $keterangan : $partial['keterangan'];
                $partial_data['tgl_awal_pk'] = $partial['tgl_awal_pk'];
                $partial_data['tgl_akhir_pk'] = $partial['tgl_akhir_pk'];
                $partial_data['jk_waktu_kredit'] = $partial['jk_waktu_kredit'];
                $partial_data['id_valuta'] = $partial['id_valuta'];
                $partial_data['kurs_valuta'] = $partial['kurs_valuta'];
                $partial_data['plafond_kredit'] = $partial['plafond_kredit'];
                $partial_data['suku_bunga_kredit'] = $partial['suku_bunga_kredit'];
                $partial_data['jenis_kredit'] = $partial['jenis_kredit'];
                $partial_data['sub_jenis_kredit'] = $partial['sub_jenis_kredit'];
                $partial_data['type_tujuan_kredit'] = $partial['type_tujuan_kredit'];
                $partial_data['kolektibilitas_kredit'] = $partial['kolektibilitas_kredit'];
                $partial_data['sektor_ekonomi'] = $partial['sektor_ekonomi'];
                $partial_data['sumber_pelunasan_kredit'] = $partial['sumber_pelunasan_kredit'];
                $partial_data['sumber_dana_kredit'] = $partial['sumber_dana_kredit'];
                $partial_data['mekanisme_penyaluran'] = $partial['mekanisme_penyaluran'];
                $partial_data['cif_customer'] = $partial['cif_customer'];
                $partial_data['nama_debitur'] = $partial['nama_debitur'];
                $partial_data['no_ktp_debitur'] = $partial['no_ktp_debitur'];
                $partial_data['ttl'] = $partial['tmpt_lahir'].', '.$partial['tgl_lahir'];
                $partial_data['jk'] = $partial['jenis_kelamin'];
                $partial_data['alamat'] = $partial['alamat_debitur'].' - '.$partial['kode_pos'];
                $partial_data['jenis_pekerjaan'] = $partial['jenis_pekerjaan'];
                $partial_data['status_pegawai'] = $partial['status_pegawai'];
                $partial_data['tlp'] = $partial['no_tlp'];
                $partial_data['hp'] = $partial['no_hp'];
                $partial_data['npwp'] = $partial['npwp'];
                $partial_data['jenis_agunan'] = $partial['jenis_agunan'];
                $partial_data['jenis_pengikatan'] = $partial['jenis_pengikatan'];
                $partial_data['nilai_agunan'] = $partial['nilai_agunan'];
                $partial_data['tgl_kirim'] = $partial['tgl_kirim'];
                $partial_data['lain'] = $partial['lain_1'].' - '.$partial['lain_2'];
                $partial_data['broker_agent'] = $partial['broker_agent'];
                $partial_data['kode_broker_agent'] = $partial['kode_broker_agent'];
                $partial_data['nama_broker_agent'] = $partial['nama_broker_agent'];
                $partial_data['nilai_tanggungan'] = $partial['nilai_tanggungan'];
                $partial_data['rate_premi'] = $partial['rate_premi'];
                $partial_data['nilai_premi'] = $partial['nilai_premi'];
                $partial_data['tgl_awal_tanggungan'] = $partial['tgl_awal_tanggungan'];
                $partial_data['tgl_akhir_tanggungan'] = $partial['tgl_akhir_tanggungan'];
                $partial_data['jk_waktu_tanggungan'] = $partial['jk_waktu_tanggungan'];
                $datas[] = $partial_data;
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
        $partial = $this->partial->get();

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
        $excel->setActiveSheetIndex(0)->setCellValue('A2', 'Laporan Berhasil (Partial Terbit)');

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
        if ($partial)
        {
            foreach ($partial as $key => $data) 
            {
                $excel->getActiveSheet()->getStyle("A$row:BD$row")->applyFromArray($styles_body);
    
                switch ($data['flag_status_validasi_web']) {
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
        $excel->getActiveSheet(0)->setTitle('Laporan Partial Terbit');
        $excel->setActiveSheetIndex(0);
        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="Laporan Berhasil - Partial Terbit.xlsx"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }
}