<?php

namespace App\ApiResource;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetBrands extends AbstractController
{
    public function __construct(
        private readonly ProductRepository $productRepository
    ) {
    }

    public function __invoke(Request $request): array
    {
        return $this->productRepository->findBrands();
    }
}
