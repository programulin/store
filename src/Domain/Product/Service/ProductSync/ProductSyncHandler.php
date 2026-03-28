<?php

declare(strict_types=1);

namespace App\Domain\Product\Service\ProductSync;

use App\Domain\Product\Integration\DTO\Product;
use App\Domain\Product\Message\ProductSyncBatch;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final readonly class ProductSyncHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @param iterable<Product> $productDtos
     */
    public function execute(iterable $productDtos): void
    {
        // TODO транзакция?

        $batch = [];

        foreach ($productDtos as $dto) {
            $batch[] = $dto;

            if (count($batch) >= ProductSyncBatch::MAX_BATCH_SIZE) {
                $this->dispatchBatch($batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->dispatchBatch($batch);
        }
    }

    /**
     * @param Product[] $batch
     */
    private function dispatchBatch(array $batch): void
    {
        $this->messageBus->dispatch(new ProductSyncBatch($batch));
    }
}
