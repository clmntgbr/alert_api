<?php

namespace App\Subscriber;

use App\Entity\Store;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Events;

class StoreSubscriber implements EventSubscriber
{
    public function __construct(
        private int $maxItemPerStore
    )
    {
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::prePersist,
        ];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $store = $args->getObject();

        if (!$store instanceof Store) {
            return;
        }

        $store->setMaxItemPerStore($this->maxItemPerStore);
    }
}