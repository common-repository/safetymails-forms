/* global ajax_object, jQuery */
(function ($) {
  'use strict'

  /**
  * All of the code for your public-facing JavaScript source
  * should reside in this file.
  *
  * Note: It has been assumed you will write jQuery code here, so the
  * $ function reference has been prepared for usage within the scope
  * of this function.
  *
  * This enables you to define handlers, for when the DOM is ready:
  *
  * $(function() {
  *
  * });
  *
  * When the window is loaded:
  *
  * $( window ).load(function() {
  *
  * });
  *
  * ...and/or other possibilities.
  *
  * Ideally, it is not considered best practise to attach more than a
  * single DOM-ready or window-load handler for a particular page.
  * Although scripts in the WordPress core, Plugins and Themes may be
  * practising this, we should strive to set a better example in our own work.
  */

  $(window).load(function () {
    if ($('.form-render').length) {
      const form = $('.form-render').attr('data-elements').replace(/\\/g, '')

      $('.form-render').formRender({
        dataType: 'json',
        formData: form
      })

      $('.safetymail').submit(function (event) {
        event.preventDefault()

        $('#error-container').addClass('hidden')
        $('#success-container').addClass('hidden')
        $('.safetymail input[type=email]')
          .removeClass('is-invalid')
          .removeClass('is-valid')
        $('.invalid-feedback').remove()

        if (passValidation()) {
          let email = window.btoa($('.safetymail input[type=email]').val())
          let url = 'https://optin.safetymails.com/main/safetyoptin'
          let apiKey = $('#key').val()
          let ticketApi = $('#ticket').val()
          $.get(
            `${url}/${apiKey}/${ticketApi}/${email}`,
            function (data) {
              switch (data.StatusCall) {
                case 'Failed':
                  $('#error-container')
                    .removeClass('hidden')
                    .html('Não foi possível validar seu email. Por favor, tente novamente.')

                  window.location.href = '#error-container'
                  break
                default:
                  if (data.StatusEmail === 'VALIDO') {
                    $.post({
                      type: 'POST',
                      url: ajax_object.ajax_url,
                      data: {
                        action: 'send_mail',
                        whatever: ajax_object.we_value,
                        id: $('#safetyId').val(),
                        fields: getFields()
                      },
                      success: function (response) {
                        console.log(response)
                        $('.safetymail input[type=email]')
                          .addClass('is-valid')

                        switch ($('.safetymail').data('type')) {
                          case 'MESSAGE':
                            $('#success-container')
                              .removeClass('hidden')
                              .html($('.safetymail').data('message'))
                            break
                          case 'REDIRECT':
                            $('.safetymail').unbind('submit').submit()
                            break
                          default:
                            $('#success-container')
                              .removeClass('hidden')
                              .html('Mensagem enviada com sucesso!')
                        }
                      },
                      error: function () {
                        $('#form-error').text('Erro ao cadastrar formulário.')
                        $('.error').removeClass('hidden')
                      }
                    })
                  } else {
                    $('.safetymail input[type=email]')
                      .focus()
                      .addClass('is-invalid')
                      .after(`<div class="invalid-feedback">${$('#callback').val()}</div>`)
                  }
              }
            })
            .fail(function (response, status, message) {
              $('#error-container')
                .removeClass('hidden')
                .html('Alguma extensão está bloqueando a comunicação com nossos servidores. Por favor, desative-a e tente novamente.')
            })
        }
      })

      $('.safetymail input[type=email]').on({
        blur: function (event) {
          $('#error-container').addClass('hidden')
          $('#success-container').addClass('hidden')
          $(this).removeClass('is-invalid').removeClass('is-valid')
          $('.invalid-feedback').remove()

          if (this.value.length > 0) {
            let email = window.btoa(this.value)
            let url = 'https://optin.safetymails.com/main/safetyoptin'
            let apiKey = $('#key').val()
            let ticketApi = $('#ticket').val()

            $.get(
              `${url}/${apiKey}/${ticketApi}/${email}`,
              function (data) {
                switch (data.StatusCall) {
                  case 'Failed':
                    $('#error-container')
                      .removeClass('hidden')
                      .html('Não foi possível validar seu email. Por favor, tente novamente.')

                    window.location.href = '#error-container'
                    break
                  default:
                    if (data.StatusEmail === 'VALIDO') {
                      $('.safetymail input[type=email]')
                        .addClass('is-valid')
                    } else {
                      $('.safetymail input[type=email]')
                        .focus()
                        .addClass('is-invalid')
                        .after(`<div class="invalid-feedback">${$('#callback').val()}</div>`)
                    }
                }
              })
              .fail(function (response, status, message) {
                $('#error-container')
                  .removeClass('hidden')
                  .html('Alguma extensão está bloqueando a comunicação com nossos servidores. Por favor, desative-a e tente novamente.')
              })
          } else {
            $('.safetymail input[type=email]')

              .addClass('is-invalid')
              .after(`<div class="invalid-feedback">${$('#callback').val()}</div>`)
          }
        }
      })

      const passValidation = function () {
        let valid = true
        $('.safetymail input').each(function (index, item) {
          if (item.checkValidity() === false) {
            $(item).focus()
            valid = false
            return false
          }
        })
        return valid
      }

      const getFields = function () {
        let fields = {}
        $('.safetymail input, .safetymail textarea, .safetymail select')
          .each(function (index, item) {
            let label = $(`label[for=${slug(item.name)}]`).text()
            let name = slug($(`label[for=${slug(item.name)}]`).text())

            if ($(item).is('select')) {
              fields[name] = {
                name: label,
                value: $(`#${item.id} option:selected`).text()
              }
              return true
            }

            switch (item.type) {
              case 'hidden':
                break
              case 'checkbox':
              case 'radio':
                if (item.checked) {
                  let value = $(`#${item.id} + label`).text()
                  fields[name] = (fields[name] === undefined)
                    ? {name: label, value: value}
                    : {name: label, value: `${fields[name].value}, ${value}`}
                }
                break
              default:
                fields[name] = {name: label, value: item.value}
            }
          })
        return fields
      }

      const slug = function (str) {
        str = str.replace(/^\s+|\s+$/g, '') // trim
        str = str.toLowerCase()

        // remove accents, swap ñ for n, etc
        var from = 'ãàáäâèéëêìíïîòóöôùúüûñç·/_,:;'
        var to = 'aaaaaeeeeiiiioooouuuunc------'
        for (var i = 0, l = from.length; i < l; i++) {
          str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i))
        }

        str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
          .replace(/\s+/g, '-') // collapse whitespace and replace by -
          .replace(/-+/g, '-') // collapse dashes

        return str
      }
    }
  })
})(jQuery)
