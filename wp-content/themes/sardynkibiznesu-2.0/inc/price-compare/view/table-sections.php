<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableSectionsModel;

/**
 * @var TableSectionsModel $table_sections
 */

$sections = $table_sections->getSections();

foreach ($sections as $section) {
  include 'table-section-head.php';
  include 'table-section.php';
}

?>
