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
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/management/user"><?php echo $title; ?></a></div>
                            <div class="breadcrumb-item"><a href="<?php echo base_url()."dashboard/management/user/".strtolower($sub_title)."/1"; ?>"><em><?php echo $sub; ?></em></a></div>
                            <div class="breadcrumb-item"><?php echo $sub_title; ?></div>
                        </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><em><?php echo $sub; ?></em> <?php echo $sub_title; ?></h2>

                        <div class="row">
                            <div class="col-12 col-md-12">
                                <div class="card">
                                    <form method="POST" id="userForm">
                                        <div class="card-body">
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Nama</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="name" id="name" value="<?php echo $user ? $user['name'] : '' ?>" placeholder="<?php echo $user ? '' : 'Silahkan Isi Nama'?>" autofocus tabindex="1">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Nama
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Nama Pengguna</label>
                                                <div class="col-sm-9">
                                                    <input type="text" class="form-control" name="username" id="username" value="<?php echo $user ? $user['username'] : '' ?>" placeholder="<?php echo $user ? '' : 'Silahkan Isi Nama Pengguna'?>" tabindex="2">
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Nama Pengguna
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!$user) : ?>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Kata Sandi</label>
                                                <div class="col-sm-9">
                                                    <input type="password" class="form-control" name="password" id="password" value="<?php echo PWD ?>" readonly>
                                                    <div class="invalid-feedback">
                                                        Silahkan Isi Kata Sandi 
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                            <div class="form-group row">
                                                <label class="col-sm-3 col-form-label">Pilih Peran</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control select2" name="role" id="role" tabindex="3" data-select-on-close="true">
                                                        <option value="">Pilih Peran</option>
                                                        <?php 
                                                        if ($roles) :
                                                            foreach ($roles as $role) :
                                                        ?>
                                                        <option value="<?php echo $role['id_role'] ?>" <?php echo $user ? ($role['id_role'] == $user['linkage_id_role'] ? 'selected' : '') : '' ?>><?php echo $role['role'] ?></option>
                                                        <?php 
                                                            endforeach;
                                                        endif;
                                                        ?>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Silahkan Pilih Peran
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group mb-0 row" id="divSelectCabang" style="visibility: hidden;">
                                                <label class="col-sm-3 col-form-label">Pilih Cabang</label>
                                                <div class="col-sm-9">
                                                    <select class="form-control select2" name="cabang" id="cabang" data-select-on-close="true">
                                                        <option value="">Pilih Cabang</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Silahkan Pilih Cabang
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($sub_title == 'Ubah' && $user) : ?>
                                            <input type="hidden" id="inputKodeCabang" value="<?php echo $user['kode_cabang'] ?>" readonly>
                                            <input type="hidden" id="id" name="id" value="<?php echo $user['id_user'] ?>" readonly>
                                            <div class="card-footer text-right" id="divBtnAction">
                                                <a href="#" class="btn btn-icon icon-left btn-danger" id="hapus"><i class="fas fa-trash-alt"></i> Hapus</a>
                                                <button class="btn btn-icon icon-left btn-primary" id="submit"><i class="fas fa-paper-plane"></i> <?php echo $sub_title?></button>
                                                <input type="hidden" id="flag" value="2" readonly>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($sub_title == 'Tambah') : ?>
                                            <div class="card-footer text-right" id="divBtnAction">
                                                <button class="btn btn-icon icon-left btn-primary" id="submit"><i class="fas fa-paper-plane"></i> <?php echo $sub_title?></button>
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