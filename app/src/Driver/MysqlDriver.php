<?php

declare(strict_types=1);

namespace App\Driver;

use App\Contract\Driver\MysqlDriverInterface;

/**
 * Stub — real implementation is provided by the framework.
 */
final class MysqlDriver implements MysqlDriverInterface
{
    public function findProduct(string $id): array
    {
        return [
            'id'    => $id,
            'name'  => "Product $id",
            'price' => 99.99,
            'source' => 'mysql',
        ];
    }
}
