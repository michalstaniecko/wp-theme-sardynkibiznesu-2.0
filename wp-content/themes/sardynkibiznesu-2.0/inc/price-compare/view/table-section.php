<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableSectionModel;

/**
 * @var TableSectionModel $section
 */

$rows = $section->getRows();

if (empty($rows))
  return;

foreach ($rows as $row) {
  include 'table-row.php';
}
