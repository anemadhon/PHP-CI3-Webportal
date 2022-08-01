<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$this->load->view('_templates/header');
?>
            <!-- Main Content -->
            <div class="main-content">
                <section class="section">
                    <div class="section-header">
                        <h1><em><?php echo $title; ?></em></h1>
                        <div class="section-header-breadcrumb">
                            <div class="breadcrumb-item active"><a href="<?php echo base_url(); ?>dashboard"><em>Dashboard</em></a></div>
                        </div>
                    </div>
                    <?php if ($this->session->flashdata('error')) : ?>
                    <div class="alert alert-danger alert-dismissible show fade">
                        <div class="alert-body">
                            <button class="close" data-dismiss="alert"><span>×</span></button>
                            <?php echo $this->session->flashdata('error'); ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </section>
            </div>
            <?php $this->load->view('_templates/footer'); ?>