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
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/acceptance/form/upload"><?php echo $title; ?></a></div>
                            <div class="breadcrumb-item"><em>Form </em><?php echo $sub; ?></div>
                        </div>
                    </div>
                    <?php if ($background > 1) : ?>
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert"><span>×</span></button>
                            Silahkan tunggu proses yang berjalan di background selesai. Terima kasih.
                        </div>
                    </div>
                    <?php endif; ?>
                    <div class="section-body">
                        <h2 class="section-title"><em>Form </em><?php echo $sub; ?></h2>
                        
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="card">
                                    <form method="POST" id="uploadForm" enctype="multipart/form-data">
                                        <div class="card-body">
                                            <div class="form-group mb-0 row">
                                                <label class="col-sm-3 col-form-label">Pilih Berkas</label>
                                                <div class="col-sm-9">
                                                    <input type="file" name="file" id="file" accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                                                    <div class="text-danger" style="display:none;">
                                                        Silahkan Pilih Berkas
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($background === 0 || $background === 1) : ?>
                                        <div class="card-footer text-right" id="upload">
                                            <button class="btn btn-icon icon-left btn-primary"><i class="fas fa-file-upload"></i> Unggah</button>
                                        </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div class="progress mb-3" data-height="35" style="display:none;">
                                    <div class="progress-bar" role="progressbar">0%</div>
                                </div>
                                <div class="alert alert-success alert-dismissible show fade" id="divAlert" style="display:none;">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert"><span>×</span></button>
                                        <span id="idSpan"></span>
                                    </div>
                                </div>
                                <div class="alert alert-danger alert-dismissible show fade" id="divAlertDanger" style="display:none;">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert"><span>×</span></button>
                                        <span id="idSpanDanger"></span>
                                    </div>
                                </div>
                                <div class="alert alert-info alert-dismissible show fade" id="divInfo" style="display:none;">
                                    <div class="alert-body">
                                        <button class="close" data-dismiss="alert"><span>×</span></button>
                                        <span>Silahkan Ke Menu Monitoring untuk Melihat Data</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view('_templates/footer'); ?>