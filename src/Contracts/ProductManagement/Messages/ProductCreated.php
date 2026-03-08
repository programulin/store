<?php

declare(strict_types=1);

namespace App\Contracts\ProductManagement\Messages;

use App\Contracts\ProductManagement\DTO\Product;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductCreated
{
    public function __construct(
        #[Assert\Valid]
        public Product $product,
    ) {}
}
