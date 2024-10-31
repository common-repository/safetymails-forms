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
    <h1>Configurações SafetyMails</h1>
    <p>
        Configurações de envio de email
    </p>

    <div class="updated notice hidden">
        <p id="form-success"></p>
    </div>

    <div class="form-group">
        <label for="host">
            Servidor SMTP
        </label>
        <input type="text" class="form-control" name="host" value="<?=$config['host']?>" id="host" aria-describedby="hostHelp" required />
        <small id="hostHelp" class="form-text text-muted">
            Endereço de servidor de envio das mensagens.
        </small>
    </div>

    <div class="form-group">
        <label for="port">
            Porta
        </label>
        <input type="text" class="form-control" name="port" id="port" value="<?=empty($config['port'])?'25':$config['port']?>" aria-describedby="portHelp" required />
        <small id="portHelp" class="form-text text-muted">
            Número da porta reservada para o SMTP o servidor
        </small>
    </div>

    <div class="form-group">
        <label for="emailSenderName"><?=__('Name to send mail as', 'safetymail-form')?></label>
        <input type="text" class="form-control" id="emailSenderName" value="<?=$config['sender_name']?>" aria-describedby="emailSenderNameHelp" required>
    </div>

    <div class="form-group">
        <label for="emailSender"><?=__('Sender email', 'safetymail-form')?></label>
        <input type="email" class="form-control" id="emailSender" value="<?=$config['email_sender']?>" aria-describedby="emailSenderHelp" required>
        <small id="emailSenderHelp" class="form-text text-muted"><?=__('Email address to send mail from', 'safetymail-form')?></small>
    </div>

    <div class="form-group">
        <label class="col-md-12">Seu servidor requer autenticação?</label>
        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-secondary <?=intval($config['require_auth']) ? 'active' : ''?>">
                <input type="radio" name="requireAuth" class="requireAuth" value="SIM" id="requireAuthYes" <?=intval($config['require_auth']) ? 'checked' : ''?>> Sim
            </label>
            <label class="btn btn-secondary <?=!intval($config['require_auth']) ? 'active' : ''?>">
                <input type="radio" name="requireAuth" class="requireAuth" value="NAO" id="requireAuthNo" <?=!intval($config['require_auth']) ? 'checked' : ''?>> Não
            </label>
        </div>
    </div>

    <div class="form-group auth <?=intval($config['require_auth']) ? '' : 'hidden'?>">
        <label for="user">
            Usuário
        </label>
        <input type="text" class="form-control" name="user" value="<?=$config['user']?>" id="user" aria-describedby="userHelp"/>
    </div>

    <div class="form-group auth <?=intval($config['require_auth']) ? '' : 'hidden'?>">
        <label for="pass">
            Senha
        </label>
        <input type="password" class="form-control" name="pass" id="pass" aria-describedby="passHelp"/>
    </div>

    <div class="form-group">
        <button id="edit-config" class="button button-primary">
            Salvar
        </button>
        <a href="<?=admin_url('admin.php?page=safetymail-form')?>" class="button button-secondary">
            Voltar
        </a>
    </div>
</div>
