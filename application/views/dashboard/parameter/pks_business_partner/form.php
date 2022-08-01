<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1 class="text-truncate sort">Parameter <?php echo $title; ?></h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>dashboard"><em>Dashboard</em></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/parameter/pksxbp"  class="text-truncate sort">Parameter <em><?php echo $title; ?></em></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url()."dashboard/parameter/pksxbp/".strtolower($sub_title)."/1"; ?>"><em><?php echo $sub; ?></em></a></div>
                            <div class="breadcrumb-item"><?php echo $sub_title; ?></div>
                        </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><em><?php echo $sub; ?></em> <?php echo $sub_title; ?></h2>
                        
                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="card">
                                    <form method="POST" id="pksBpForm">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Pilih <em>Business Partner</em></label>
                                                <div class="col-sm-9">
                                                    <select class="form-control select2" name="idbusiness" id="idbusiness" autofocus data-select-on-close="true">
                                                        <option value="">Pilih <em>Business Partner</em></option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Silahkan Pilih <em>Business Partner</em>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">No. PKS Askrindo</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="askrindo" id="askrindo" value="<?php echo $pks_bp ? $pks_bp['pks_askrindo'] : ''?>" placeholder="<?php echo $pks_bp ? '' : 'Silahkan Isi No. PKS Askrindo'?>">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi No. PKS Askrindo
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">No. PKS Eksternal</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="eksternal" id="eksternal" value="<?php echo $pks_bp ? $pks_bp['pks_eksternal'] : ''?>" placeholder="<?php echo $pks_bp ? '' : 'Silahkan Isi No. PKS Eksternal'?>">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi No. PKS Eksternal
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Kode Produk Eksternal</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="produk" id="produk" value="<?php echo $pks_bp ? $pks_bp['produk_eksternal'] : ''?>" placeholder="<?php echo $pks_bp ? '' : 'Silahkan Isi Kode Produk Eksternall'?>">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Kode Produk Eksternal
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Kode Bank</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="bank" id="bank" value="<?php echo $pks_bp ? $pks_bp['bank'] : ''?>" placeholder="<?php echo $pks_bp ? '' : 'Silahkan Isi Kode Bank'?>">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Kode Bank
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Kode Cabang Bank</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="cabangbank" id="cabangbank" value="<?php echo $pks_bp ? $pks_bp['bank_cabang'] : ''?>" placeholder="<?php echo $pks_bp ? '' : 'Silahkan Isi Kode Cabang Bank'?>">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Kode Cabang Bank
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Kode Cabang Askrindo</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control multiselect-select-all-filtering" multiple="multiple" name="idcabangask[]" id="idcabangask"></select>
                                                    <small class="text-danger" style="display:none">
                                                        Silahkan Pilih Cabang Askrindo
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="form-group mb-0 row" id="divInputBrokerAgent" style="visibility: hidden;">
                                                <label class="col-sm-3 col-form-label">Kode Broker Agent</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="brokeragent" id="brokeragent" value="<?php echo $pks_bp ? $pks_bp['broker_agent'] : ''?>" placeholder="<?php echo $pks_bp ? '' : 'Silahkan Isi Kode Broker Agent'?>">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Kode Broker Agent
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($sub_title == 'Ubah') : ?>
                                            <input type="hidden" id="id" name="id" value="<?php echo $pks_bp['id'] ?>" readonly>
                                            <input type="hidden" id="inputIdBP" value="<?php echo $pks_bp['idbusiness'] ?>" readonly>
                                            <input type="hidden" id="inputTipeBP" value="<?php echo $pks_bp['tipe'] ?>" readonly>
                                            <input type="hidden" id="inputCabang" value="<?php echo $pks_bp['list'] ?>" readonly>
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