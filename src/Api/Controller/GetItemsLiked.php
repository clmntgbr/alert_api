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
class GetItemsLiked extends AbstractController
{
    public static string $operationName = 'get_items_liked';

    public function __construct(
        private ItemRepository $itemRepository,
        private Security $security
    ) {
    }

    /** @return Item[] */
    public function __invoke(Request $request)
    {
        $limit = $request->query->get('limit');
        $index = $request->query->get('index');

        /** @var User $user */
        $user = $this->security->getToken()?->getUser();

        $items = $this->itemRepository->findItemsLiked($limit + $index, $user->getActiveStore());

        return array_slice($items, $index, $limit + $index);
    }
}
