<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Contract\ProductManagement\Messages\ProductUpdated;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements;
use App\Domain\Product\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductUpdatedHandler
{
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
    )
    {}

    public function __invoke(ProductUpdated $event): void
    {
        $dto = $event->product;

        $product = $this->getProductById($dto->id);

        if ($dto->version <= $product->version) {
            // TODO мб логировать, хотя возможно лучше логировать все сообщения из кафки
            return;
        }

        $product->name = $dto->name;
        $product->description = $dto->description;
        $product->cost = $dto->cost;
        $product->tax = $dto->tax;
        $product->version = $dto->version;
        $product->measurements = new ProductMeasurements(
            weight: $dto->measurements->weight,
            height: $dto->measurements->height,
            width: $dto->measurements->width,
            length: $dto->measurements->length,
        );

        $this->entityManager->flush();
    }

    private function getProductById(int $id): Product
    {
        $product = $this->productRepository->getById($id);

        if ($product === null) {
            throw new \RuntimeException("Product with id $id not found");
        }

        return $product;
    }
}
