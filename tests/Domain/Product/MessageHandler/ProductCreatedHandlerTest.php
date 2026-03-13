<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product\MessageHandler;

use App\Domain\Product\Integration\DTO\Product as ProductDTO;
use App\Domain\Product\Integration\DTO\ProductMeasurements;
use App\Domain\Product\Integration\Message\ProductCreated;
use App\Domain\Product\Service\SyncProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

final class ProductCreatedHandlerTest extends KernelTestCase
{
    public function testHandler(): void
    {
        // TODO Найти способ генерировать DTO в тестах автоматически
        $dto = new ProductDTO(
            id: 1,
            name: 'Bus Test',
            measurements: new ProductMeasurements(
                weight: 1,
                height: 1,
                width: 1,
                length: 1,
            ),
            cost: 10,
            tax: 2,
            version: 1,
            description: null,
        );

        $event = new ProductCreated($dto);

        $syncServiceMock = $this->createMock(SyncProductServiceInterface::class);

        $syncServiceMock->expects($this->once())
            ->method('execute')
            ->with([$dto]);

        $container = self::getContainer();

        $container->set(SyncProductServiceInterface::class, $syncServiceMock);

        $container->get(MessageBusInterface::class)
            ->dispatch($event);
    }
}
