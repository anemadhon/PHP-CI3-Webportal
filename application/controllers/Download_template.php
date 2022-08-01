<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require('./application/third_party/PHPExcel/PHPExcel.php');

class Download_template extends CI_Controller {
	public function __construct()
    {
		parent::__construct();
        date_default_timezone_set('Asia/Jakarta');

        $this->load->model('Pks_business_partner_model', 'pkspartner');
        $this->load->model('User_model', 'user');
		
        $this->helper->check_is_login();
	}

    public function index()
    {
		$this->helper->check_eligible_user_menus();

        $get_cabang_askrindo = $this->user->get_by_id($this->session->userdata['id']);

		$data = array(
			'title' => 'Akseptasi',
            'sub' => 'Unduh Template',
            'cabang' => $get_cabang_askrindo['kode_cabang']
		);

		$this->load->view('dashboard/acceptance/form/download', $data);
	}

    function excel()
    {
        $this->form_validation->set_rules(
			'downloadparam',
			'Parameter Unduh',
			'trim|required',
			array(
				'required' => 'Silahkan Pilih Parameter Download.'
			)
		);

        if ($this->form_validation->run() === FALSE)
        {
            redirect(base_url().'dashboard/acceptance/form/download');
            exit();
        }

        $id = $this->input->post('downloadparam');

        $get_parameter_data = $this->pkspartner->get_by_id($id);
        $get_cabang_askrindo = $this->user->get_by_id($this->session->userdata['id']);

        $kode_cabang = $get_cabang_askrindo['kode_cabang'] ? $get_cabang_askrindo['kode_cabang'] : 'Admin';

        $data_values = array(
            'value' => $get_parameter_data,
            'kode_cabang' => $kode_cabang
        );

        $nama_file = $kode_cabang.'_'.str_replace('/','',$get_parameter_data['pks_askrindo']).'_'.date('dmyHis').'.xlsx';

        $excel = new PHPExcel();

        $this->_set_styles_excel($excel);

        $this->_create_data_akseptasi_sheet($excel, $data_values);

        $file_informasi = PHPExcel_IOFactory::load('files/default/informasi.xlsx');
        
        $sheet_deskripsi = $file_informasi->getSheet(0);
        $sheet_legend = $file_informasi->getSheet(1);
        
        $excel->addExternalSheet($sheet_deskripsi);
        $excel->addExternalSheet($sheet_legend);

        // Proses file excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$nama_file.'"'); // Set nama file excel nya
        header('Cache-Control: max-age=0');
        
        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }

    private function _set_styles_excel($excel)
    {
        // Set orientasi kertas jadi LANDSCAPE
        $excel->getActiveSheet()->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);

        //set column width
        $column_length = 52;
        foreach (range(0, $column_length) as $column)
        {
            $excel->getActiveSheet(0)->getColumnDimensionByColumn($column)->setWidth(34);
        }

        //set row height row ke 1 - 5 & 7
        for ($i=1; $i <= 6 ; $i++)
        {
            $excel->getActiveSheet(0)->getRowDimension($i)->setRowHeight(20);
        }
        $excel->getActiveSheet(0)->getRowDimension(8)->setRowHeight(20);
        
