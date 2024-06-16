<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableModel {
  private $head;
  private $sections;

  public function __construct($id) {
    $this->head = new TableHeadModel($id);
    $this->sections = new TableSectionsModel($id);
  }

  public function getHead(): TableHeadModel {
    return $this->head;
  }

  public function getSections(): TableSectionsModel {
    return $this->sections;
  }
}
