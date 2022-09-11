<?php

namespace App\Api\Controller;

use App\Entity\Product;
use App\Entity\Store;
use App\Repository\ProductRepository;
use App\Repository\StoreRepository;
use App\Service\OpenFoodFactApiService;
use App\Service\PostProductByEanService;
use Hautelook\AliceBundle\Functional\TestBundle\Entity\Prod;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetActiveStore extends AbstractController
{
    public static $operationName = 'get_active_store';

    public function __construct(
        private StoreRepository $storeRepository,
        private OpenFoodFactApiService $openFoodFactApiService
    ){
        
    }

    public function __invoke(Request $request): ?Store
    {
        $store = $this->storeRepository->findOneBy(['isActive' => true]);

        if ($store instanceof Store) {
            return $store;
        }

        return null;
    }
}