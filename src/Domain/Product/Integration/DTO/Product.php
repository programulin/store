<?php

declare(strict_types=1);

namespace App\Domain\Product\Integration\DTO;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class Product
{
    public function __construct(
        #[Assert\Positive]
        public int $id,

        #[Assert\NotBlank]
        public string $name,

        #[Assert\Valid]
        public ProductMeasurements $measurements,

        #[Assert\PositiveOrZero]
        public int $cost,

        #[Assert\PositiveOrZero]
        public int $tax,

        #[Assert\PositiveOrZero]
        public int $version,

        public ?string $description,
    ) {}
}
