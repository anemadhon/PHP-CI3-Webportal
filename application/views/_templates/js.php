<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- General JS Scripts -->
    <script src="<?php echo base_url(); ?>assets/modules/jquery.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/popper.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/tooltip.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/bootstrap/js/bootstrap.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/nicescroll/jquery.nicescroll.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/stisla.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/uniform.min.js"></script>

    <?php if ($this->uri->segment(1) != '' ) { ?>
<!-- JS Libraies -->
    <script src="<?php echo base_url(); ?>assets/modules/datatables/datatables.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/datatables/Select-1.2.4/js/dataTables.select.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/jquery-ui/jquery-ui.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/sweetalert/sweetalert.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/select2/dist/js/select2.full.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/jquery-selectric/jquery.selectric.min.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/multiselect/bootstrap_multiselect.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/multiselect/form_multiselect.js"></script>
    <?php } ?>

    <!-- Template JS File -->
    <script src="<?php echo base_url(); ?>assets/js/scripts.js"></script>
    <?php if ($this->uri->segment(2) == 'acceptance' && $this->uri->segment(3) == 'form' && $this->uri->segment(4) == 'upload') { ?>
    <script src="<?php echo base_url(); ?>assets/modules/filepond/filepond-plugin-file-validate-type.js"></script>
    <script src="<?php echo base_url(); ?>assets/modules/filepond/filepond.js"></script>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/upload.js"></script>
    <?php } ?>

    <!-- Page Specific JS File -->
    <?php if ($this->uri->segment(1) == '' ) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/login.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(1) == 'auth' ) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/reset_password.js"></script>
    <?php } ?>
    <?php if (($this->uri->segment(3) == 'role' || $this->uri->segment(3) == 'kc') && !$this->uri->segment(4)) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/role_x_kc.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(3) == 'user' && !$this->uri->segment(4)) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/user.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(3) == 'user' && ($this->uri->segment(4) == 'add' || $this->uri->segment(4) == 'edit')) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/user_form.js"></script>
    <?php } ?>
    <?php if (($this->uri->segment(3) == 'bp' || $this->uri->segment(3) == 'pksxbp') && !$this->uri->segment(4)) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/bp_pks.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(3) == 'bp' && ($this->uri->segment(4) == 'add' || $this->uri->segment(4) == 'edit')) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/bp_form.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(3) == 'pksxbp' && ($this->uri->segment(4) == 'add' || $this->uri->segment(4) == 'edit')) { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/pks_x_bp_form.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(3) == 'form' && $this->uri->segment(4) == 'download') { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/download.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(3) == 'monitor') { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/monitoring.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(2) == 'report' && $this->uri->segment(4) == 'successed') { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/successed.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(2) == 'report' && $this->uri->segment(4) == 'partial') { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/partial.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(2) == 'report' && $this->uri->segment(4) == 'failed') { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/failed.js"></script>
    <?php } ?>
    <?php if ($this->uri->segment(2) == 'report' && $this->uri->segment(4) == 'pending') { ?>
    <script src="<?php echo base_url(); ?>assets/js/specific_page/pending.js"></script>
    <?php } ?>
</body>
</html>