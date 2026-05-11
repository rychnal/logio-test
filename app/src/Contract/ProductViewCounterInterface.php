<?php

declare(strict_types=1);

namespace App\Contract;

interface ProductViewCounterInterface
{
    public function increment(string $id): void;
}
