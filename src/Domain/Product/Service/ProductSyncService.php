<?php

declare(strict_types=1);

namespace App\Domain\Product\Service;

use App\Contract\ProductManagement\Client\ProductClientInterface;
use App\Contract\ProductManagement\DTO\Product as ProductDTO;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements;
use App\Domain\Product\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;

final readonly class ProductSyncService
{
    public function __construct(
        private ProductClientInterface $productClient,
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
    ) {}

    public function execute(): void
    {
        $productsDto = $this->productClient->getAllProducts();
        $batchSize = 100;
        $i = 0;

        foreach ($productsDto as $dto) {
            $product = $this->productRepository->getById($dto->id);

            $product === null
                ? $this->handleCreate($dto)
                : $this->handleUpdate($product, $dto);

            if ($i >= $batchSize) {
                $this->entityManager->flush();
                $this->entityManager->clear();
                $i = 0;
            } else {
                $i++;
            }
        }

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    private function handleCreate(ProductDTO $dto): void
    {
        $product = new Product(
            id: $dto->id,
            name: $dto->name,
            measurements: new ProductMeasurements(
                $dto->measurements->weight,
                $dto->measurements->height,
                $dto->measurements->width,
                $dto->measurements->length,
            ),
            description: $dto->description,
            cost: $dto->cost,
            tax: $dto->tax,
            version: $dto->version,
        );

        $this->entityManager->persist($product);
    }

    private function handleUpdate(Product $product, ProductDTO $dto): void
    {
        $product->name = $dto->name;
        $product->description = $dto->description;
        $product->cost = $dto->cost;
        $product->tax = $dto->tax;
        $product->version = $dto->version;
        $product->measurements = new ProductMeasurements(
            $dto->measurements->weight,
            $dto->measurements->height,
            $dto->measurements->width,
            $dto->measurements->length,
        );
    }
}
