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
class GetItemsExpired extends AbstractController
{
    public static string $operationName = 'get_items_expired';

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
        return $this->itemRepository->findItemsExpired(100, $user->getActiveStore());
    }
}