<?php

declare(strict_types=1);

namespace App\Contract\Driver;

interface ElasticSearchDriverInterface
{
    public function findById(string $id): array;
}
