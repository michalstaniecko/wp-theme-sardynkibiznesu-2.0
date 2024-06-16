<?php

namespace SardynkiBiznesu\PriceCompare;

use SardynkiBiznesu\PriceCompare\Model\TableModel;

class PriceCompare {
  private static $instance = null;

  public static function getInstance() {
    if (self::$instance === null) {
      self::$instance = new static();
    }
    return self::$instance;
  }

  private function __construct() {
    require_once get_stylesheet_directory() . '/inc/price-compare/shortcodes.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-head-cell-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-sections-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-section-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-head-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-row-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-rows-model.php';
    require_once get_stylesheet_directory() . '/inc/price-compare/model/table-cell-model.php';

    $this->init();
  }

  public function init() {
    add_action('init', array($this, 'register_post_type'));

    Shortcodes::getInstance()->init();
  }

  public function register_post_type() {
    $labels = array(
      'name' => __('Price Compare', 'sardynkibiznesu'),
      'singular_name' => __('Price Compare', 'sardynkibiznesu'),
      'menu_name' => __('Price Compare', 'sardynkibiznesu'),
      'name_admin_bar' => __('Price Compare', 'sardynkibiznesu'),
      'add_new' => __('Add New', 'sardynkibiznesu'),
      'add_new_item' => __('Add New Price Compare', 'sardynkibiznesu'),
      'new_item' => __('New Price Compare', 'sardynkibiznesu'),
      'edit_item' => __('Edit Price Compare', 'sardynkibiznesu'),
      'view_item' => __('View Price Compare', 'sardynkibiznesu'),
      'all_items' => __('All Price Compares', 'sardynkibiznesu'),
      'search_items' => __('Search Price Compares', 'sardynkibiznesu'),
      'parent_item_colon' => __('Parent Price Compares:', 'sardynkibiznesu'),
      'not_found' => __('No price compares found.', 'sardynkibiznesu'),
      'not_found_in_trash' => __('No price compares found in Trash.', 'sardynkibiznesu')
    );

    $args = array(
      'labels' => $labels,
      'description' => __('Description.', 'sardynkibiznesu'),
      'public' => false,
      'publicly_queryable' => false,
      'show_ui' => true,
      'show_in_menu' => true,
      'query_var' => true,
      'rewrite' => false,
      'capability_type' => 'post',
      'has_archive' => true,
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array('title'),
      'exclude_from_search' => true,
    );

    register_post_type('price-compare', $args);
  }

  public function get_table($id) {
    $table = get_post($id);

    if ($table === null) {
      return null;
    }
    try {
      $tableModel = new TableModel($id);
    } catch (\Exception $e) {
      throw new \Exception('Error while creating table model');
    }
    return $tableModel;
  }
}
