<?php

declare(strict_types=1);

namespace App\Domain\Product\Service\ProductSnapshot;

use App\Domain\Product\Integration\DTO\Product;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Generator;
use RuntimeException;

readonly class SnapshotReader
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        #[Target('monolog.logger.product_snapshot')]
        private LoggerInterface $snapshotLogger,
    ) {}

    /**
     * @return Generator<Product>
     */
    public function read(string $path): Generator
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new RuntimeException("Cant open file: $path");
        }

        try {
            while (($line = fgets($handle)) !== false) {
                $line = trim($line);

                if ($line === '') {
                    continue;
                }

                yield $this->makeProductFromJson($line);
            }
        } finally {
            fclose($handle);
        }
    }

    private function makeProductFromJson(string $line): Product
    {
        $product = $this->serializer->deserialize($line, Product::class, 'json');

        $errors = $this->validator->validate($product);

        if ($errors->count() > 0) {
            $errorMessage = 'Product validation failed';

            $this->snapshotLogger->error($errorMessage, [
                'errors' => $errors,
                'json' => $line,
                'product' => $product,
            ]);
            throw new Exception($errorMessage);
        }

        return $product;
    }
}
