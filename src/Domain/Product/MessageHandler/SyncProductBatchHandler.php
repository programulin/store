<?php

declare(strict_types=1);

namespace App\Domain\Product\MessageHandler;

use App\Contract\ProductManagement\DTO\Product as ProductDTO;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Mapper\ProductMapper;
use App\Domain\Product\Message\SyncProductBatchMessage;
use App\Domain\Product\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class SyncProductBatchHandler {
    public function __construct(
        private ProductRepository $productRepository,
        private EntityManagerInterface $entityManager,
        private ProductMapper $productMapper,
    ) {}

    public function __invoke(SyncProductBatchMessage $message): void
    {
        $this->entityManager->wrapInTransaction(function() use ($message) {
            $productsMap = $this->getExistingProductsMap($message);

            foreach ($message->dtos as $dto) {
                isset($productsMap[$dto->id])
                    ? $this->handleUpdate($productsMap[$dto->id], $dto)
                    : $this->handleCreate($dto);
            }

            $this->entityManager->flush();
        });

        $this->entityManager->clear();
    }

    private function handleCreate(ProductDTO $dto): void
    {
        $product = $this->productMapper->mapDtoToEntity($dto);
        $this->entityManager->persist($product);

        /**
         * TODO нет защиты от попытки создать один и тот же товар разными воркерами
         * AI говорит, в Doctrine нет возможности сделать ON CONFLICT (id) DO NOTHING внутри flush()
         * Поэтому можно либо запускать нативный sql-запрос,
         * либо мб спасёт игнор исключения UniqueConstraintViolationException,
         * либо забиваем. Если упадёт - перезапустим вручную.
         */
    }

    private function handleUpdate(Product $product, ProductDTO $dto): void
    {
        if ($product->version >= $dto->version) {
            return;
        }

        $this->productMapper->mapDtoToEntity($dto, $product);
    }

    /**
     * @return array<int, Product>
     */
    private function getExistingProductsMap(SyncProductBatchMessage $message): array
    {
        $ids = array_map(fn($dto) => $dto->id, $message->dtos);

        $existingProducts = $this->productRepository->getProductsByIdsForUpdate($ids);

        $map = [];

        foreach ($existingProducts as $product) {
            $map[$product->id] = $product;
        }

        return $map;
    }
}
