<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Domain\Product\Message\ProductSyncBatch;
use App\Domain\Product\Service\ProductSync\ProductSyncBatchHandler as BatchHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductSyncBatchHandler {
    public function __construct(
        private BatchHandler $batchHandler,
    ) {}

    public function __invoke(ProductSyncBatch $message): void
    {
        $this->batchHandler->execute($message->dtos);
    }
}
