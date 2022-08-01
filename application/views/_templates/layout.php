<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<body>
    <div id="app">
        <div class="main-wrapper main-wrapper-1">
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar justify-content-between">
                <ul class="navbar-nav mr-3">
                    <li><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
                </ul>
                
                <ul class="navbar-nav navbar-right">
                    <li class="dropdown"><a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                        <img alt="image" src="<?php echo base_url(); ?>assets/img/avatar-1.png" class="rounded-circle mr-1">
                        <div class="d-sm-inline-block">Hai, <?php echo $this->session->userdata('name'); ?></div></a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <div class="dropdown-title" id="loggedAt">Masuk Beberapa Saat Yang Lalu</div>
                            <a href="<?php echo base_url(); ?>auth/password/change" class="dropdown-item has-icon">
                                <i class="fas fa-cog"></i> Ganti Kata sandi
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?php echo base_url(); ?>auth/logout" class="dropdown-item has-icon text-danger">
                                <i class="fas fa-sign-out-alt"></i> Keluar
                            </a>
                        </div>
                    </li>
                </ul>
            </nav>
