<?php

namespace App\Subscriber;

use App\Entity\Item;
use App\Entity\Store;
use App\Entity\User;
use App\Repository\StoreRepository;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\Security\Core\Security;

class ItemSubscriber implements EventSubscriber
{
    public function __construct(
        private StoreRepository $storeRepository,
        private Security        $security
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
            Events::preUpdate,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $item = $args->getObject();

        if (!$item instanceof Item) {
            return;
        }

        if (!$this->security->getToken()?->getUser() instanceof User) {
            return;
        }

        $store = $this->storeRepository->findOneBy(['isActive' => true]);

        if ($store instanceof Store) {
            $item->setStore($store);
        }
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $item = $args->getObject();

        if (!$item instanceof Item) {
            return;
        }

        if (!$this->security->getToken()?->getUser() instanceof User) {
            return;
        }
    }
}