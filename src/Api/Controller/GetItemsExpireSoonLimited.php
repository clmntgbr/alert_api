<?php

namespace App\Api\Controller;

use App\Entity\Item;
use App\Entity\Product;
use App\Repository\ItemRepository;
use App\Repository\ProductRepository;
use App\Service\OpenFoodFactApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

#[AsController]
class GetItemsExpireSoonLimited extends AbstractController
{
    public static $operationName = 'get_items_by_expiration_date_soon_limited';

    public function __construct(
        private ItemRepository $itemRepository,
        private Security $security
    ){
        
    }

    /** @return Item[] */
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $this->security->getToken()?->getUser();
        
        return $this->itemRepository->findItemsExpireSoon(5, $user->getActiveStore());
    }
}