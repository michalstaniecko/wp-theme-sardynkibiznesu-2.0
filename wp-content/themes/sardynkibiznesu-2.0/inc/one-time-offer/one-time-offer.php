<?php

class OneTimeOffer {

  public $cookie_name = '';

  public $post_id = null;

  public function __construct() {
    add_action('template_redirect', array($this, 'run'));
  }

  public function is_enabled() {
    global $post;

    $this->post_id = $post->ID;

    return get_field('enable_one_time_offer', $this->post_id);
  }

  public function set_cookie_name() {
    $this->cookie_name = get_field('cookie_name_one_time_offer', $this->post_id);
    return !!$this->cookie_name;
  }

  public function validate_cookie() {
    return isset($_COOKIE[$this->cookie_name]);
  }

  public function remove_cookie() {
    setcookie($this->cookie_name, '1', time() - 3600, '/');
  }

  public function run() {

    if (!$this->is_enabled()) {
      return;
    }

    if (false === $this->set_cookie_name()) {
      return;
    }

    if (!$this->validate_cookie()) {
      wp_redirect('/');
    }

    $this->remove_cookie();

  }
}
