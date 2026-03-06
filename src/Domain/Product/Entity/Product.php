<?php

declare(strict_types=1);

namespace App\Domain\Product\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    public int $id;

    #[ORM\Column(length: 255)]
    public string $name;

    #[ORM\Embedded(class: ProductMeasurements::class)]
    public ProductMeasurements $measurements;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    public ?string $description;

    #[ORM\Column]
    public int $cost;

    #[ORM\Column]
    public int $tax;

    #[ORM\Version]
    #[ORM\Column(type: Types::INTEGER)]
    public int $version;

    public function __construct(int $id, string $name, ProductMeasurements $measurements, ?string $description, int $cost, int $tax, int $version)
    {
        $this->id = $id;
        $this->name = $name;
        $this->measurements = $measurements;
        $this->description = $description;
        $this->cost = $cost;
        $this->tax = $tax;
        $this->version = $version;
    }
}
