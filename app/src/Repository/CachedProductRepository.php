<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\ProductFinderInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CachedProductRepository implements ProductFinderInterface
{
    public function __construct(
        private readonly ProductFinderInterface $inner,
        private readonly CacheInterface $cache,
    ) {
    }

    public function find(string $id): array
    {
        return $this->cache->get("product.$id", fn(ItemInterface $item) => $this->inner->find($id));
    }
}
