<?php

declare(strict_types=1);

namespace App\Domain\Product\Mapper;

use App\Domain\Product\Integration\DTO\Product as ProductDTO;
use App\Domain\Product\Integration\DTO\ProductMeasurements as ProductMeasurementsDTO;
use App\Domain\Product\Entity\Product;
use App\Domain\Product\Entity\ProductMeasurements;

final class ProductMapper
{
    public function mapDtoToEntity(ProductDTO $dto, ?Product $product = null): Product
    {
        $measurements = new ProductMeasurements(
            $dto->measurements->weight,
            $dto->measurements->height,
            $dto->measurements->width,
            $dto->measurements->length,
        );

        if ($product === null) {
            return new Product(
                id: $dto->id,
                name: $dto->name,
                measurements: $measurements,
                description: $dto->description,
                cost: $dto->cost,
                tax: $dto->tax,
                version: $dto->version,
            );
        }

        $product->name = $dto->name;
        $product->description = $dto->description;
        $product->cost = $dto->cost;
        $product->tax = $dto->tax;
        $product->version = $dto->version;
        $product->measurements = $measurements;

        return $product;
    }

    public function mapEntityToDto(Product $product): ProductDTO
    {
        $measurements = new ProductMeasurementsDto(
            $product->measurements->weight,
            $product->measurements->height,
            $product->measurements->width,
            $product->measurements->length,
        );

        return new ProductDTO(
            id: $product->id,
            name: $product->name,
            measurements: $measurements,
            cost: $product->cost,
            tax: $product->tax,
            version: $product->version,
            description: $product->description,
        );
    }
}
