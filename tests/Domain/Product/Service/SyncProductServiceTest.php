<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product\Service;

use App\Domain\Product\Integration\DTO\Product as ProductDTO;
use App\Domain\Product\Integration\DTO\ProductMeasurements as ProductMeasurementsDTO;
use App\Domain\Product\Message\SyncProductBatchMessage;
use App\Domain\Product\Service\SyncProductService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

class SyncProductServiceTest extends TestCase
{
    public function testExecute(): void
    {
        $bus = $this->createMock(MessageBusInterface::class);
        $service = new SyncProductService($bus);

        $dtos = [];

        for ($i = 1; $i <= 150; $i++) {
            $dtos[] = $this->createTestDto($i);
        }

        $callCount = 0;
        $bus
            ->expects($this->exactly(2))
            ->method('dispatch')
            ->willReturnCallback(function (SyncProductBatchMessage $message) use (&$callCount) {
                $callCount++;
                $expectedDtosCount = $callCount === 1 ? 100 : 50;

                $this->assertCount($expectedDtosCount, $message->dtos);

                return new Envelope($message);
        });

        $service->execute($dtos);
        $this->assertEquals(2, $callCount);
    }

    private function createTestDto(int $id): ProductDTO
    {
        // TODO найти решение для автогенерации DTO
        return new ProductDTO(
            id: $id,
            name: "Test $id",
            measurements: new ProductMeasurementsDTO(1, 1, 1, 1),
            cost: 100,
            tax: 20,
            version: 1,
            description: null
        );
    }
}
