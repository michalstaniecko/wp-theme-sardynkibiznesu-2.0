<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableRowModel {
  private $title;
  private $description;
  private $cells;

  public function __construct(array $row) {
    $this->title = $row['title'];
    $this->description = $row['description'];
    $this->cells = array_map(function($cell) {
      return new TableCellModel($cell);
    }, $row['values']);
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getDescription(): string {
    return $this->description;
  }

  /**
   * @return TableCellModel[]
   */
  public function getCells() {
    return $this->cells;
  }
}
