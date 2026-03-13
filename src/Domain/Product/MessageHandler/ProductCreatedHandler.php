<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Contract\ProductManagement\Message\ProductCreated;
use App\Domain\Product\Service\SyncProductService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductCreatedHandler
{
    public function __construct(
        private SyncProductService $syncProductService,
    )
    {}

    public function __invoke(ProductCreated $event): void
    {
        $this->syncProductService->execute([$event->product]);
    }
}
