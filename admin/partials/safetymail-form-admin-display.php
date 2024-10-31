<?php

/**
 * layout da página de administração
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://www.henriquerodrigues.me
 * @since      1.0.0
 *
 * @package    Safetymail_Form
 * @subpackage Safetymail_Form/admin/partials
 */

?>
<div class="wrap">
    <img class="plugin-logo" src="<?=plugin_dir_url( __FILE__ ) . '../img/logo.png'?>">
    <h1 class="wp-heading-inline"><?=__('Forms', 'safetymail-form')?></h1>
    <a href="<?=admin_url( 'admin.php?page=safetymail-form-new' )?>" class="page-title-action"><?=__('Add New', 'safetymail-form')?></a>
    <hr class="wp-header-end">
    <div class="meta-box-sortables ui-sortable">
        <form method="post">
            <?php
            $this->forms_list->prepare_items();
            $this->forms_list->display();
            ?>
        </form>
    </div>
</div>
