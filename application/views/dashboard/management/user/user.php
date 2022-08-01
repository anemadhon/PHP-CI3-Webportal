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
                    <div class="section-body">
                        <h2 class="section-title"><em><?php echo $sub; ?></em></h2>
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header">
                                        <a href="<?php echo base_url(); ?>dashboard/management/user/add" class="btn btn-icon icon-left btn-primary"><i class="fas fa-pencil-alt"></i> Tambah Baru</a>
                                    </div>
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="userTable">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>No.</th>
                                                        <th>Nama</th>
                                                        <th>Nama Pengguna</th>
                                                        <th>Peran</th>
                                                        <th>Cabang</th>
                                                        <th>Status</th>
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