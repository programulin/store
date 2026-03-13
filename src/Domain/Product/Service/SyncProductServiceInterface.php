<?php

declare(strict_types=1);

namespace App\Domain\Product\Service;

use App\Domain\Product\Integration\DTO\Product;

interface SyncProductServiceInterface
{
    /**
     * @param iterable<Product> $dtos
     */
    public function execute(iterable $dtos): void;
}
