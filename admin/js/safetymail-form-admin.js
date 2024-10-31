/* global jQuery, ajax_object, form, id */
jQuery(function ($) {
  let formBuilder
  $(function () {
    const editor = document.getElementById('fb-editor')
    if (editor) {
      let options = {
        disabledActionButtons: ['data', 'clear', 'save'],
        i18n: {
          locale: $('html').attr('lang'),
          preloaded: {
            'pt-BR': {
              'addOption': 'Adicionar opção',
              'allFieldsRemoved': 'Todos os campos foram removidos.',
              'allowSelect': 'Permitir seleção',
              'autocomplete': 'Autocomplete',
              'button': 'Botão',
              'cannotBeEmpty': 'Este campo não pode estar vazio',
              'checkbox': 'Caixa de seleção',
              'checkboxes': 'Caixa de seleções',
              'checkboxGroup': 'Grupo de caixa de seleção',
              'class': 'Classe',
              'clear': 'Limpar',
              'clearAll': 'Limpar',
              'clearAllMessage': 'Tem certeza de que deseja limpar todos os campos?',
              'close': 'Fechar',
              'content': 'Conteúdo',
              'copy': 'Copiar para área de transferência',
              'datefield': 'Campo de data',
              'description': 'Texto de ajuda',
              'descriptionField': 'Descrição',
              'devMode': 'Modo desenvolvedor',
              'editNames': 'Editar nomes',
              'editorTitle': 'Título',
              'editXML': 'Editar XML',
              'fieldDeleteWarning': 'Aviso de deleção do campo',
              'fieldNonEditable': 'Este campo não pode ser editado.',
              'fieldRemoveWarning': 'Tem certeza que deseja remover este campo?',
              'fieldVars': 'Variáveis do campo',
              'fileUpload': 'Carregar arquivo',
              'formUpdated': 'Formulário atualizado',
              'getStarted': 'Arraste um campo da direita para esta área',
              'header': 'Cabeçalho',
              'hidden': 'Campo oculto',
              'hide': 'Esconder',
              'label': 'Nome do campo',
              'labelEmpty': 'Campo não pode estar vazio',
              'limitRole': 'Limitar o acesso a uma ou mais das seguintes funções:',
              'mandatory': 'Obrigatório',
              'maxlength': 'Comprimento máximo',
              'minOptionMessage': 'Este campo exige um mínimo de 2 opções',
              'name': 'Alias',
              'no': 'Não',
              'off': 'Desligado',
              'on': 'Ligado',
              'opcional': 'Opcional',
              'option': 'Opção',
              'optionEmpty': 'Valor da opção vazio',
              'paragraph': 'Parágrafo',
              'placeholder': 'Placeholder',
              'placeholder.className': 'Classe',
              'placeholder.email': 'Entre seu e-mail',
              'placeholder.label': 'Rótulo',
              'placeholder.password': 'Digite sua senha',
              'placeholder.placeholder': 'Placeholder',
              'placeholder.text': 'Digite algum texto',
              'placeholder.textarea': 'Digite um monte de texto',
              'placeholder.value': 'Valor',
              'preview': 'Pré visualização',
              'radio': 'Botão de radio',
              'radioGroup': 'Grupo de radio',
              'remove': '&#215;',
              'removeMessage': 'Remover elemento',
              'required': 'Obrigatório',
              'richtext': 'Editor de texto rico',
              'roles': 'Acesso',
              'rows': 'Linhas',
              'save': 'Guardar',
              'select': 'Selecione',
              'selectColor': 'Selecione uma cor',
              'selectionsMessage': 'Permitir várias seleções',
              'selectOptions': 'Opções',
              'size': 'Tamanho',
              'sizes': 'Tamanhos',
              'sizes.lg': 'Grande',
              'sizes.m': 'Padão',
              'sizes.sm': 'pequeno',
              'sizes.xs': 'Extra pequeno',
              'style': 'Estilo',
              'styles': 'Estilos',
              'styles.btn': 'Botão estilo',
              'styles.btn.danger': 'Perigo',
              'styles.btn.default': 'Padrão',
              'styles.btn.info': 'Informações',
              'styles.btn.primary': 'Primary',
              'styles.btn.success': 'Sucesso',
              'styles.btn.warning': 'Atenção',
              'subtype': 'Subtipo',
              'text': 'Campo de texto',
              'textArea': 'Área de texto',
              'toggle': 'Alternar',
              'value': 'Valor',
              'viewJSON': '{ }',
              'viewXML': '</>',
              'warning': 'Aviso!',
              'yes': 'Sim'
            }
          }
        },
        disableFields: ['autocomplete', 'hidden', 'file'],
        controlOrder: [
          'header',
          'paragraph',
          'text',
          'textarea',
          'checkbox-group',
          'radio-group',
          'select',
          'button',
          'date',
          'number'
        ]
      }

      if (typeof form !== 'undefined') {
        options.formData = form
        options.format = 'json'
      }

      formBuilder = $(editor).formBuilder(options)
    }

    $('.code-copy').click(function (e) {
      e.preventDefault()
      let id = $(e.target).parent('a').data('target')

      var clipboardText = ''

      clipboardText = $(`#${id}`).text()
      copyToClipboard(clipboardText)
    })
  })

  /**
   *  Salva os dados do formulário no banco de dados
   */
  $('#save-form').on({
    click: function (e) {
      if (passValidation()) {
        $.post({
          type: 'POST',
          url: ajax_object.ajax_url,
          data: {
            action: 'save_form',
            whatever: ajax_object.we_value,
            name: $('#name').val(),
            subject: $('#subject').val(),
            emailRecipient: $('#emailRecipient').val(),
            emailReplyTo: $('#emailReplyTo').val(),
            html: $('.contentType:checked').val(),
            form_action: $('.action:checked').val(),
            actionContent: $('#actionContent').val(),
            showStatus: $('.showStatus:checked').val(),
            protected: $('.protected:checked').val(),
            api_key: $('#key').val(),
            api_ticket: $('#ticket').val(),
            invalid_callback: $('#invalidCallback').val(),
            fields: formBuilder.actions.getData('json', true)
          },
          success: function (e) {
            window.location.href = 'admin.php?page=safetymail-form'
          },
          error: function () {
            $('#form-error').text('Erro ao cadastrar formulário.')
            $('.error').removeClass('hidden')
          }
        })
      }
    }
  })

  /**
   *  Salva os dados do formulário no banco de dados
   */
  $('#edit-form').on({
    click: function (e) {
      if (passValidation()) {
        $.post({
          type: 'POST',
          url: ajax_object.ajax_url,
          data: {
            action: 'edit_form',
            whatever: ajax_object.we_value,
            name: $('#name').val(),
            subject: $('#subject').val(),
            emailRecipient: $('#emailRecipient').val(),
            emailReplyTo: $('#emailReplyTo').val(),
            html: $('.contentType:checked').val(),
            form_action: $('.action:checked').val(),
            actionContent: $('#actionContent').val(),
            showStatus: $('.showStatus:checked').val(),
            protected: $('.protected:checked').val(),
            api_key: $('#key').val(),
            api_ticket: $('#ticket').val(),
            invalid_callback: $('#invalidCallback').val(),
            fields: formBuilder.actions.getData('json', true),
            id: id
          },
          success: function (response) {
            formBuilder.actions.clearFields()
            window.location.href = 'admin.php?page=safetymail-form'
          },
          error: function () {
            $('#form-error').text('Erro ao cadastrar formulário.')
            $('.error').removeClass('hidden')
          }
        })
      }
    }
  })

  const passValidation = function () {
    let valid = true
    $('input').each(function (index, item) {
      if (item.checkValidity() === false) {
        let id = $(item).parents('.tab-pane').prop('id')
        $(`#${id}-tab`).trigger('click')
        $(item).focus()
        valid = false
        return false
      }
    })
    return valid
  }

  function copyToClipboard (text) {
    var textArea = document.createElement('textarea')
    textArea.value = text
    document.body.appendChild(textArea)

    textArea.select()

    try {
      var successful = document.execCommand('copy')
      var msg = successful ? 'successful' : 'unsuccessful'
      console.log('Copying text command was ' + msg)
    } catch (err) {
      console.log('Oops, unable to copy')
    }

    document.body.removeChild(textArea)
  }

  $('.action').on({
    change: function (e) {
      switch ($(e.target).val()) {
        case 'NOTHING':
          $('#actionContentGroup').addClass('hidden')
          break
        case 'MESSAGE':
          $('#actionContentLabel').text('Mensagem de resposta')
          $('#actionContentGroup').removeClass('hidden')
          break
        case 'REDIRECT':
          $('#actionContentLabel').text('URL de redirecionamento')
          $('#actionContentGroup').removeClass('hidden')
          break
      }
    }
  })
  /**
   *  Salva os dados do formulário no banco de dados
   */
  $('#edit-config').on({
    click: function (e) {
      if (passValidation()) {
        $.post({
          type: 'POST',
          url: ajax_object.ajax_url,
          data: {
            action: 'edit_config',
            whatever: ajax_object.we_value,
            host: $('#host').val(),
            port: $('#port').val(),
            email_sender: $('#emailSender').val(),
            sender_name: $('#emailSenderName').val(),
            require_auth: $('.requireAuth:checked').val(),
            user: $('#user').val(),
            pass: $('#pass').val()
          },
          success: function (e) {
            window.location.href = 'admin.php?page=safetymail-form'
          },
          error: function () {
            $('#form-error').text('Erro ao cadastrar formulário.')
            $('.error').removeClass('hidden')
          }
        })
      }
    }
  })

  $('.requireAuth').on({
    change: function (e) {
      switch ($(e.target).val()) {
        case 'SIM':
          $('.auth').removeClass('hidden')
          break
        case 'NAO':
          $('.auth').addClass('hidden')
          break
      }
    }
  })
})
