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

        if (o['success'] === false) {
          $('.single-newsletter-form-modal .notification.error').modal('show');
          $('.single-newsletter-form button').prop('disabled', false);
          return false;
        }
        if (!o['httpStatus'] || o['success'] === true) {
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

  function changeKeys(item) {
    if (item.name === 'first_name') {
      return {
        ...item,
        name: 'name'
      }
    }
    if (item.name === 'campaign_token') {
      return {
        ...item,
        name: 'campaignId'
      }
    }
    return item;
  }

  $(document).ready(function () {

    $('form.getresponse-form').validate({
      submitHandler: function (form) {
        $('.single-newsletter-form button').prop('disabled', true);
        const data = $(form).serializeArray();
        add_contact(data);
      }
    })

    $('.single-newsletter-form form:not(.getresponse-form)').each(function (index, elem) {
      $(elem).validate({
        submitHandler: function (form) {
          const data = $(form).serializeArray().map(changeKeys)
          add_contact(data);

          return false;
          form.submit();
        }
      })
    })

  })

})(jQuery);
