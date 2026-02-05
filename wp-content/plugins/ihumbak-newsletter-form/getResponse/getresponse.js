(function ($) {

  /**
   * Show inline message in the newsletter form
   * @param {jQuery} $container - The newsletter form container
   * @param {string} type - 'success' or 'error'
   */
  function showMessage($container, type) {
    // Hide any visible messages first
    $container.find('[data-message]').removeClass('is-visible');

    // Show the appropriate message
    $container.find('[data-message="' + type + '"]').addClass('is-visible');

    // If success, hide the form content
    if (type === 'success') {
      $container.addClass('newsletter-form--submitted');
    }
  }

  /**
   * Reset form state
   * @param {jQuery} $form - The form element
   */
  function resetFormState($form) {
    $form.find('button').prop('disabled', false);
    $form.find('.newsletter-form__submit').removeClass('is-loading');
  }

  /**
   * Set form to loading state
   * @param {jQuery} $form - The form element
   */
  function setLoadingState($form) {
    $form.find('button').prop('disabled', true);
    $form.find('.newsletter-form__submit').addClass('is-loading');
  }

  /**
   * AJAX call to add contact to GetResponse
   * @param {Array} userForm - Serialized form data
   * @param {jQuery} $form - The form element
   */
  function add_contact(userForm, $form) {
    var $container = $form.closest('[data-newsletter-form]');

    $.ajax({
      type: 'post',
      url: grAddContact.ajax_url,
      data: {
        action: 'add_to_get_response',
        userForm: userForm
      },
      success: function (o) {
        if (o['success'] === false) {
          showMessage($container, 'error');
          resetFormState($form);
          return false;
        }

        if (!o['httpStatus'] || o['success'] === true) {
          showMessage($container, 'success');
          $form[0].reset();
        } else {
          showMessage($container, 'error');
        }

        resetFormState($form);
      },
      error: function() {
        showMessage($container, 'error');
        resetFormState($form);
      },
      dataType: 'json'
    });
  }

  /**
   * Transform field names for API compatibility
   * @param {Object} item - Form field object
   * @returns {Object} - Transformed field object
   */
  function changeKeys(item) {
    if (item.name === 'first_name') {
      return {
        ...item,
        name: 'name'
      };
    }
    if (item.name === 'campaign_token') {
      return {
        ...item,
        name: 'campaignId'
      };
    }
    return item;
  }

  $(document).ready(function () {
    // Initialize validation for all getresponse-form instances
    $('form.getresponse-form').each(function() {
      var $form = $(this);

      $form.validate({
        submitHandler: function (form) {
          var $formEl = $(form);
          setLoadingState($formEl);

          var data = $formEl.serializeArray().map(changeKeys);
          add_contact(data, $formEl);

          return false;
        }
      });
    });

    // Allow retry on error - hide error message when user starts typing
    $(document).on('focus', '[data-newsletter-form] input', function() {
      var $container = $(this).closest('[data-newsletter-form]');
      $container.find('[data-message="error"]').removeClass('is-visible');
    });
  });

})(jQuery);
