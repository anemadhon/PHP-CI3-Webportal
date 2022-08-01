<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1 class="text-truncate sort">Parameter <em><?php echo $title; ?></em></h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>dashboard"><em>Dashboard</em></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/parameter/pksxbp" class="text-truncate">Parameter <em><?php echo $title; ?></em></a></div>
                            <div class="breadcrumb-item"><em><?php echo $sub; ?></em></div>
                        </div>
                    </div>
                    <?php if ($this->session->flashdata('success')) : ?>
                    <div class="alert alert-success alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert"><span>×</span></button>
                            <?php echo $this->session->flashdata('success'); ?>
                        </div>
                    </div>
					<?php endif; ?>
					<?php if ($this->session->flashdata('failed')) : ?>
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert"><span>×</span></button>
                            <?php echo $this->session->flashdata('failed'); ?>
                        </div>
                    </div>
					<?php endif; ?>
                    <div class="alert alert-primary alert-has-icon">
                      <div class="alert-icon"><i class="far fa-lightbulb"></i></div>
                      <div class="alert-body">
                        <div class="alert-title">Info</div>
                        <ul>
                            <li>Kode Bank Induk : Kode Bank Induk di Sistem ACS menu "Maintenance Business Partner" Group kolom/attribut "Business Partner Group Code"</li>
                            <li>Kode Produk Eksternal : Kode Product External yang ter-register pada PKS di Sistem ACS</li>
                            <li>Kode Cabang Bank : Kode Uker (Unit Kerja) Kantor Cabang Bank yang terdaftar pada Business Partner di Sistem ACS</li>
                        </ul>
                      </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><em><?php echo $sub; ?></em></h2>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <a href="<?php echo base_url(); ?>dashboard/parameter/pksxbp/add" class="btn btn-icon icon-left btn-primary"><i class="fas fa-pencil-alt"></i> Tambah Baru</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="pksBpTable">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th><em>Business Partner</em></th>
                                                        <th>No. PKS Askrindo</th>
                                                        <th>No. PKS Eksternal</th>
                                                        <th>Kode Produk Eksternal</th>
                                                        <th>Kode Bank Induk</th>
                                                        <th>Kode Cabang Bank</th>
                                                        <th>List Kode Cabang Askrindo</th>
                                                        <th>Aksi</th>
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