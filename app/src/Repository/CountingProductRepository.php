<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\ProductFinderInterface;
use App\Contract\ProductViewCounterInterface;

final class CountingProductRepository implements ProductFinderInterface
{
    public function __construct(
        private readonly ProductFinderInterface $inner,
        private readonly ProductViewCounterInterface $counter,
    ) {
    }

    public function find(string $id): array
    {
        $productData = $this->inner->find($id);
        $this->counter->increment($id);

        return $productData;
    }
}
