<?php

namespace App\Api\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\OpenFoodFactApiService;
use App\Service\PostProductByEanService;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Prod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetProductByEan extends AbstractController
{
    public static $operationName = 'get_product_by_ean';

    public function __construct(
        private ProductRepository $productRepository,
        private OpenFoodFactApiService $openFoodFactApiService
    ){
        
    }

    public function __invoke(Request $request): Product
    {
        $product = $this->productRepository->findOneBy(['ean' => $request->attributes->get('ean')]);

        if ($product instanceof Product) {
            return $product;
        }

        $product = $this->openFoodFactApiService
            ->find($request->attributes->get('ean'))
            ->createProduct();
        
        return $product;
    }
}