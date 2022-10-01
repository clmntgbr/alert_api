<?php

namespace App\Api\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\OpenFoodFactApiService;
use App\Utils\GetAttribute;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetProductByEan extends AbstractController
{
    public static string $operationName = 'get_product_by_ean';

    public function __construct(
        private ProductRepository $productRepository,
        private OpenFoodFactApiService $openFoodFactApiService,
        private GetAttribute $getAttribute
    ) {
    }

    public function __invoke(Request $request): Product
    {
        $ean = $this->getAttribute->get('ean', $request);

        $product = $this->productRepository->findOneBy(['ean' => $ean]);

        if ($product instanceof Product) {
            return $product;
        }

        $product = $this->openFoodFactApiService
            ->find($ean)
            ->createProduct();

        return $product;
    }
}
