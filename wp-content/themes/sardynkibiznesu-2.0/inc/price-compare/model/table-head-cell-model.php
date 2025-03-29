<?php

namespace SardynkiBiznesu\PriceCompare\Model;

class TableHeadCellModel {
  private $name;
  private $price;
  private $label;
  private $link;

  private $imageId;

  public function __construct(array $head) {
    $this->name = $head['name'];
    $this->price = $head['price'];
    $this->label = $head['label'];
    $this->link = $head['link'];
    $this->imageId = $head['image'];
  }

  public function getName(): string {
    return $this->name;
  }

  public function getPrice(): string {
    return $this->price;
  }

  public function getLabel(): string {
    return $this->label;
  }

  public function getLink(): string {
    return $this->link;
  }

  public function getImageSrc(): array | null {
    if (empty($this->imageId)) {
      return null;
    }
    $imageSrc = wp_get_attachment_image_src($this->imageId, 'article-desktop');
    return $imageSrc;
  }
}
