<?php

declare(strict_types=1);

namespace App\Domain\Product\Service\ProductSync;

use App\Domain\Product\Message\ProductSyncStarted;
use Symfony\Component\Messenger\MessageBusInterface;
use Throwable;

readonly class ProductSyncStarter
{
    public function __construct(
        private MessageBusInterface $messageBus,
    )
    {}

    public function execute(): void
    {
        $this->messageBus->dispatch(new ProductSyncStarted());
    }
}
