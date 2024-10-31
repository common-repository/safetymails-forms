<?php

/**
 * layout da página de configurações
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
    <h1><?=__('Edit Form', 'safetymail-form')?> <?=$form['name']?></h1>

    <div class="updated notice hidden">
        <p id="form-success"></p>
    </div>

    <div class="form-group">
        <label for="name">
            <?=__('Name', 'safetymail-form')?>
        </label>
        <input type="name" class="form-control" id="name" aria-describedby="nameHelp" value="<?=$form['name']?>" placeholder="<?=__('Ex: contact, newsletter, etc.', 'safetymail-form')?>"
            required>
        <small id="nameHelp" class="form-text text-muted">
            <?=__('A simple name to identify the form', 'safetymail-form')?>
        </small>
    </div>

    <ul class="nav nav-tabs" id="builer-menu" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="builder-tab" data-toggle="tab" href="#builder" role="tab" aria-controls="builder" aria-selected="true">
                <?=__('Build your form', 'safetymail-form')?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="form-settings-tab" data-toggle="tab" href="#form-settings" role="tab" aria-controls="form-settings" aria-selected="true">
                <?=__('Form settings', 'safetymail-form')?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="message-settings-tab" data-toggle="tab" href="#message-settings" role="tab" aria-controls="message-settings" aria-selected="true">
                <?=__('Message settings', 'safetymail-form')?>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="api-settings-tab" data-toggle="tab" href="#api-settings" role="tab" aria-controls="api-settings" aria-selected="true">
                <?=__('Activate email validation', 'safetymail-form')?>
            </a>
        </li>
    </ul>

    <div class="tab-content">
        <div id="builder" class="tab-pane active">
            <div class="form-group">
                <div id="fb-editor"></div>
            </div>
        </div>
        <div id="form-settings" class="tab-pane fade">
            <div class="form-group">
                <label class="col-md-12" id="invalidCallbackLabel"><?=__('Validation message', 'safetymail-form')?></label>
                <input type="text" class="form-control" id="invalidCallback" value="<?=$form['invalid_callback']?>" aria-describedby="invalidCallbackHelp">
                <small id="nameHelp" class="form-text text-muted">
                    <?=__('This message will be displayed for invalid email addresses', 'safetymail-form')?>
                </small>
            </div>
            <div class="form-group">
                <label class="col-md-12"><?=__('Post-processing action', 'safetymail-form')?></label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary <?=$form['action'] === 'NOTHING' ? 'active' : ''?>">
                        <input type="radio" name="action" class="action" id="actionNothing" value="NOTHING" <?=$form['action'] === 'NOTHING' ? 'checked' : ''?>> <?=__('None', 'safetymail-form')?>
                    </label>
                    <label class="btn btn-secondary <?=$form['action'] === 'MESSAGE' ? 'active' : ''?>">
                        <input type="radio" name="action" class="action" id="actionMessage" value="MESSAGE" <?=$form['action'] === 'MESSAGE' ? 'checked' : ''?>> <?=__('Show Message', 'safetymail-form')?>
                    </label>
                    <label class="btn btn-secondary <?=$form['action'] === 'REDIRECT' ? 'active' : ''?>">
                        <input type="radio" name="action" class="action" id="actionRedirect" value="REDIRECT" <?=$form['action'] === 'REDIRECT' ? 'checked' : ''?>> <?=__('Redirect', 'safetymail-form')?>
                    </label>
                </div>
            </div>

            <div class="form-group <?=$form['action'] === 'NOTHING' ? 'hidden' : ''?>" id="actionContentGroup">
                <label class="col-md-12" id="actionContentLabel">
                    <?=$form['action'] === 'MESSAGE'
                        ? __('Message', 'safetymail-form')
                        : __('Redirect URL', 'safetymail-form')
                    ?>
                </label>
                <input type="text" class="form-control" id="actionContent" value="<?=$form['action_content']?>" aria-describedby="actionContentHelp">
            </div>

            <div class="form-group">
                <label class="col-md-12"><?=__('Send email status', 'safetymail-form')?></label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary <?=intval($form['show_status']) ? 'active' : ''?>">
                        <input type="radio" name="showStatus" class="showStatus" id="showStatusTrue" value="1" <?=intval($form['show_status']) ? 'checked' : ''?>><?=__('Yes', 'safetymail-form')?>
                    </label>
                    <label class="btn btn-secondary <?=!intval($form['html']) ? 'active' : ''?>">
                        <input type="radio" name="showStatus" class="showStatus" id="showStatusFalse" value="0" <?=!intval($form['show_status']) ? 'checked' : ''?>><?=__('No', 'safetymail-form')?>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-md-12"><?=__('Send invalid email addresses', 'safetymail-form')?></label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label for="protectedTrue" class="btn btn-secondary <?=intval($form['protected']) ? 'active' : ''?>">
                        <input type="radio" name="protected" class="protected" id="protectedTrue" value="1" <?=intval($form['protected']) ? 'checked' : ''?>> <?=__('Yes', 'safetymail-form')?>
                    </label>
                    <label for="protectedFalse" class="btn btn-secondary <?=!intval($form['protected']) ? 'active' : ''?>">
                        <input type="radio" name="protected" class="protected" id="protectedFalse" value="0" <?=!intval($form['protected']) ? 'checked' : ''?>> <?=__('No', 'safetymail-form')?>
                    </label>
                </div>
            </div>
        </div>
        <div id="message-settings" class="tab-pane fade">
            <div class="form-group">
                <label for="formAction"><?=__('Subject', 'safetymail-form')?></label>
                <input type="text" class="form-control" id="subject" value="<?=$form['subject']?>" aria-describedby="subjectHelp" required>
                <small id="subjectHelp" class="form-text text-muted"><?=__('Subject for the messages sent from the form', 'safetymail-form')?></small>
            </div>
            <div class="form-group">
                <label for="formAction"><?=__('Recipient email', 'safetymail-form')?></label>
                <input type="email" class="form-control" id="emailRecipient" value="<?=$form['email_recipient']?>" aria-describedby="emailRecipientHelp" required>
                <small id="emailRecipientHelp" class="form-text text-muted"><?=__('Email address to send mail to', 'safetymail-form')?></small>
            </div>
            <div class="form-group">
                <label for="emailReplyTo"><?=__('Reply email', 'safetymail-form')?></label>
                <input type="email" class="form-control" id="emailReplyTo" value="<?=$form['email_replyto']?>" aria-describedby="nameHelp">
                <small id="emailReplyToHelp" class="form-text text-muted"><?=__('Optional reply-to email for the messages sent from the form', 'safetymail-form')?></small>
            </div>
            <div class="form-group">
                <label class="col-md-12"><?=__('Content type', 'safetymail-form')?></label>
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                    <label class="btn btn-secondary <?=intval($form['html']) ? 'active' : ''?>">
                        <input type="radio" name="contentType" class="contentType" id="contentTypeHTML" VALUE="1" <?=intval($form['html']) ? 'checked' : ''?>> HTML
                    </label>
                    <label class="btn btn-secondary <?=!intval($form['html']) ? 'active' : ''?>">
                        <input type="radio" name="contentType" class="contentType" VALUE="0" id="contentTypeTXT" <?=!intval($form['html']) ? 'checked' : ''?>> TXT
                    </label>
                </div>
            </div>
        </div>
        <div id="api-settings" class="tab-pane">
            <div class="form-group">
                <div class="alert alert-info small" role="alert">
                    <?=__("Register your Form API on SafetyMail's SafetyOptin, copy the data (API Key and Ticket API) and paste here to link your form to your integration.", 'safetymail-form');?>
                </div>
            </div>
            <div class="form-group">
                <label for="key"><?=__('API Key', 'safetymail-form')?></label>
                <input type="text" class="form-control" id="key" value="<?=$form['api_key']?>" aria-describedby="keyHelp" required>
                <small id="keyHelp" class="form-text text-muted"><?=__('Key given by SafetyOptin', 'safetymail-form')?></small>
            </div>
            <div class="form-group">
                <label for="ticket"><?=__('Ticket API', 'safetymail-form')?></label>
                <input type="text" class="form-control" id="ticket" value="<?=$form['api_ticket']?>" aria-describedby="ticketHelp" required>
                <small id="ticketHelp" class="form-text text-muted"><?=__('Ticket given by SafetyOptin', 'safetymail-form')?></small>
            </div>
        </div>
    </div>
    <div class="form-group">
        <button id="edit-form" class="button button-primary"><?=__('Save Form', 'safetymail-form')?></button>
        <a href="<?=admin_url( 'admin.php?page=safetymail-form' )?>" class="button button-secondary"><?=__('Return', 'safetymail-form')?></a>
    </div>
</div>
<script>
  const form = '<?=$form['element'];?>'.replace(/\\/g, '')
  const id = <?=$form['id'];?>
</script>
