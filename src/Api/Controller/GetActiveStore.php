<?php

namespace App\Api\Controller;

use App\Entity\Store;
use App\Repository\StoreRepository;
use App\Service\PostProductByEanService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class GetActiveStore extends AbstractController
{
    public static string $operationName = 'get_active_store';

    public function __construct(
        private StoreRepository        $storeRepository
    )
    {

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