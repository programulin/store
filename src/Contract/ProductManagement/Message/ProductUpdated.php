<?php

declare(strict_types=1);

namespace App\Contract\ProductManagement\Message;

use App\Contract\ProductManagement\DTO\Product;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class ProductUpdated
{
    public function __construct(
        #[Assert\Valid]
        public Product $product,
    ) {}
}
