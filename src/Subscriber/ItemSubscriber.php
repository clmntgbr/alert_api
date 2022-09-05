<?php

namespace App\Subscriber;

use App\Entity\Item;
use App\Entity\User;
use App\Repository\ItemRepository;
use App\Repository\StoreRepository;
use DateTime;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ItemSubscriber implements EventSubscriber
{
    public function __construct(
        private StoreRepository $storeRepository
    ) {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $item = $args->getObject();

        if (!$item instanceof Item) {
            return;
        }

        if ($item->getExpirationDate()->format('Y-m-d') <= (new DateTime('now'))->format('Y-m-d')) {
            $item->setExpirationDate(null);
        }
        
        $store = $this->storeRepository->findOneBy(['isActive' => true]);

        $item->setStore($store);
    }
}