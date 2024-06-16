<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableHeadModel {
  private $field_name = 'head';

  /**
   * @var TableHeadCellModel[]
   */
  private $head;

  public function __construct($id) {
    $head = get_field('head', $id)['product'];
    if (empty($head)) {
      return;
    }
    $this->head = array_map(function($head_cell) {
      return new TableHeadCellModel($head_cell);
    }, $head);
  }

  public function getHead(): array {
    return $this->head;
  }

  public function countColumns() {
    return count($this->head);
  }
}
