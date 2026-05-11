<?php

declare(strict_types=1);

namespace App\Contract\Driver;

interface MysqlDriverInterface
{
    public function findProduct(string $id): array;
}
