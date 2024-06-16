<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableRowsModel {
  private $rows;

  public function __construct(array $rows) {
    $this->rows = array_map(function($row) {
      return new TableRowModel($row);
    }, $rows);
  }

  /**
   * @return TableRowModel[]
   */
  public function getRows() {
    return $this->rows;
  }
}
