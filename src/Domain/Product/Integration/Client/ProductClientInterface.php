<?php

declare(strict_types=1);

namespace App\Domain\Product\Integration\Client;

interface ProductClientInterface
{
    public function getLatestSnapshotUrl(): string;
}
