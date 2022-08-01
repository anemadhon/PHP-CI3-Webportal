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
                            <div class="breadcrumb-item"><a href="<?php echo base_url(); ?>dashboard/parameter/kc"><?php echo $title; ?></a></div>
                            <div class="breadcrumb-item"><em><?php echo $sub; ?></em></div>
                        </div>
                    </div>
                    <div class="section-body">
                        <h2 class="section-title"><em><?php echo $sub; ?></em></h2>

                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table table-striped" id="kcTable">
                                                <thead class="text-center">
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Nama Cabang</th>
                                                        <th>Kode Cabang</th>
                                                        <th><em>Linkage</em> Kanwil</th>
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