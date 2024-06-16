<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableModel;

/**
 * @var TableModel $table
 */

$table_head = $table->getHead();
$table_sections = $table->getSections();

?>

<div class="w-full table-price">
    <div class="relative">
      <?php include 'table-head.php'; ?>
      <?php include 'table-sections.php'; ?>
      <?php include 'table-head.php'; ?>
    </div>
</div>

