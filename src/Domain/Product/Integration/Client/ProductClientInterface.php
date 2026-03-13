<?php

declare(strict_types=1);

namespace App\Domain\Product\Integration\Client;

use App\Domain\Product\Integration\DTO\Product;

interface ProductClientInterface
{
    /**
     * TODO Выкачивать либо пачками через пагинацию, либо из снапшота с использвоанием итератора
     *
     * @return Product[]
     */
    public function getAllProducts(): iterable;
}
