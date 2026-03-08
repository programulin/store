<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product\MessageHandler;

use App\Contracts\ProductManagement\DTO\Product as ProductDto;
use App\Contracts\ProductManagement\DTO\ProductMeasurements as DtoMeasurements;
use App\Contracts\ProductManagement\Messages\ProductCreated;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductCreatedHandlerTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private MessageBusInterface $bus;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->bus = self::getContainer()->get(MessageBusInterface::class);

        $this->em->createQuery('DELETE FROM ' . Product::class)->execute();
    }

    public function testCreatesProductSuccess(): void
    {
        $dto = new ProductDto(
            id: 100,
            name: 'New Product',
            measurements: new DtoMeasurements(
                weight: 10,
                height: 10,
                width: 10,
                length: 10
            ),
            cost: 500,
            tax: 100,
            version: 1,
            description: 'New Product Description',
        );

        $this->bus->dispatch(new ProductCreated($dto));

        $this->em->clear();
        $createdProduct = $this->em->find(Product::class, 100);

        $this->assertNotNull($createdProduct);
        $this->assertSame('New Product', $createdProduct->name);
        $this->assertSame(500, $createdProduct->cost);
        $this->assertSame(1, $createdProduct->version);
    }

    public function testDoesNotCreateIfAlreadyExists(): void
    {
        $existing = new Product(
            id: 100,
            name: 'Existing',
            measurements: new ProductMeasurements(
                weight: 1,
                height: 1,
                width: 1,
                length: 1,
            ),
            description: null,
            cost: 1,
            tax: 1,
            version: 1
        );
        $this->em->persist($existing);
        $this->em->flush();

        $dto = new ProductDto(
            id: 100,
            name: 'Attempt to overwrite',
            measurements: new DtoMeasurements(
                weight: 10,
                height: 10,
                width: 10,
                length: 10,
            ),
            cost: 999,
            tax: 999,
            version: 10,
            description: 'This should not happen',
        );

        $this->bus->dispatch(new ProductCreated($dto));

        $this->em->clear();
        $productInDb = $this->em->find(Product::class, 100);

        $this->assertSame('Existing', $productInDb->name);
        $this->assertSame(1, $productInDb->cost);
    }
}
