<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableRowsModel {
  private $rows;

  public function __construct(array $rows, int $sectionIndex) {
    $index = 0;
    $this->rows = array_map(function($row) use (&$index, $sectionIndex) {
      $row['index'] = $index++;
      $row['sectionIndex'] = $sectionIndex;
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
