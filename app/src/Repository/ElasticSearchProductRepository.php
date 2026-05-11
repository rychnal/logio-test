<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\Driver\ElasticSearchDriverInterface;
use App\Contract\ProductFinderInterface;

final class ElasticSearchProductRepository implements ProductFinderInterface
{
    public function __construct(private readonly ElasticSearchDriverInterface $driver)
    {
    }

    public function find(string $id): array
    {
        return $this->driver->findById($id);
    }
}
