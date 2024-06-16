<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableCellModel {
  private $cell;

  /**
   * @var string
   * @return 'text' | 'yes-no'
   */
  private $type;

  private $value;

  public function __construct(array $cell) {
    $this->cell = $cell;
    $this->type = $cell['value_type'];
    $this->setValue();
  }

  public function getCell(): array {
    return $this->cell;
  }

  private function setValue() {
    switch ($this->type) {
      case 'text':
        $this->value = $this->cell['product_feature_text'];
        break;
      case 'yes-no':
        $this->value = $this->cell['product_feature_check'] === true;
        break;
      default:
        $this->value = '';
    }
  }

  /**
   * @return 'text' | 'yes-no'
   */
  public function getType(): string {
    return $this->type;
  }

  public function getValue() {
    $value = '-';
    if ($this->type ==='text')
      $value = $this->value;
    if ($this->type === 'yes-no' && $this->value === true) {
      ob_start();
      get_template_part('inc/price-compare/view/table-cell-check');
      $value = ob_get_clean();
    }
    return $value;
  }
}
