<?php

require plugin_dir_path(__FILE__) . 'getresponse-api-php-master/src/GetResponseAPI3.class.php';

add_action('wp_ajax_add_to_get_response', 'ih_add_subscription');
add_action('wp_ajax_nopriv_add_to_get_response', 'ih_add_subscription');

function ih_add_subscription() {
  switch (get_field('default_newsletter', 'options')) {
    case 'mailerlite':
      add_to_mailer_lite();
      break;
    case 'getresponse':
      add_to_get_response();
      break;
    default:
      break;
  }
}

function newsletterMapUserForm($formData) {
  foreach ($formData as $value) {
    $contact[$value['name']] = $value['value'];
  }
  return $contact;
}

function add_to_mailer_lite() {
  $newsletter = new NewsletterMailerLite(get_field('newsletter_api_key', 'options'));

  $contact = newsletterMapUserForm($_POST['userForm']);
  try {
    $newsletter->subscribeEmail($contact['email'], $contact['name'], $contact['campaignId']);
  } catch (Exception $error) {
    wp_send_json_error(array(
                         'mailerlite' => 'ok',
                         'error'      => $error->getMessage(),
                         'groups'     => $error
                       ));
  }
  wp_send_json_success(array( 'mailerlite' => 'ok' ));
}

function add_to_get_response() {

  $userForm = $_POST['userForm'];
  foreach ($userForm as $value) {
    $contact[$value['name']] = $value['value'];
  }

  $getresponse = new GetResponse('04bbb1e6fb8c2fa3cb6d5a59d974d814');
  $name = ( !empty($contact['name']) ? $contact['name'] : '' );
  $email = ( !empty($contact['email']) ? $contact['email'] : '' );
  $campaignId = ( !empty($contact['campaignId']) ? $contact['campaignId'] : '' );


  $addContact[] = $getresponse->addContact(array(
                                             'name'       => $name,
                                             'email'      => $email,
                                             'dayOfCycle' => 0,
                                             'campaign'   => array( 'campaignId' => $campaignId ),
                                           ));

  echo json_encode($addContact[0]);
  wp_die();

}
