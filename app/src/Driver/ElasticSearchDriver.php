<?php

declare(strict_types=1);

namespace App\Driver;

use App\Contract\Driver\ElasticSearchDriverInterface;

/**
 * Stub — real implementation is provided by the framework.
 */
final class ElasticSearchDriver implements ElasticSearchDriverInterface
{
    public function findById(string $id): array
    {
        return [
            'id'    => $id,
            'name'  => "Product $id",
            'price' => 99.99,
            'source' => 'elasticsearch',
        ];
    }
}
