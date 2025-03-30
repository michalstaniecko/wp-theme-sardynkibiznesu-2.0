<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableSectionModel {
  private $title;
  private $icon;

  private $rows;

  private $index;

  public function __construct($section) {
    $this->title = $section['title'];
    $this->icon = $section['icon'];
    $this->index = $section['index'];

    $this->rows = new TableRowsModel($section['rows'], $this->index);
  }

  public function getTitle(): string {
    return $this->title;
  }

  public function getIcon(): string {
    return $this->icon;
  }

  /**
   * @return TableRowModel[]
   */
  public function getRows() {
    return $this->rows->getRows();
  }
}
