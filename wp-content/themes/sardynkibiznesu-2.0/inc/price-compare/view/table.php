<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableModel;

/**
 * @var TableModel $table
 */

$table_head = $table->getHead();
$table_sections = $table->getSections();

?>
<div class="table-price [.page\_\_content--boxed-without-sidebar_&]:md:-mx-[80px] [.page\_\_content--boxed-without-sidebar_&]:lg:-mx-[140px]">
  <div class="relative">
    <?php include 'table-head.php'; ?>
    <?php include 'table-sections.php'; ?>
    <?php include 'table-head.php'; ?>
  </div>
</div>
