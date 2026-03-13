<?php

declare(strict_types=1);

namespace App\Domain\Product\Service;

use App\Domain\Product\Integration\DTO\Product;
use App\Domain\Product\Message\SyncProductBatchMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SyncProductService implements SyncProductServiceInterface
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @param iterable<Product> $dtos
     */
    public function execute(iterable $dtos): void
    {
        $batch = [];

        foreach ($dtos as $dto) {
            $batch[] = $dto;

            if (count($batch) >= 100) {
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
        $this->messageBus->dispatch(new SyncProductBatchMessage($batch));
    }
}