        // set style header table default
        $style_table_header_default = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => '000033')
            ),
            'font' => array(
                'size' => 12,
                'bold' => TRUE,
                'color' => array('rgb' => 'FFFFFF')
            )
        );
        $excel->getActiveSheet(0)->getStyle('A1:C1')->applyFromArray($style_table_header_default);
        
        // set style body table default
        $style_table_body_default = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E2FFFF')
            ),
            'font' => array(
                'size' => 12,
                'bold' => TRUE
            )
        );
        $excel->getActiveSheet(0)->getStyle('A2:A6')->applyFromArray($style_table_body_default);

        // set style table default column value
        $styles_table_default_column_value = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN,
                    'color' => array('argb' => '00000000'),
                ),
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            )
        );
        $excel->getActiveSheet(0)->getStyle('B2:B6')->applyFromArray($styles_table_default_column_value);
        $excel->getActiveSheet(0)->getStyle('C2:C6')->applyFromArray($styles_table_default_column_value);

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
        $excel->getActiveSheet(0)->getStyle('A8:AZ8')->applyFromArray($styles);
            
        //set style first 5 columns table
        $style_first_five_columns = array(
            'fill' => array(
                'type' => PHPExcel_Style_Fill::FILL_SOLID,
                'color' => array('rgb' => 'E2FFFF')
            ),
            'font' => array(
                'bold' => TRUE
            )
        );
        $excel->getActiveSheet(0)->getStyle('A8:E8')->applyFromArray($style_first_five_columns);
        
        //set style first 5 columns table value
        $style_first_five_columns_value = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
            ),
            'font' => array(
                'italic' => TRUE
            )
        );
        $excel->getActiveSheet(0)->getStyle('A9:E9')->applyFromArray($style_first_five_columns_value);

        $excel->getActiveSheet(0)->getStyle('D6')->getFont()->setItalic(TRUE);

	$excel->getActiveSheet(0)->getStyle('B3')->getAlignment()->setWrapText(TRUE);
        $excel->getActiveSheet(0)->getRowDimension(3)->setRowHeight(-1);
        
        $excel->getActiveSheet(0)->getStyle('B5')->getAlignment()->setWrapText(TRUE);
        $excel->getActiveSheet(0)->getRowDimension(5)->setRowHeight(-1);
    }

    private function _create_data_akseptasi_sheet($excel, $data)
    {
        $excel->setActiveSheetIndex(0)->setCellValue('A1', 'Nama Field');
        $excel->setActiveSheetIndex(0)->setCellValue('B1', 'Value');
        $excel->setActiveSheetIndex(0)->setCellValue('C1', 'Keterangan');
        
        $excel->setActiveSheetIndex(0)->setCellValue('A2', 'Kode Bank');
        $excel->setActiveSheetIndex(0)->setCellValue('A3', 'Kode Cabang Bank');
        $excel->setActiveSheetIndex(0)->setCellValue('A4', 'Kode Cabang Askrindo');
        $excel->setActiveSheetIndex(0)->setCellValue('A5', 'Kode Produk');
        $excel->setActiveSheetIndex(0)->setCellValue('A6', 'Kode Broker Agent');
        
        $excel->setActiveSheetIndex(0)->setCellValueExplicit('B2', $data['value']['bank'], PHPExcel_Cell_DataType::TYPE_STRING);
	$excel->setActiveSheetIndex(0)->setCellValueExplicit('B3', str_replace(',', '|', $data['value']['bank_cabang']), PHPExcel_Cell_DataType::TYPE_STRING);
        $excel->setActiveSheetIndex(0)->setCellValueExplicit('B4', $data['kode_cabang'], PHPExcel_Cell_DataType::TYPE_STRING);
        $excel->setActiveSheetIndex(0)->setCellValue('B5', str_replace(',', '|', $data['value']['produk_eksternal']));
        $excel->setActiveSheetIndex(0)->setCellValue('B6', $data['value']['broker_agent']);
        
        $excel->setActiveSheetIndex(0)->mergeCells('C2:C6');
        $excel->setActiveSheetIndex(0)->setCellValue('C2', 'Default, Selalu Gunakan Data Ini');

        $excel->setActiveSheetIndex(0)->setCellValue('D6', '*NOTE: format untuk penulisan tanggal = YYYYMMDD (ex: '.date('Ymd').')');
        
        $excel->setActiveSheetIndex(0)->setCellValue('A8', 'Kode Bank');
        $excel->setActiveSheetIndex(0)->setCellValue('B8', 'Kode Cabang Bank');
        $excel->setActiveSheetIndex(0)->setCellValue('C8', 'Kode Cabang Askrindo');
        $excel->setActiveSheetIndex(0)->setCellValue('D8', 'Kode Produk');
        $excel->setActiveSheetIndex(0)->setCellValue('E8', 'Kode Broker Agent');
        
        $excel->setActiveSheetIndex(0)->setCellValue('A9', '<isi sesuai kode bank diatas>');
        $excel->setActiveSheetIndex(0)->setCellValue('B9', '<isi sesuai kode cabang bank diatas>');
        $excel->setActiveSheetIndex(0)->setCellValue('C9', '<isi sesuai kode cabang askrindo diatas>');
        $excel->setActiveSheetIndex(0)->setCellValue('D9', '<isi sesuai kode produk diatas>');
        $excel->setActiveSheetIndex(0)->setCellValue('E9', $data['value']['broker_agent'] ? '<isi sesuai kode broker agent diatas>' : '');
        
        $excel->setActiveSheetIndex(0)->setCellValue('F8', 'No Rekening Pinjaman');
        $excel->setActiveSheetIndex(0)->setCellValue('G8', 'No Perjanjian Kredit (PK)');
        $excel->setActiveSheetIndex(0)->setCellValue('H8', 'Tgl Awal PK');
        $excel->setActiveSheetIndex(0)->setCellValue('I8', 'Tgl Akhir PK');
        $excel->setActiveSheetIndex(0)->setCellValue('J8', 'Jk Waktu Kredit (Dalam Bulan)');
        $excel->setActiveSheetIndex(0)->setCellValue('K8', 'id valuta (Currency)');
        $excel->setActiveSheetIndex(0)->setCellValue('L8', 'Kurs Valuta');
        $excel->setActiveSheetIndex(0)->setCellValue('M8', 'Plafond Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('N8', 'Suku Bunga Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('O8', 'Jenis Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('P8', 'Sub Jenis Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('Q8', 'Type Tujuan Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('R8', 'Kolektibilitas Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('S8', 'Sektor Ekonomi');
        $excel->setActiveSheetIndex(0)->setCellValue('T8', 'Sumber Pelunasan Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('U8', 'Sumber Dana Kredit');
        $excel->setActiveSheetIndex(0)->setCellValue('V8', 'Mekanisme Penyaluran');
        $excel->setActiveSheetIndex(0)->setCellValue('W8', 'CIF Customer');
        $excel->setActiveSheetIndex(0)->setCellValue('X8', 'Nama Debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('Y8', 'No KTP Debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('Z8', 'Tempat Lahir');
        $excel->setActiveSheetIndex(0)->setCellValue('AA8', 'Tanggal Lahir');
        $excel->setActiveSheetIndex(0)->setCellValue('AB8', 'Jenis Kelamin');
        $excel->setActiveSheetIndex(0)->setCellValue('AC8', 'Alamat Debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('AD8', 'Kode Pos');
        $excel->setActiveSheetIndex(0)->setCellValue('AE8', 'Jenis Pekerjaan');
        $excel->setActiveSheetIndex(0)->setCellValue('AF8', 'Status Kepegawaian');
        $excel->setActiveSheetIndex(0)->setCellValue('AG8', 'No Telepon');
        $excel->setActiveSheetIndex(0)->setCellValue('AH8', 'No HP debitur');
        $excel->setActiveSheetIndex(0)->setCellValue('AI8', 'NPWP');
        $excel->setActiveSheetIndex(0)->setCellValue('AJ8', 'Jenis Agunan');
        $excel->setActiveSheetIndex(0)->setCellValue('AK8', 'Jenis Pengikatan');
        $excel->setActiveSheetIndex(0)->setCellValue('AL8', 'Nilai Agunan');
        $excel->setActiveSheetIndex(0)->setCellValue('AM8', 'Tgl Kirim');
        $excel->setActiveSheetIndex(0)->setCellValue('AN8', 'Other 1');
        $excel->setActiveSheetIndex(0)->setCellValue('AO8', 'Other 2');
        $excel->setActiveSheetIndex(0)->setCellValue('AP8', 'BROKER_AGENT');
        $excel->setActiveSheetIndex(0)->setCellValue('AQ8', 'KODE_BROKER_AGENT');
        $excel->setActiveSheetIndex(0)->setCellValue('AR8', 'NAMA_BROKER_AGENT');
        $excel->setActiveSheetIndex(0)->setCellValue('AS8', 'Nilai Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('AT8', 'Rate Premi');
        $excel->setActiveSheetIndex(0)->setCellValue('AU8', 'Nilai Premi');
        $excel->setActiveSheetIndex(0)->setCellValue('AV8', 'Tanggal Awal Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('AW8', 'Tanggal Akhir Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('AX8', 'Jk Waktu Pertanggungan');
        $excel->setActiveSheetIndex(0)->setCellValue('AY8', 'No. Surat Pengantar');
	$excel->setActiveSheetIndex(0)->setCellValue('AZ8', 'Tgl No. Surat Pengantar');

        $excel->setActiveSheetIndex(0)->setCellValue('AP9', $data['value']['broker_agent'] ? 3 : '');
        $excel->setActiveSheetIndex(0)->setCellValue('AQ9', $data['value']['broker_agent']);
        
        // Set judul sheet pertama
        $excel->setActiveSheetIndex(0);
        $excel->getActiveSheet(0)->setTitle('Data Akseptasi');
    }
}