(function ($) {
  function add_contact(userForm) {
    $.ajax({
      type: 'post',
      url: grAddContact.ajax_url,
      data: {
        action: 'add_to_get_response',
        userForm: userForm
      },
      success: function (o) {
        if (!o['httpStatus']) {
          var thankyou_url = userForm.find(item => item.name === 'thankyou_url').value
          window.location = thankyou_url;
        } else {
          $('.single-newsletter-form-modal .notification.error').modal('show');

        }

        $('.single-newsletter-form button').prop('disabled', false);
      },
      dataType: 'json'
    })
  }

  $(document).ready(function () {

    $('form.getresponse-form').validate({
      submitHandler: function (form) {
        $('.single-newsletter-form button').prop('disabled', true);
        data = $(form).serializeArray();
        add_contact(data);
      }
    })

    $('.single-newsletter-form form:not(.getresponse-form)').each(function (index, elem) {
      $(elem).validate({
        submitHandler: function (form) {
          form.submit();
        }
      })
    })

  })

})(jQuery);
