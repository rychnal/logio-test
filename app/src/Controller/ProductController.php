<?php

declare(strict_types=1);

namespace App\Controller;

use App\Contract\ProductFinderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products')]
final class ProductController
{
    public function __construct(private readonly ProductFinderInterface $finder)
    {
    }

    #[Route('/{id}', methods: ['GET'])]
    public function detail(string $id): JsonResponse
    {
        return new JsonResponse($this->finder->find($id));
    }
}
