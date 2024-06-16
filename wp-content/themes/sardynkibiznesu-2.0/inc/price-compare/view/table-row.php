<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableHeadModel;
use SardynkiBiznesu\PriceCompare\Model\TableRowModel;

/**
 * @var TableHeadModel $table_head
 * @var TableRowModel $row
 */

$cells = $row->getCells();

$cols = $table_head->countColumns();

?>


<div class="grid lg:grid-cols-<?php echo $cols + 2 ?> table__row table__row--body table-border-bottom">
    <div class="grid items-start gap-2 table__row-head lg:col-span-2 table-border-bottom lg:table-border-bottom-none table-cell-padding">
        <a href="#" class="table-price__link flex items-start gap-2 text-base hover:text-base hover:no-underline">
            <i class="far fa fa-angle-right mt-[2px]"></i>
            <div class="text-base"><?php echo $row->getTitle() ?></div>
        </a>
        <div class="collapse font-normal text-sm text-gray-500"><?php echo $row->getDescription() ?></div>
    </div>
    <div class="grid lg:col-span-3 grid-cols-<?php echo $cols ?>">
      <?php include 'table-cells.php'; ?>
    </div>
</div>
