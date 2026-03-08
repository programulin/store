<?php

declare(strict_types=1);

namespace App\Tests\Domain\Product\Service;

use App\Contract\ProductManagement\Client\ProductClientInterface;
use App\Contract\ProductManagement\DTO\Product as ProductDto;
use App\Contract\ProductManagement\DTO\ProductMeasurements as DtoMeasurements;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements as EntityMeasurements;
use App\Domain\Product\Repository\ProductRepository;
use App\Domain\Product\Service\ProductSyncService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductSyncServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private ProductRepository $repository;
    private ProductClientInterface&MockObject $clientMock;
    private ProductSyncService $service;

    protected function setUp(): void
    {
        self::bootKernel();
        $container = self::getContainer();

        $this->em = $container->get(EntityManagerInterface::class);
        $this->repository = $container->get(ProductRepository::class);
        $this->clientMock = $this->createMock(ProductClientInterface::class);

        $this->service = new ProductSyncService(
            productClient: $this->clientMock,
            productRepository: $this->repository,
            entityManager: $this->em
        );

        $this->em->createQuery('DELETE FROM ' . Product::class)->execute();
    }

    public function testExecuteCreatesNewProduct(): void
    {
        $dto = new ProductDto(
            id: 10,
            name: 'New Item',
            measurements: new DtoMeasurements(1, 2, 3, 4),
            cost: 100,
            tax: 20,
            version: 1,
            description: 'New Desc'
        );
        $this->clientMock
            ->expects($this->once())
            ->method('getAllProducts')
            ->willReturn([$dto]);

        $this->service->execute();

        $this->em->clear();
        $product = $this->em->find(Product::class, 10);

        $this->assertNotNull($product);
        $this->assertSame('New Item', $product->name);
        $this->assertSame(100, $product->cost);
        $this->assertSame(1, $product->version);
    }

    public function testExecuteUpdatesExistingProduct(): void
    {
        $existing = new Product(
            id: 20,
            name: 'Old Name',
            measurements: new EntityMeasurements(10, 10, 10, 10),
            description: 'Old Desc',
            cost: 50,
            tax: 5,
            version: 5
        );
        $this->em->persist($existing);
        $this->em->flush();

        $dto = new ProductDto(
            id: 20,
            name: 'Updated Name',
            measurements: new DtoMeasurements(20, 20, 20, 20),
            cost: 99,
            tax: 9,
            version: 6,
            description: 'Updated Desc'
        );
        $this->clientMock
            ->expects($this->once())
            ->method('getAllProducts')
            ->willReturn([$dto]);

        $this->service->execute();

        $this->em->clear();
        $product = $this->em->find(Product::class, 20);

        $this->assertNotNull($product);
        $this->assertSame('Updated Name', $product->name);
        $this->assertSame(99, $product->cost);
        $this->assertSame(6, $product->version);
        $this->assertSame('Updated Desc', $product->description);
    }
}
