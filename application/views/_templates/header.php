<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?php echo $title; ?></title>

    <!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/bootstrap/css/bootstrap.min.css">
    <?php if ($this->uri->segment(1) != '' ) { ?>
<!-- General CSS Files -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/fontawesome/css/all.min.css">
    <?php if ($this->uri->segment(2) != '') { ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/icomoon/styles.css">
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/datatables/datatables.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/datatables/Select-1.2.4/css/select.bootstrap4.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/select2/dist/css/select2.min.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/jquery-selectric/selectric.css">
    <?php } ?>
    <?php } ?>

    <!-- Template CSS -->
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/style.css">
    <?php 
    if ($this->uri->segment(1) != '' ) {
        if ($this->uri->segment(2) != '') { 
    ?>
<link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/custom.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/css/components.css">
    <?php 
        } 
    }
    ?>
    <?php if ($this->uri->segment(2) == 'acceptance' && $this->uri->segment(3) == 'form' && $this->uri->segment(4) == 'upload') { ?>
    <link rel="stylesheet" href="<?php echo base_url(); ?>assets/modules/filepond/filepond.css">
    <?php } ?>
    <?php if ($this->uri->segment(1) == '' ) { ?>
    <!-- reCaptcha -->
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <?php } ?>
</head>

<?php 
if ($this->uri->segment(1) != '' && $this->uri->segment(1) != 'auth' && $this->uri->segment(2) != 'error') {
    $this->load->view('_templates/layout');
    $this->load->view('_templates/sidebar');
} 
?>
