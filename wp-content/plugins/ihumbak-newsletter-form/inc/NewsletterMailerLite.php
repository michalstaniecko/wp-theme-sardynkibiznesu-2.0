<?php

class NewsletterMailerLite extends Newsletter {

  private \MailerLite\MailerLite $mailerLite;

  public function __construct($api_key) {
    parent::__construct($api_key);

    $this->mailerLite = new \MailerLite\MailerLite([ "api_key" => $api_key ]);
  }

  protected function includes() {

  }

  public function subscribeEmail(string $email, string $name, string $groupId) {
    return $this->mailerLite
      ->subscribers
      ->create(array(
                 'email'  => $email,
                 'fields' => array(
                   'name' => $name
                 ),
                 'groups' => array(
                   $groupId
                 )
               ));
  }

  public function getLists() {
    return $this->mailerLite->groups->get();
  }
}
