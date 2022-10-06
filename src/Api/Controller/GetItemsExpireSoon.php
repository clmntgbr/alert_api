<?php

namespace App\Api\Controller;

use App\Entity\Item;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\StoreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class GetItemsExpireSoon extends AbstractController
{
    public static string $operationName = 'get_items_expire_soon';

    public function __construct(
        private ItemRepository $itemRepository,
        private StoreRepository $storeRepository,
        private Security $security
    ) {
    }

    /** @return Item[] */
    public function __invoke(Request $request)
    {
        $limit = $request->query->get('limit');
        $index = $request->query->get('index');
        $store_id = $this->getStoreId($request->query->get('store_isActive'), $request->query->get('store_id'));

        $items = $this->itemRepository->findItemsExpireSoon($limit + $index, $store_id);

        return array_slice($items, $index, $limit + $index);
    }

    private function getStoreId(?string $store_isActive, ?string $store_id) 
    {
        if ($store_isActive === null) {
            return $store_id;
        }

        if ($store_id === null) {
            $store = $this->storeRepository->findOneBy(['isActive' => true]);
            return $store->getId();
        }

        return 0;
    }
}
