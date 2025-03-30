<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableSectionModel;

/**
 * @var TableSectionModel $section
 */

?>
<div class="border-b border-gray-200">
    <div class="flex">
      <?php if (!empty($section->getIcon())): ?>
          <div>
            <?php echo $section->getIcon() ?>
          </div>
      <?php endif; ?>
        <h2 class="table-price__section-title"><?php echo $section->getTitle() ?></h2>
    </div>
</div>
