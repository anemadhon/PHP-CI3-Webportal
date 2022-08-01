<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
            <div class="main-sidebar sidebar-style-2">
                <aside id="sidebar-wrapper">
                    <div class="sidebar-brand">
                        <a href="<?php echo base_url(); ?>">Askrindo WebPortal</a>
                    </div>
                    <div class="sidebar-brand sidebar-brand-sm">
                        <a href="<?php echo base_url(); ?>">AWP</a>
                    </div>
                    <ul class="sidebar-menu">
                        <li class="menu-header">Menu Utama</li>
                        <?php echo $this->menus->create_menus();?>
                    </ul>
                </aside>
            </div>
