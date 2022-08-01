<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
<body class="body-login">
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="card card-primary">
                            
                            <div class="card-header justify-content-center"><h4><?php echo $title ?> Askrindo WebPortal</h4></div>
                            <div class="card-body">
                                <form method="POST" id="loginForm">
                                    <div class="form-group">
                                        <label for="email">Nama Pengguna</label>
                                        <input id="username" type="text" class="form-control" name="username" id="username" placeholder="Silahkan Isi Nama Pengguna" autofocus tabindex="1">
                                        <div class="invalid-feedback">
                                            Silahkan Isi Nama Pengguna
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="d-block">
                                            <label for="password" class="control-label">Kata Sandi</label>
                                        </div>
                                        <input id="password" type="password" class="form-control" name="password" id="password" value="<?php echo $default === 1 ? '' : PWD ?>" placeholder="<?php echo $default === 1 ? 'Silahkan Isi Kata Sandi' : '' ?>" tabindex="2">
                                        <div class="invalid-feedback">
                                            Silahkan Isi Kata Sandi
                                        </div>
                                    </div>

                                    <div class="form-group text-center">
                                        <div class="g-recaptcha" data-sitekey="<?php echo SITEKEY_RECAPTCHA ?>" style="display:inline-block;" tabindex="4"></div>
                                        <div class="invalid-feedback link">
                                            <a href="#" id="link">Refresh reCaptcha</a>
                                        </div>
                                    </div>
                                    <?php if (!$connection) : ?>
                                        <p class="text-center text-danger">
                                            ANDA SEDANG OFFLINE
                                            <a href="<?php echo base_url()?>" id="link"><small>Refresh Halaman</small></a>
                                        </p>
                                    <?php endif; ?>

                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary btn-lg btn-block" id="submit" tabindex="5">Masuk</button>
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