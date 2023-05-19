<?php

class Newsletter {

  protected string $email;

  protected string $list;

  protected string $api_key;

  public function __construct($api_key) {
    $this->api_key = $api_key;
  }

  protected function includes() {

  }

  public function subscribeEmail(string $email, string $name, string $groupId) {

  }

  public function unsubscribeEmail() {

  }

  public function getLists() {

  }
}
