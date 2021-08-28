<?php

require plugin_dir_path( __FILE__ ) . 'getresponse-api-php-master/src/GetResponseAPI3.class.php';

add_action( 'wp_ajax_add_to_get_response', 'add_to_get_response' );
add_action( 'wp_ajax_nopriv_add_to_get_response', 'add_to_get_response' );
function add_to_get_response() {

  $userForm = $_POST['userForm'];
  foreach ( $userForm as $value ) {
    $contact[ $value['name'] ] = $value['value'];
  }

  $getresponse = new GetResponse( '04bbb1e6fb8c2fa3cb6d5a59d974d814' );
  $name        = ( ! empty( $contact['name'] ) ? $contact['name'] : '' );
  $email       = ( ! empty( $contact['email'] ) ? $contact['email'] : '' );
  $campaignId  = ( ! empty( $contact['campaignId'] ) ? $contact['campaignId'] : '' );


  $addContact[] = $getresponse->addContact( array(
    'name'       => $name,
    'email'      => $email,
    'dayOfCycle' => 0,
    'campaign'   => array( 'campaignId' => $campaignId ),
  ) );

  echo json_encode( $addContact[0] );
  wp_die();

}

?>