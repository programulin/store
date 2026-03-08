<?php

declare(strict_types=1);

namespace App\Contracts\ProductManagement\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductMeasurements
{
    public function __construct(
        #[Assert\PositiveOrZero]
        public int $weight,

        #[Assert\PositiveOrZero]
        public int $height,

        #[Assert\PositiveOrZero]
        public int $width,

        #[Assert\PositiveOrZero]
        public int $length,
    )
    {}
}
