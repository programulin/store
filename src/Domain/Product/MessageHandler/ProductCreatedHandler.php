<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Domain\Product\Integration\Message\ProductCreated;
use App\Domain\Product\Service\SyncProductServiceInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductCreatedHandler
{
    public function __construct(
        private SyncProductServiceInterface $syncProductService,
    )
    {}

    public function __invoke(ProductCreated $event): void
    {
        $this->syncProductService->execute([$event->product]);
    }
}
