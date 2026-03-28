<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Domain\Product\Service\ProductSnapshot\SnapshotDownloader;
use App\Domain\Product\Service\ProductSnapshot\SnapshotReader;
use App\Domain\Product\Service\ProductSync\ProductSyncHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductSyncStartedHandler {
    public function __construct(
        private ProductSyncHandler $productSyncHandler,
        private SnapshotDownloader $snapshotDownloader,
        private SnapshotReader $snapshotReader,
    ) {}

    public function __invoke(): void
    {
        $snapshotPath = $this->snapshotDownloader->execute();

        $productsGenerator = $this->snapshotReader->read($snapshotPath);

        $this->productSyncHandler->execute($productsGenerator);
    }
}
