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
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/acceptance/form/download"><?php echo $title; ?></a></div>
                            <div class="breadcrumb-item"><em>Form </em><?php echo $sub; ?></div>
                        </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><em>Form </em><?php echo $sub; ?></h2>

                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="card">
                                    <form method="POST" action="<?php echo base_url(); ?>download_template/excel">
                                        <div class="card-body">
                                            <div class="form-group mb-0 row">
                                                <label class="col-sm-3 col-form-label">Pilih <em>Business Partner</em> - PKS</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control select2" name="downloadparam" id="downloadparam" required data-select-on-close="true">
                                                        <option value="">Pilih Business Partner - PKS</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" id="userCabang" value="<?php echo $cabang; ?>">
                                        <div class="card-footer text-right">
                                            <button class="btn btn-icon icon-left btn-primary"><i class="fas fa-file-download"></i> Unduh</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view('_templates/footer'); ?>