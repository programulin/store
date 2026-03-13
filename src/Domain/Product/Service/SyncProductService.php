<?php

declare(strict_types=1);

namespace App\Domain\Product\Service;

use App\Contract\ProductManagement\DTO\Product as ProductDTO;
use App\Domain\Product\Message\SyncProductBatchMessage;
use Symfony\Component\Messenger\MessageBusInterface;

final readonly class SyncProductService
{
    public function __construct(
        private MessageBusInterface $messageBus,
    ) {}

    /**
     * @param iterable<ProductDTO> $dtos
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
     * @param ProductDTO[] $batch
     */
    private function dispatchBatch(array $batch): void
    {
        $this->messageBus->dispatch(new SyncProductBatchMessage($batch));
    }
}
