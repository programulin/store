<?php

declare(strict_types=1);

namespace App\Domain\Product\Integration\Message;

use App\Domain\Product\Integration\DTO\Product;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductCreated
{
    public function __construct(
        #[Assert\Valid]
        public Product $product,
    ) {}
}
