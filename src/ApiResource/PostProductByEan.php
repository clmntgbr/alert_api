<?php

namespace App\ApiResource;

use App\Entity\Product;
use App\Service\PostProductByEanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class PostProductByEan extends AbstractController
{
    public function __construct(
        private readonly PostProductByEanService $productByEanService
    ) {
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, Product $data): Product
    {
        /** @var Product $product */
        $product = $request->attributes->get('data');

        if ($product instanceof Product) {
            return $this->productByEanService->find($product->getEan(), $product->getGeography());
        }

        throw new \Exception('Ean should\'nt be null.', 404);
    }
}
