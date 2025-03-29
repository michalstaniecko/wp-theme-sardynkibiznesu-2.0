<?php

namespace SardynkiBiznesu\PriceCompare\View;

use SardynkiBiznesu\PriceCompare\Model\TableHeadModel;

/**
 * @var TableHeadModel $table_head
 */

if (empty($table_head->getHead()))
  return;

$cols = $table_head->countColumns();

$grid_class = 'lg:grid-cols-[2fr_repeat(3,1fr)]';

if ($cols === 2) {
  $grid_class = 'lg:grid-cols-[2fr_repeat(2,1fr)]';
}

?>

  <div
      class="sticky top-[88px] grid grid-cols-<?php echo $cols ?> <?php echo $grid_class ?>  table__row table__row--head table-border-bottom"
  >
    <div class="hidden lg:flex items-center lg:col-span:2"></div>
    <?php foreach ($table_head->getHead() as $head): ?>
      <div class="flex flex-col  gap-2 lg:col-span-1 table-border-left last:table-border-right table-cell-padding">
        <?php if (!empty($head->getImageSrc())): ?>
          <div class="mb-3 h-[80px]">
            <a href="<?php echo $head->getLink() ?>" title="<?php echo $head->getName() ?>" target="_blank">
              <img
                  src="<?php echo $head->getImageSrc()[0] ?>" alt="<?php echo $head->getName() ?>"
                  class="w-full h-full object-contain"
              >
            </a>
          </div>
        <?php endif; ?>
        <div
            class="mt-auto min-w-0 text-sm break-words md:text-xl font-medium text-gray-900"
        ><?php echo $head->getName() ?></div>
        <div>
          <div class="text-lg text-lg font-medium"><?php echo $head->getPrice() ?></div>
          <div class="hidden md:block text-sm font-normal"><?php echo $head->getLabel() ?></div>
        </div>
        <div class="hidden md:block">
          <a href="<?php echo $head->getLink() ?>" class="button button-small">Zobacz ofertę</a>
        </div>
        <div class="md:hidden">
          <a href="<?php echo $head->getLink() ?>" class="text-sm underline">Zobacz ofertę</a>
        </div>
      </div>
    <?php endforeach; ?>
  </div>

<?php

