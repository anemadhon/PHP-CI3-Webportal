<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1><?php echo $title; ?></h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>dashboard"><em>Dashboard</em></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/report/upload/successed"><?php echo $title; ?></a></div>
                            <div class="breadcrumb-item"><?php echo $sub; ?></div>
                        </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><?php echo $sub; ?></h2>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <a href="<?php echo base_url(); ?>report_successed_upload/download_excel" class="btn btn-icon icon-left btn-primary"><i class="fas fa-file-excel"></i> Unduh Laporan Excel</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="reportSuccessedTable">
                                            	<thead class="text-center">
                                                    <tr>
                                                        <th class="no-sort">#</th>
                                                        <th>Nama Berkas</th>
                                                        <th>No. <em>Batch</em></th>
                                                        <th>Waktu Unggah</th>
							<th>Pengunggah</th>
                                                        <th>No Rekening Pinjaman</th>
                                                        <th>No Perjanjian Kredit (PK)</th>
                                                        <th>No Polis ACS</th>
                                                        <th class="no-sort"><em>Flag</em> Status Terbit</th>
                                                        <th class="no-sort"><em>Flag</em> Status Terbit Inquiry</th>
                                                        <th>Keterangan</th>
                                                        <th class="no-sort">Tanggal Awal PK</th>
                                                        <th class="no-sort">Tanggal Akhir PK</th>
                                                        <th class="no-sort">Jangka Waktu</th>
                                                        <th class="no-sort">Id Valuta</th>
                                                        <th class="no-sort">Kurs Valuta</th>
                                                        <th class="no-sort">Plafond</th>
                                                        <th class="no-sort">Suku Bunga</th>
                                                        <th class="no-sort">Jenis</th>
                                                        <th class="no-sort">Sub Jenis</th>
                                                        <th class="no-sort">Tipe Tujuan</th>
                                                        <th class="no-sort">Kolektibilitas</th>
                                                        <th class="no-sort">Sektor Ekonomi</th>
                                                        <th class="no-sort">Sumber Pelunasan</th>
                                                        <th class="no-sort">Sumber Dana</th>
                                                        <th class="no-sort">Mekanisme Penyaluran</th>
                                                        <th class="no-sort">CIF <em>Customer</em></th>
                                                        <th>Nama</th>
                                                        <th>No KTP</th>
                                                        <th class="no-sort">TTL</th>
                                                        <th class="no-sort">JK</th>
                                                        <th class="no-sort">Alamat (Kode Pos)</th>
                                                        <th class="no-sort">Jenis Pekerjaan</th>
                                                        <th class="no-sort">Status Pegawai</th>
                                                        <th class="no-sort">No Tlp</th>
                                                        <th class="no-sort">No HP</th>
                                                        <th class="no-sort">NPWP</th>
                                                        <th class="no-sort">Jenis Agunan</th>
                                                        <th class="no-sort">Jenis Pengikatan</th>
                                                        <th class="no-sort">Nilai Agunan</th>
                                                        <th class="no-sort">Tanggal Kirim</th>
                                                        <th class="no-sort">Lain-lain</th>
                                                        <th class="no-sort">Broker Agent</th>
                                                        <th class="no-sort">Kode Broker Agent</th>
                                                        <th class="no-sort">Nama Broker Agent</th>
                                                        <th class="no-sort">Nilai Tanggungan</th>
                                                        <th class="no-sort">Rate Premi</th>
                                                        <th class="no-sort">Nilai Premi</th>
                                                        <th class="no-sort">Tanggal Awal Tanggungan</th>
                                                        <th class="no-sort">Tanggal Akhir Tanggungan</th>
                                                        <th class="no-sort">Jangka Waktu Tanggungan</th>
                                                    </tr>
                                                </thead>
                                                <tbody></tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view('_templates/footer'); ?>