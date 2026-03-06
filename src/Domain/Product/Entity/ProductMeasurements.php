<?php

declare(strict_types=1);

namespace App\Domain\Product\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class ProductMeasurements
{
    public function __construct(
        #[ORM\Column]
        public int $weight,

        #[ORM\Column]
        public int $height,

        #[ORM\Column]
        public int $width,

        #[ORM\Column]
        public int $length,
    ) {}
}
