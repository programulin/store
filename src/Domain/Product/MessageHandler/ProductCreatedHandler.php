<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Contract\ProductManagement\Messages\ProductCreated;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements;
use App\Domain\Product\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductCreatedHandler
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(ProductCreated $event): void
    {
        $dto = $event->product;

        if ($this->isProductExists($dto->id)) {
            return;
        }

        $product = new Product(
            id: $dto->id,
            name: $dto->name,
            measurements: new ProductMeasurements(
                weight: $dto->measurements->weight,
                height: $dto->measurements->height,
                width: $dto->measurements->width,
                length: $dto->measurements->length,
            ),
            description: $dto->description,
            cost: $dto->cost,
            tax: $dto->tax,
            version: $dto->version,
        );

        $this->entityManager->persist($product);
        $this->entityManager->flush();
    }

    private function isProductExists(int $id): bool
    {
        return $this->productRepository->getById($id) !== null;
    }
}
