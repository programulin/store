<?php

declare(strict_types=1);

namespace App\Domain\Product\Message;

use App\Domain\Product\Integration\DTO\Product;
use Exception;

readonly class ProductSyncBatch
{
    public const int MAX_BATCH_SIZE = 1000;

    public function __construct(
        /** @var Product[] */
        public array $dtos,
    ) {
        $dtosCount = count($this->dtos);

        if ($dtosCount >= self::MAX_BATCH_SIZE) {
            throw new Exception('Too many dtos: ' . $dtosCount);
        }

    }
}
