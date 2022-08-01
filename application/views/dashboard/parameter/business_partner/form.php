<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1>Parameter <em><?php echo $title; ?></em></h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>dashboard"><em>Dashboard</em></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/parameter/bp">Parameter <em><?php echo $title; ?></em></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url()."dashboard/parameter/bp/".strtolower($sub_title)."/1"; ?>"><em><?php echo $sub; ?></em></a></div>
                            <div class="breadcrumb-item"><?php echo $sub_title; ?></div>
                        </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><em><?php echo $sub; ?></em> <?php echo $sub_title; ?></h2>

                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="card">
                                    <form method="POST" id="bpForm">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Nama <em>Business Partner</em></label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="nama" id="nama" value="<?php echo $bp ? $bp['nama'] : '' ?>" placeholder="<?php echo $bp ? '' : 'Silahkan Isi Nama Business Partner' ?>" autofocus tabindex="1">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Nama <em>Business Partner</em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-0 row">
                                                <label class="col-sm-3 col-form-label">Pilih Jenis <em>Business Partner</em></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control select2" name="jenis" id="jenis" tabindex="2" data-select-on-close="true">
                                                        <option value="">Pilih Jenis</option>
                                                        <?php foreach ($jenis_bp as $key => $jenis) : ?>
                                                        <option value="<?php echo $key ?>" <?php echo $bp ? ($key == $bp['jenis'] ? 'selected' : '') : ''?>><?php echo $jenis ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Silahkan Pilih Jenis <em>Business Partner</em>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($sub_title == 'Ubah' && $bp) : ?>
                                            <input type="hidden" id="id" name="id" value="<?php echo $bp['id'] ?>" readonly>
                                            <div class="card-footer text-right" id="divBtnAction">
                                                <a href="#" class="btn btn-icon icon-left btn-danger" id="hapus"><i class="fas fa-trash-alt"></i> Hapus</a>
                                                <button class="btn btn-icon icon-left btn-primary" id="submit"><i class="fas fa-paper-plane"></i> <?php echo $sub_title; ?></button>
                                                <input type="hidden" id="flag" value="2" readonly>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($sub_title == 'Tambah') : ?>
                                            <div class="card-footer text-right" id="divBtnAction">
                                                <button class="btn btn-icon icon-left btn-primary" id="submit"><i class="fas fa-paper-plane"></i> <?php echo $sub_title; ?></button>
                                                <input type="hidden" id="flag" value="1" readonly>
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                                <div class="alert alert-light text-center" id="divAlert" style="display:none;">Proses Pengecekan</div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
            <?php $this->load->view('_templates/footer'); ?>