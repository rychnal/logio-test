<?php

declare(strict_types=1);

namespace App\Counter;

use App\Contract\ProductViewCounterInterface;

final class FileProductViewCounter implements ProductViewCounterInterface
{
    public function __construct(private readonly string $counterFilePath)
    {
    }

    public function increment(string $id): void
    {
        $directory = dirname($this->counterFilePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $fileHandle = fopen($this->counterFilePath, 'c+');
        flock($fileHandle, LOCK_EX);

        $counts = json_decode(stream_get_contents($fileHandle), true) ?? [];
        $counts[$id] = ($counts[$id] ?? 0) + 1;

        ftruncate($fileHandle, 0);
        rewind($fileHandle);
        fwrite($fileHandle, json_encode($counts));

        flock($fileHandle, LOCK_UN);
        fclose($fileHandle);
    }
}
