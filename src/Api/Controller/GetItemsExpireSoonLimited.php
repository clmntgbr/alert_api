<?php

namespace App\Api\Controller;

use App\Entity\Item;
use App\Entity\User;
use App\Repository\ItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Security\Core\Security;

#[AsController]
class GetItemsExpireSoonLimited extends AbstractController
{
    public static string $operationName = 'get_items_expire_soon_limited';

    public function __construct(
        private ItemRepository $itemRepository,
        private Security       $security
    )
    {

    }

    /** @return Item[] */
    public function __invoke(Request $request)
    {
        /** @var User $user */
        $user = $this->security->getToken()?->getUser();
        return $this->itemRepository->findItemsExpireSoon(5, $user->getActiveStore() ?? null);
    }
}