<?php

declare(strict_types=1);

namespace App\Contract;

interface ProductFinderInterface
{
    public function find(string $id): array;
}
