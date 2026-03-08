<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product\MessageHandler;

use App\Contract\ProductManagement\DTO\Product as ProductDto;
use App\Contract\ProductManagement\DTO\ProductMeasurements as DtoMeasurements;
use App\Contract\ProductManagement\Messages\ProductUpdated;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements;
use App\Domain\Product\Entity\ProductMeasurements as EntityMeasurements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductUpdatedHandlerTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->bus = self::getContainer()->get(MessageBusInterface::class);

        // TODO найти решение для автоматической очистки таблиц
        $this->em->createQuery('DELETE FROM ' . Product::class)->execute();
    }

    public function testUpdatesProductWhenVersionIsHigher(): void
    {
        // TODO найти решение для быстрой генерации сущностей в БД
        $product = new Product(
            id: 1,
            name: 'Old Name',
            measurements: new ProductMeasurements(
                weight: 1,
                height: 1,
                width: 1,
                length: 1,
            ),
            description: 'Old Desc',
            cost: 10,
            tax: 10,
            version: 1,
        );

        $this->em->persist($product);
        $this->em->flush();

        // TODO мб подумать над решением для быстрой генерации DTO
        $dto = new ProductDto(
            id: 1,
            name: 'New Name',
            measurements: new DtoMeasurements(
                weight: 2,
                height: 2,
                width: 2,
                length: 2,
            ),
            cost: 20,
            tax: 20,
            version: 2,
            description: 'New Desc',
        );

        $this->bus->dispatch(new ProductUpdated($dto));

        $this->em->clear();
        $updatedProduct = $this->em->find(Product::class, 1);

        $this->assertSame('New Name', $updatedProduct->name);
        $this->assertSame(2, $updatedProduct->version);
        // TODO подумать как сравнить все поля сразу, мб toArray в оба класса и сравнивать массивы?
    }

    public function testDoesNotUpdateProductWhenVersionIsLowerOrEqual(): void
    {
        $product = new Product(
            id: 2,
            name: 'Current Name',
            measurements: new EntityMeasurements(
                weight: 1,
                height: 1,
                width: 1,
                length: 1,
            ),
            description: 'Current Desc',
            cost: 10,
            tax: 10,
            version: 5,
        );

        $this->em->persist($product);
        $this->em->flush();

        $dto = new ProductDto(
            id: 2,
            name: 'Outdated Name',
            measurements: new DtoMeasurements(
                weight: 2,
                height: 2,
                width: 2,
                length: 2,
            ),
            cost: 20,
            tax: 20,
            version: 4,
            description: 'Outdated Desc',
        );

        $this->bus->dispatch(new ProductUpdated($dto));

        $this->em->clear();
        $existingProduct = $this->em->find(Product::class, 2);

        $this->assertSame('Current Name', $existingProduct->name);
        $this->assertSame(5, $existingProduct->version);
        $this->assertSame('Current Desc', $existingProduct->description);
    }
}
