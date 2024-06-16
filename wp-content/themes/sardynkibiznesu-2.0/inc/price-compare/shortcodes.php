<?php

namespace SardynkiBiznesu\PriceCompare;

class Shortcodes {
  private static $instance = null;

  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new static();
    }
    return self::$instance;
  }

  private function __construct() {
    $this->init();
  }

  public function init() {
    add_shortcode('price_compare', array($this, 'price_compare'));
  }

  public function price_compare($atts) {
    $atts = shortcode_atts(array(
      'id' => null
    ), $atts, 'price_compare');

    if ($atts['id'] === null) {
      return '';
    }

    $table = PriceCompare::getInstance()->get_table($atts['id']);

    if ($table === null) {
      return '';
    }

    ob_start();
    require get_stylesheet_directory() . '/inc/price-compare/view/table.php';
    return ob_get_clean();
  }
}
