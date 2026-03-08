<?php

declare(strict_types=1);

namespace App\Domain\Product\Repository;

use App\Domain\Product\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function getById(int $id): ?Product
    {
        /** @var ?Product */
        return $this->find($id);
    }
}
