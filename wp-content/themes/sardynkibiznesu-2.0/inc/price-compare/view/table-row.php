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

$isFirst = $row->getIndex() === 0 && $row->getSectionIndex() === 0;

$buttonClass = 'collapse-button table-price__link flex items-start gap-2 text-base hover:text-base hover:no-underline';
$descriptionClass = 'collapse font-normal text-sm text-gray-500';

if ($isFirst) {
    $buttonClass .= ' active';
    $descriptionClass .= ' show';
}

?>


<div class="grid lg:grid-cols-<?php echo $cols + 2 ?> table__row table__row--body table-border-bottom">
    <div class="grid items-start gap-2 table__row-head lg:col-span-2 table-border-bottom lg:table-border-bottom-none table-cell-padding">
        <a href="#" class="<?php echo $buttonClass ?>">
            <i class="far fa fa-angle-right mt-[2px] [.collapse-button.active_&]:rotate-90 transition-[transform] duration-300"></i>
            <h3 class="table-price__row-title [.table-price_&]:my-0 [.table-price_&]:font-medium [.table-price_&]:text-base"><?php echo $row->getTitle() ?></h3>
        </a>
        <div class="<?php echo $descriptionClass; ?>"><?php echo $row->getDescription() ?></div>
    </div>
    <div class="grid lg:col-span-3 grid-cols-<?php echo $cols ?>">
      <?php include 'table-cells.php'; ?>
    </div>
</div>
