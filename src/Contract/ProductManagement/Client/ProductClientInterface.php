<?php

declare(strict_types=1);

namespace App\Contract\ProductManagement\Client;

use App\Contract\ProductManagement\DTO\Product;

interface ProductClientInterface
{
    /**
     * TODO Выкачивать либо пачками через пагинацию, либо из снапшота с использвоанием итератора
     *
     * @return Product[]
     */
    public function getAllProducts(): iterable;
}
