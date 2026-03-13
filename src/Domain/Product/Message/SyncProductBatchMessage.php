<?php

declare(strict_types=1);

namespace App\Domain\Product\Message;

use App\Domain\Product\Integration\DTO\Product;

final readonly class SyncProductBatchMessage {
    public function __construct(
        /** @var Product[] */
        public array $dtos,
    ) {}
}
