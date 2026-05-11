<?php

declare(strict_types=1);

namespace App\Repository;

use App\Contract\Driver\MysqlDriverInterface;
use App\Contract\ProductFinderInterface;

final class MysqlProductRepository implements ProductFinderInterface
{
    public function __construct(private readonly MysqlDriverInterface $driver)
    {
    }

    public function find(string $id): array
    {
        return $this->driver->findProduct($id);
    }
}
