<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
<body class="body-login">
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <?php if ($this->session->flashdata('success')) : ?>
                <div class="alert alert-light alert-dismissible show fade col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                    <div class="alert-body">
                        <button class="close" data-dismiss="alert"><span>×</span></button>
                        <?php echo $this->session->flashdata('success'); ?>
                    </div>
                </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="card card-primary">
                            <div class="card-header justify-content-center"><h4>Ganti Kata Sandi</h4></div>

                            <div class="card-body">
                                <form method="POST" id="resetPwdForm">
                                    <div class="form-group">
                                        <label for="password">Kata Sandi Baru</label>
                                        <input type="password" class="form-control" name="password" id="password" placeholder="Silahkan Isi Kata Sandi" tabindex="1">
                                        <div class="invalid-feedback">
                                            Silahkan Isi Kata Sandi
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="password-confirm">Konfirmasi Kata Sandi</label>
                                        <input type="password" class="form-control" name="confirm" id="confirm" placeholder="Silahkan Isi Konfirmasi Kata Sandi" tabindex="2">
                                        <div class="invalid-feedback">
                                            Silahkan Isi Konfirmasi
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" id="submit" tabindex="3">Ubah</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="alert alert-light text-center" id="divAlert" style="display:none;">Proses Pengecekan</div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php $this->load->view('_templates/js'); ?>