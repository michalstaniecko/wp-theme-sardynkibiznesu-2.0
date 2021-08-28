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
          window.location = "https://sardynkibiznesu.pl/zapis-na-newsletter/podziekowanie/";
        } else {
          $('.single-newsletter-form-modal .notification.error').modal('show');

        }

        $('.single-newsletter-form button').prop('disabled', false);
      },
      dataType: 'json'
    })
  }

  $(document).ready(function () {
    $grForm = $('form.getresponse-form');
    $grForm.on('submit', function (e) {
      e.preventDefault();

      $('.single-newsletter-form button').prop('disabled', true);
      data = $(this).serializeArray();
      add_contact(data);
    });

    $('.single-newsletter-form form').each(function (index, elem) {
      $(elem).validate({
        submitHandler: function (form) {
          form.submit();
        }
      })
    })

  })

})(jQuery);
