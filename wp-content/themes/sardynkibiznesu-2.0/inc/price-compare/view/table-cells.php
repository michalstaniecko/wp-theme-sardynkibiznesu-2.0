<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableCellModel;

/**
 * @var TableCellModel[] $cells
 */

foreach ($cells as $cell): ?>
    <div class="flex items-center justify-center table__row-cell first:table-border-left table-border-right table-cell-padding">
      <?php //print_r($cell->getCell()); ?>
      <?php echo $cell->getValue() ?>
    </div>
<?php endforeach; ?>
