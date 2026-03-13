<?php

declare(strict_types=1);

namespace App\Domain\Product\Integration\Client;

/**
 * TODO Без этой фэйковой реализации тесты падают из-за попытки создать инстанс команды SyncProductsCommand
 */
final readonly class FakeClient implements ProductClientInterface
{
    public function getAllProducts(): iterable {
        return [];
    }
}
