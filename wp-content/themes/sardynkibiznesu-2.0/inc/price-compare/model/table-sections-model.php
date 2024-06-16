<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableSectionsModel {
  private $field_name = 'section';
  private $sections;

  public function __construct($id) {
    $sections = get_field('section', $id);

    if (empty($sections)) {
      return;
    }

    $this->sections = array_map(function($section) {
      return new TableSectionModel($section);
    }, $sections);
  }

  /**
   * @return TableSectionModel[]
   */
  public function getSections() {
    return $this->sections;
  }
}
